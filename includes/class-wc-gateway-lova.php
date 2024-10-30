<?php

use Lova\Main;

class WC_Gateway_Lova extends WC_Payment_Gateway {
	private bool $test_mode;
	private string $email;
	private string $password;
	private string $api_key;
	private string $api_callback_token;

	public function __construct() {
		$this->id                 = 'lova';
		$this->icon               = plugin_dir_url( dirname( __FILE__ ) ) . 'assets/img/lova.png';
		$this->method_title       = __( 'Lova Payment Gateway', 'lova-payment-gateway' );
		$this->method_description = __( 'Accept payments on your store via Lova Payment Gateway', 'lova-payment-gateway' );

		$this->supports = [ 'products' ];

		$this->init_form_fields();
		$this->init_settings();
		$this->title              = $this->get_option( 'title' );
		$this->description        = $this->get_option( 'description' );
		$this->enabled            = $this->get_option( 'enabled' );
		$this->test_mode          = 'yes' === $this->get_option( 'test_mode' );
		$this->email              = $this->get_option( 'email' );
		$this->password           = $this->get_option( 'password' );
		$this->api_key            = $this->test_mode ? $this->get_option( 'test_api_key' ) : $this->get_option( 'api_key' );
		$this->api_callback_token = $this->test_mode ? $this->get_option( 'test_api_callback_token' ) : $this->get_option( 'api_callback_token' );

		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
		add_action( 'woocommerce_before_checkout_form', [ $this, 'display_error_notice' ] );
		add_action( 'woocommerce_api_lova', [ $this, 'lova_webhook' ] );
	}

	function display_error_notice() {
		$error_message = sanitize_text_field( $_GET['errorMessage'] );
		if ( is_checkout() && ! empty( $error_message ) ) {
			wc_print_notice( $error_message, 'error' );
		}
	}

	public function init_form_fields() {
		$this->form_fields = [
			'enabled'                 => [
				'title'       => __( 'Enable/Disable', 'lova-payment-gateway' ),
				'label'       => __( 'Enable Lova Payment Gateway', 'lova-payment-gateway' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no'
			],
			'title'                   => [
				'title'       => __( 'Title', 'lova-payment-gateway' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'lova-payment-gateway' ),
				'default'     => 'Lova Payment Gateway',
				'desc_tip'    => true,
			],
			'description'             => [
				'title'       => __( 'Description', 'lova-payment-gateway' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'lova-payment-gateway' ),
				'default'     => __( 'Pay by crypto via Lova Payment Gateway.', 'lova-payment-gateway' ),
			],
			'test_mode'               => [
				'title'       => __( 'Test Mode', 'lova-payment-gateway' ),
				'label'       => __( 'Enable/Disable', 'lova-payment-gateway' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'yes',
				'desc_tip'    => true,
			],
			'email'                   => [
				'title' => __( 'Email', 'lova-payment-gateway' ),
				'type'  => 'email'
			],
			'password'                => [
				'title' => __( 'Password', 'lova-payment-gateway' ),
				'type'  => 'password'
			],
			'test_api_key'            => [
				'title' => __( 'Test API key', 'lova-payment-gateway' ),
				'type'  => 'text',
				'class' => 'test-field',
			],
			'test_api_callback_token' => [
				'title' => __( 'Test API callback token', 'lova-payment-gateway' ),
				'type'  => 'text',
				'class' => 'test-field',
			],
			'api_key'                 => [
				'title' => __( 'API key', 'lova-payment-gateway' ),
				'type'  => 'text',
				'class' => 'prod-field',
			],
			'api_callback_token'      => [
				'title' => __( 'API callback token', 'lova-payment-gateway' ),
				'type'  => 'text',
				'class' => 'prod-field',
			],
		];
	}

	function process_payment( $order_id ): array {
		$order    = new WC_Order( $order_id );
		$response = wp_remote_post( Main::$api_url . '/webshop/login', [
			'headers' => [
				'Accept'         => 'application/json',
				'X-localization' => 'en',
				$this->api_key   => $this->api_callback_token,
			],
			'body'    => [
				'email'    => $this->email,
				'password' => $this->password,
			],
			'timeout'       => 30,
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

		$response = wp_remote_post( Main::$api_url . '/webshop/fundrequest', [
			'headers' => [
				'Authorization'  => 'Bearer ' . $bearer_token,
				'Accept'         => 'application/json',
				'X-localization' => 'en',
				$this->api_key   => $this->api_callback_token,
			],
			'body'    => [
				'amount'         => $order->get_total(),
				'woo_order_id'   => $order->get_id(),
				'woo_return_url' => get_site_url() . '/wc-api/lova/',
			],
			'timeout'       => 30,
		] );


		if ( ! is_wp_error( $response ) ) {
			$body = json_decode( $response['body'], true );

			if ( $body['success'] ) {
				$redirect_url = $body['redirect_url'];

				return [
					'result'   => 'success',
					'redirect' => $redirect_url
				];
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

	}

	public function lova_webhook() {
		$response = wp_remote_post( Main::$api_url . '/webshop/login', [
			'headers' => [
				'Accept'         => 'application/json',
				'X-localization' => 'en',
				$this->api_key   => $this->api_callback_token,
			],
			'body'    => [
				'email'    => $this->email,
				'password' => $this->password,
			],
			'timeout'       => 30,
		] );
		if ( is_wp_error( $response ) ) {
			wp_safe_redirect( get_permalink( wc_get_page_id( 'checkout' ) ) . '?errorMessage=' . $response->get_error_message() );
			die;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! $body['success'] ) {
			wp_safe_redirect( get_permalink( wc_get_page_id( 'checkout' ) ) . '?errorMessage=API access error' );
			die;
		}
		$bearer_token = $body['token'];
		$tzid         = sanitize_text_field($_GET['transaction_id']);

		$response = wp_remote_get( Main::$api_url . '/webshop/fundrequest/show/' . $tzid, [
			'headers' => [
				'Authorization'  => 'Bearer ' . $bearer_token,
				'Accept'         => 'application/json',
				'X-localization' => 'en',
				$this->api_key   => $this->api_callback_token,
			]
		] );
		if ( is_wp_error( $response ) ) {
			wp_safe_redirect( get_permalink( wc_get_page_id( 'checkout' ) ) . '?errorMessage=' . $response->get_error_message() );
			die;
		}
		$body  = json_decode( wp_remote_retrieve_body( $response ), true );
		$order = wc_get_order( $body['data']['woo_order_id'] );

		if ( ! $order ) {
			wp_safe_redirect( get_permalink( wc_get_page_id( 'checkout' ) ) . '?errorMessage=Order number was not found' );
			die;
		}

		$status = $body['data']['status'];
		if ( $status === 'approve' ) {
			WC()->cart->empty_cart();
			$order->payment_complete();
			wp_safe_redirect( $this->get_return_url( $order ) );
		} else {
			wp_safe_redirect( get_permalink( wc_get_page_id( 'checkout' ) ) . '?errorMessage=Transaction status - is ' . $status );
		}
		die;
	}

}

new WC_Gateway_Lova;
