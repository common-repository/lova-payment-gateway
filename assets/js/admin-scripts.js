jQuery(function ($) {
    var scripts = {
        init: function () {
            this.testModeHandler();
            this.filtersHandler();
        },
        testModeHandler: function(){
            const testmode = $('#woocommerce_lova_test_mode');
            const test_class = $('.test-field');
            const prod_class = $('.prod-field');
            // On change 
            testmode.change(function() {
                if ( $(this).is(':checked') ){
                    test_class.closest('tr').css('display', 'table-row');
                    prod_class.closest('tr').css('display', 'none');
                }else{
                    test_class.closest('tr').css('display', 'none');
                    prod_class.closest('tr').css('display', 'table-row');
                }
            });

            // On load page
            if (testmode.is(':checked')){
                test_class.closest('tr').css('display', 'table-row');
                prod_class.closest('tr').css('display', 'none');
            }else{
                test_class.closest('tr').css('display', 'none');
                prod_class.closest('tr').css('display', 'table-row');
            }
        },
        filtersHandler: function (){
            var options = {
                valueNames: [ 'id', 'to', 'amount', 'fee_total', 'fee', 'total_amount', 'status', 'transaction_id', 'date', 'mode' ]
            };
              
            var transactionList = new List('transaction-list', options);

            let convertDigitIn = function (str){
                return str.split('-').reverse().join('-');
            };

            var updateList = function(){
                var values_status = $(".status_s").val();
                var values_date_s = convertDigitIn($(".date_s").val());
                var values_date_e = convertDigitIn($(".date_e").val());
                let totalAmount = 0;

                transactionList.filter(function(item) {
                    if ( ( values_status === item.values().status || !values_status) && ( values_date_s <= item.values().date.split(" ")[0] || !values_date_s) && (values_date_e >= item.values().date.split(" ")[0] || !values_date_e) ){
                        totalAmount += parseFloat(item.values().total_amount);
                    }
                    return ( values_status === item.values().status || !values_status)
                    && ( values_date_s <= item.values().date.split(" ")[0] || !values_date_s)
                    && (values_date_e >= item.values().date.split(" ")[0] || !values_date_e); 
                });

                $('#total_amount').text(totalAmount);
            }
                                        
            $(".status_s").change(updateList);
            $(".date_s").change(updateList);
            $(".date_e").change(updateList);

            let totalAmount = 0;

            transactionList.filter(function(item){
                totalAmount += parseFloat(item.values().total_amount);
                return item;
            });

            $('#total_amount').text(totalAmount);
        },
    }
    scripts.init();
});
