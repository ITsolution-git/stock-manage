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
            if(paymentData == undefined ) {
                var data = {"status": "error", "message": "Please enter Payment Information"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.creditFname == undefined) {
                var data = {"status": "error", "message": "Please enter First Name"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.creditLname == undefined) {
                var data = {"status": "error", "message": "Please enter Last Name"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.creditCard == undefined) {
                var data = {"status": "error", "message": "Please enter Credit Card number"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if((paymentData.amount == undefined) || (paymentData.amount==0) || (paymentData.amount==0.00)) {
                var data = {"status": "error", "message": "Amount should not be blank or 0"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.expMonth == undefined) {
                var data = {"status": "error", "message": "Please select Month of Expiration"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.expYear == undefined) {
                var data = {"status": "error", "message": "Please select Year of Expiration"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.cvv == undefined) {
                var data = {"status": "error", "message": "Please enter CVV"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.street == undefined) {
                var data = {"status": "error", "message": "Please enter Street Address"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.suite == undefined) {
                var data = {"status": "error", "message": "Please enter Suite"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.city == undefined) {
                var data = {"status": "error", "message": "Please enter City"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.state == undefined) {
                var data = {"status": "error", "message": "Please select State"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.zip == undefined) {
                var data = {"status": "error", "message": "Please enter Zip"}
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
