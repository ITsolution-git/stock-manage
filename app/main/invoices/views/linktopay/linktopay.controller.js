(function ()
{
    'use strict';

    angular
            .module('app.invoices')
            .controller('extLinktoPayController', extLinktoPayController);

    /** @ngInject */


    function extLinktoPayController($scope, notifyService, $http)
    {
      var vm = this;
        $scope.cancel = function () {
            $mdDialog.hide();
        };

        $scope.pay_creditCard = function(paymentData, invoice_id, ltp_id){
            if(paymentData == undefined ) {
                var data = {"status": "error", "message": "Please enter Payment Information"}
                notifyService.notify(data.status, data.message);
                return false;
            }
                
            var combine_array_id = {};
            combine_array_id.creditFname = paymentData.creditFname;
            combine_array_id.creditLname = paymentData.creditLname;
            combine_array_id.creditCard = paymentData.creditCard;
            combine_array_id.cvv = paymentData.cvv;
            combine_array_id.expMonth = paymentData.expMonth;
            combine_array_id.expYear = paymentData.expYear;
            combine_array_id.amount = paymentData.amount;

            combine_array_id.street = paymentData.street;
                combine_array_id.suite = paymentData.suite;
                combine_array_id.city = paymentData.city;
                combine_array_id.state = paymentData.state;
                combine_array_id.zip = paymentData.zip;

            if(!paymentData.storeCard) {
                combine_array_id.storeCard = 0;
            }else{
                combine_array_id.storeCard = 1;
            }
            combine_array_id.invoice_id = invoice_id;
            combine_array_id.linkToPay = 1;
            combine_array_id.ltp_id=ltp_id;
            //alert(combine_array_id.invoice_id);return false;


                $http.post('api/public/payment/chargeCreditCard',combine_array_id).success(function(result) 
                {
                    //$mdDialog.hide();
                    if(result != '0')
                    {
                        /*$scope.allData.order_data[0].total_payments = result.data.amt.total_payments;
                        $scope.allData.order_data[0].balance_due = result.data.amt.balance_due;
                        $http.get('api/public/invoice/getInvoiceHistory/'+$stateParams.id+'/'+sessionService.get('company_id')+'/0').success(function(result) {
                            $scope.siData = result.data.allData;
                        });*/
                        notifyService.notify('success',"Payment made Successfully");
                    }
                    else
                    {
                        notifyService.notify('error',"Payment could not be made");
                    }
                    return false;
                    
                });
            }
        /**
         * Close dialog
         */
        function closeDialog() {
            $mdDialog.hide();
        }
                
    }
})();
