<?php

namespace Lova;
/*
 * Plugin Name: Lova Payment Gateway
 * Description: Incredible fast and user friendly payment method for fiat and cryptocurrencies created by Lova.
 * Author: Digital Money Transfer
 * Author URI: https://lova.ba
 * Version: 1.2.1
 * Text Domain: lova-payment-gateway
 */

class Main {
	private static Main $instance;
	public static string $api_url = 'https://account.bcx.ba/api/v1';

	private function __construct() {
		add_filter( 'woocommerce_payment_gateways', [ $this, 'register_gateway_class' ] );
		add_action( 'plugins_loaded', [ $this, 'include_lova_class' ] );
	}

	public function register_gateway_class( $gateways ) {
		$gateways[] = 'WC_Gateway_Lova';

		return $gateways;
	}

	public function include_lova_class() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', [ $this, 'woocommerceMissingWcNotice' ] );

			return;
		}
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ], 10 );
		add_action( 'admin_menu', [ $this, 'lova_plugin_menu' ] );
		require_once 'includes/class-wc-gateway-lova.php';
	}

	public function enqueue_scripts( $hook ) {
		if ( ! empty( $_GET['page'] ) && $_GET['page'] === 'lova-payment' ) {
			wp_enqueue_script( 'lova-admin-scripts', plugin_dir_url( __FILE__ ) . 'assets/js/admin-scripts.js', ['jquery'] );
		}
		if ( $hook != 'woocommerce_page_wc-settings' || ( empty( $_GET['section'] ) || $_GET['section'] != 'lova' ) ) {
			return;
		}
		wp_enqueue_script( 'lova-admin-scripts', plugin_dir_url( __FILE__ ) . 'assets/js/admin-scripts.js', ['jquery'] );
	}

	public function woocommerceMissingWcNotice() {
		echo '<div class="error"><p><strong>' . sprintf( 'Lova payment gateway requires WooCommerce to be installed and active. You can download %s here.', '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
	}

	public static function getInstance(): Main {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function lova_plugin_menu() {
		add_menu_page( 'Lova Payment', 'Lova Payment', 'manage_options', "lova-payment", [
			$this,
			"lova_transactions_page"
		], plugin_dir_url( __FILE__ ) . 'assets/img/lova-icon.png' );
	}

	public function getAllTransaction( $page = null ) {
		$payment_gateway    = WC()->payment_gateways->payment_gateways()['lova'];
		$test_mode          = 'yes' === $payment_gateway->get_option( 'test_mode' );
		$email              = $payment_gateway->get_option( 'email' );
		$password           = $payment_gateway->get_option( 'password' );
		$api_key            = $test_mode ? $payment_gateway->get_option( 'test_api_key' ) : $payment_gateway->get_option( 'api_key' );
		$api_callback_token = $test_mode ? $payment_gateway->get_option( 'test_api_callback_token' ) : $payment_gateway->get_option( 'api_callback_token' );
		$url                = self::$api_url . '/webshop/login';

		$response = wp_remote_post( $url, [
			'headers' => [
				'Accept'         => 'application/json',
				'X-localization' => 'en',
				$api_key         => $api_callback_token,
			],
			'body'    => [
				'email'    => $email,
				'password' => $password,
			],
		] );

		if ( ! is_wp_error( $response ) ) {
			$body = json_decode( $response['body'], true );

			if ( $body['success'] ) {
				$bearer_token = $body['token'];
			} else {
				return [
					'result'   => 'failure',
					'redirect' => false,
					'message'  => __( 'API access error', 'lova-payment-gateway' ),
				];
			}
		} else {
			return [
				'result'   => 'failure',
				'redirect' => false,
				'message'  => __( 'API connection error', 'lova-payment-gateway' ),
			];
		}

		$headers = [ 'Authorization' => 'Bearer ' . $bearer_token ];

		if ( $page == null ) {
			$url = self::$api_url . '/fund/request';
		} else {
			$url = $page;
		}

		$response = wp_remote_post( $url, [
			'method'      => 'GET',
			'timeout'     => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking'    => true,
			'headers'     => $headers
		] );

		$response_body = json_decode( wp_remote_retrieve_body( $response ), true );

		return $response_body;
	}

	public function lova_transactions_page() {
		$transactionsList = $this->getAllTransaction();
		?>
        <div style="padding: 15px; display: flex; align-items: center;">
            <div style="margin-right: 15px;">
                <h3><?php echo __( 'Total payment', 'lova-payment-gateway' ); ?>: <span
                            id="total_amount"></span></h3>
            </div>
            <div style="display: flex; align-items: center;">
                <h3><?php echo __( 'Filters', 'lova-payment-gateway' ); ?>:</h3>
                <div style="margin-left: 10px; margin-right: 20px;">
                    <select name="status" class="status_s">
                        <option value="" default>Status</option>
                        <option value="cancel">Cancel</option>
                        <option value="approve">Approve</option>
                    </select>
                </div>
                <div>
                    <input type="date" name="date_s" data-date-format="DD MMMM YYYY" class="date_s"/> -
                    <input type="date" name="date_e" data-date-format="DD MMMM YYYY" class="date_e"/>
                </div>
            </div>
        </div>
        <table class="wp-list-table widefat fixed striped table-view-list posts" id="transaction-list">
            <thead>
            <tr>
				<th>Id</th>
                <th>Kupac</th>
                <th>Iznos</th>
                <th>Provizija</th>
                <!-- <th>Fee</th> -->
                <!-- <th>Base Fee</th> -->
                <th>Uplaćeno</th>
                <!-- <th>Fromcurrency</th> -->
                <th>Status</th>
                <th>Id Transakcije</th>
                <th>Datum</th>
                <!-- <th>Is expired</th> -->
                <th>Vrsta</th>
            </tr>
            </thead>

            <tbody id="the-list" class="list">
			<?php
			if ( ! empty( $transactionsList ) ) {
				foreach ( $transactionsList['data'] as $item ) {
					echo '<tr>';
					echo '<td class="id">' . esc_html( $item['id'] ) . '</td>';
					echo '<td class="to">' . esc_html( $item['to'] ) . '</td>';
					echo '<td class="amount">' . esc_html( $item['amount'] ) . '</td>';
					echo '<td class="fee_total">' . esc_html( $item['fee_total'] ) . '</td>';
					// echo '<td class="fee">' . esc_html( $item['fee'] ) . '</td>';
					// echo '<td>'.$item['base_fee'].'</td>';
					echo '<td class="total_amount">' . esc_html( $item['total_amount'] ) . '</td>';
					// echo '<td>'.$item['fromcurrency'].'</td>';
					echo '<td class="status">' . esc_html( $item['status'] ) . '</td>';
					echo '<td class="transaction_id">' . esc_html( $item['transaction_id'] ) . '</td>';
					echo '<td class="date">' . esc_html( $item['date'] ) . '</td>';
					// echo '<td>'.$item['is_expired'].'</td>';
					echo '<td class="mode">' . esc_html( $item['mode'] ) . '</td>';
					echo '</tr>';
				}
			} else {
				echo '<tr>';
				echo '<td colspan="10">';
				echo __( 'Transactions not found', 'lova-payment-gateway' );
				echo '</td>';
				echo '</tr>';
			}
			?>
            </tr>
            </tbody>

            <tfoot>
            <tr>
				<th>Id</th>
                <th>Kupac</th>
                <th>Iznos</th>
                <th>Provizija</th>
                <!-- <th>Fee</th> -->
                <!-- <th>Base Fee</th> -->
                <th>Uplaćeno</th>
                <!-- <th>Fromcurrency</th> -->
                <th>Status</th>
                <th>Id Transakcije</th>
                <th>Datum</th>
                <!-- <th>Is expired</th> -->
                <th>Vrsta</th>
            </tr>
            </tfoot>

        </table>
		<?php
	}

}

$GLOBALS['Lova\Main'] = Main::getInstance();
