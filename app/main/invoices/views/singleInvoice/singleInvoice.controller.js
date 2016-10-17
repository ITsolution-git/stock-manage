(function ()
{
    'use strict';

    angular
            .module('app.invoices')
            .controller('singleInvoiceController', singleInvoiceController);
    /** @ngInject */
    function singleInvoiceController($q,$mdDialog,$document,$mdSidenav,DTOptionsBuilder,DTColumnBuilder,$resource,$scope,$http,sessionService,notifyService,$stateParams,$state) {
        var vm = this;
        vm.linktopay = linktopay;

        var invoice_data = {invoice_id:$stateParams.id,company_id :sessionService.get('company_id')};
        
        $http.get('api/public/invoice/getInvoiceDetail/'+$stateParams.id+'/'+sessionService.get('company_id')+'/0').success(function(result) {

            if(result.data.success == '0') {
                    $state.go('app.invoices');
                } 

            $scope.allData = result.data.allData;
            if(result.data.allData.order_data[0].grand_total > result.data.allData.order_data[0].total_payments){
                $scope.showPaymentDetails = true;
            }else{
                $scope.showPaymentDetails = false;
            }

            $scope.brand_coordinator = sessionService.get('role_title');
        });

        $http.get('api/public/invoice/getInvoiceHistory/'+$stateParams.id+'/'+sessionService.get('company_id')+'/0').success(function(resultHistory) {

            /*if(result.data.success == '0') {
                    $state.go('app.invoices');
                }*/
                
            $scope.siData = resultHistory.data.allData;
        });

        /*$http.get('api/public/invoice/getInvoicePayment/'+$stateParams.id+'/'+sessionService.get('company_id')+'/0').success(function(result123) {

            if(result123.data.success == '0') {
                    $state.go('app.invoices');
            }else{
                //$scope.spData = result123.data.allData[0];
                //alert(result123.data.allData[0].first_name+' : '+result123.data.allData[0].last_name+' : '+result123.data.allData[0].credit_card);
                //$scope.company = result123.data.allData[0];
            }
        });*/

        $http.get('api/public/invoice/getInvoiceCards/'+$stateParams.id+'/'+sessionService.get('company_id')+'/0').success(function(result123) {

            if(result123.data.success == '0') {
                    //$state.go('app.invoices');
            }else{
                $scope.cardsAll = result123.data.allData;
            }
        });

        var misc_list_data = {};
        var condition_obj = {};
        condition_obj['company_id'] =  sessionService.get('company_id');
        misc_list_data.cond = angular.copy(condition_obj);

        $http.post('api/public/common/getAllMiscDataWithoutBlank',misc_list_data).success(function(result, status, headers, config) {
                $scope.miscData = result.data.records;
        });

        var state = {};
        state.table ='state';

        $http.post('api/public/common/GetTableRecords',state).success(function(result) 
        {   
            if(result.data.success=='1')
            {   
                $scope.states_all = result.data.records;
            }
        });

        // JS FOR MODAL LINK TO PAY
        function linktopay(ev, settings) {
            var invoice_id = document.createElement('input');
            invoice_id.name = 'invoice_id';
            invoice_id.setAttribute('value', $stateParams.id);
            

            var company_id = document.createElement('input');
            company_id.name = 'company_id';
            company_id.setAttribute('value', sessionService.get('company_id'));

            var combine_array_id = {};
            combine_array_id.invoice_id = invoice_id.value;
            $("#ajax_loader").show();

            $http.post('api/public/order/paymentLinkToPay',combine_array_id).success(function(result) 
            {
                $("#ajax_loader").hide();
                if(result.data.success=='1')
                {
                    //notifyService.notify('success',"Payment added Successfully");
                    $mdDialog.show({
                controller: 'linktoPayController',
                controllerAs: 'vm',
                templateUrl: 'app/main/invoices/dialogs/linktopay/linktopay-dialog.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    event: ev,
                    result: result
                }
            });
                }
                else
                {
                    notifyService.notify('error',"Please try again");
                }
            });
        }

        $scope.updateOrderStatus = function(name,value,id)
        {
            var order_main_data = {};

            order_main_data.table ='orders';

            $scope.name_filed = name;
            var obj = {};
            obj[$scope.name_filed] =  value;
            order_main_data.data = angular.copy(obj);

            var condition_obj = {};
            condition_obj['id'] =  id;
            order_main_data.cond = angular.copy(condition_obj);

            $http.post('api/public/common/UpdateTableRecords',order_main_data).success(function(result) {

                var data = {"status": "success", "message": "Data Updated Successfully."}
                notifyService.notify(data.status, data.message);
            });
        }

        $scope.print_pdf = function()
        {
            var target;
            var form = document.createElement("form");
            form.action = 'api/public/invoice/createInvoicePdf';
            form.method = 'post';
            form.target = target || "_blank";
            form.style.display = 'none';

            var invoice_id = document.createElement('input');
            invoice_id.name = 'invoice_id';
            invoice_id.setAttribute('value', $stateParams.id);
            form.appendChild(invoice_id);

            var company_id = document.createElement('input');
            company_id.name = 'company_id';
            company_id.setAttribute('value', sessionService.get('company_id'));
            form.appendChild(company_id);

            document.body.appendChild(form);
            form.submit();
        }

        /*$scope.validateFloatKeyPress = function()
        {
            var v = parseFloat($scope.pay.cashAmount);
            $scope.pay.cashAmount = (isNaN(v)) ? '' : v.toFixed(2);
        }*/

        $scope.getStoredProfile = function(profile)
        {
            if(profile != 0){
                $("#ajax_loader").show();
                var combine_array_id = {};
                combine_array_id.cppd_id = profile;
                $http.post('api/public/invoice/getPaymentCard',combine_array_id).success(function(result) 
                {
                    $("#ajax_loader").hide();
                    if(result.data.success=='1')
                    {
                        $scope.company.creditFname = 'XXXXXX';
                        $scope.company.creditLname = 'XXXXXX';
                        $scope.company.creditCard = '000000000000000000';
                        $scope.company.expMonth = '01';
                        $scope.company.expYear = '22';
                        $scope.company.cvv = '000';
                        $scope.company.street = 'XXXXXX';
                        $scope.company.city = 'XXXXXX';
                        $scope.company.state = 'AL';
                        $scope.company.zip = '00000';
                        $scope.company.amount = $scope.allData.order_data[0].balance_due;
                    }
                    else{
                        var data = {"status": "error", "message": "Please try with any other saved card or new credit card."}
                        notifyService.notify(data.status, data.message);
                        return false;
                    }
                });
            }else{
                $scope.company.creditFname = '';
                $scope.company.creditLname = '';
                $scope.company.creditCard = '';
                $scope.company.expMonth = '';
                $scope.company.expYear = '';
                $scope.company.cvv = '';
                $scope.company.street = '';
                $scope.company.city = '';
                $scope.company.state = '';
                $scope.company.zip = '';   
            }
            
        }


        $scope.pay_cash = function(amount)
        {
            if((amount == undefined) || (amount.cashAmount==0) || (amount.cashAmount==0.00)) {
                var data = {"status": "error", "message": "Amount should not be blank or 0"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            $("#ajax_loader").show();

            var invoice_id = document.createElement('input');
            invoice_id.name = 'invoice_id';
            invoice_id.setAttribute('value', $stateParams.id);
            

            var company_id = document.createElement('input');
            company_id.name = 'company_id';
            company_id.setAttribute('value', sessionService.get('company_id'));

            var combine_array_id = {};
            combine_array_id.amount = amount.cashAmount;
            combine_array_id.invoice_id = invoice_id.value;
            combine_array_id.company_id = sessionService.get('company_id');

            $http.post('api/public/order/paymentInvoiceCash',combine_array_id).success(function(result) 
            {
                amount.cashAmount = null;
                amount.cashAmount = '';
                if(result.data.success=='1')
                {
                    $scope.allData.order_data[0].total_payments = result.data.amt.total_payments;
                    $scope.allData.order_data[0].balance_due = result.data.amt.balance_due;
                    if($scope.allData.order_data[0].grand_total > $scope.allData.order_data[0].total_payments){
                        $scope.showPaymentDetails = true;
                    }else{
                        $scope.showPaymentDetails = false;
                        $scope.allData.order_data[0].approval_id = result.data.amt.approval_id;
                    }
                    $http.get('api/public/invoice/getInvoiceHistory/'+$stateParams.id+'/'+sessionService.get('company_id')+'/0').success(function(result) {
                        $scope.siData = result.data.allData;
                    });
                    $("#ajax_loader").hide();
                    notifyService.notify('success',"Payment added Successfully");
                }
                else
                {
                    $("#ajax_loader").hide();
                    notifyService.notify('error',"Payment not added");
                }
            });

        }
        $scope.pay_creditCard = function(paymentData)
        {
            //paymentData.storeCard
            
            if(paymentData == undefined ) {
                var data = {"status": "error", "message": "Please enter Payment Information"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.creditFname.length == 0) {
                var data = {"status": "error", "message": "Please enter First Name"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.creditLname.length == 0) {
                var data = {"status": "error", "message": "Please enter Last Name"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.creditCard == undefined) {
                var data = {"status": "error", "message": "Please enter Credit Card number"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if((paymentData.creditCard.length < 12) || (paymentData.creditCard.length > 20)) {
                var data = {"status": "error", "message": "Please enter valid Credit Card number"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if((paymentData.amount == undefined) || (paymentData.amount==0) || (paymentData.amount==0.00)) {
                var data = {"status": "error", "message": "Amount should not be blank or 0"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.expMonth.length == 0) {
                var data = {"status": "error", "message": "Please select Month of Expiration"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.expYear.length == 0) {
                var data = {"status": "error", "message": "Please select Year of Expiration"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.cvv == undefined) {
                var data = {"status": "error", "message": "Please enter CVV"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.street.length == 0) {
                var data = {"status": "error", "message": "Please enter Street Address"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.city.length == 0) {
                var data = {"status": "error", "message": "Please enter City"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.state.length == 0) {
                var data = {"status": "error", "message": "Please select State"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if(paymentData.zip.length == 0) {
                var data = {"status": "error", "message": "Please enter Zip"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            //storeCard
            /**/
            
            $("#ajax_loader").show();

            var invoice_id = document.createElement('input');
            invoice_id.name = 'invoice_id';
            invoice_id.setAttribute('value', $stateParams.id);
            

            var company_id = document.createElement('input');
            company_id.name = 'company_id';
            company_id.setAttribute('value', sessionService.get('company_id'));

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
            combine_array_id.linkToPay = 0;
            if(paymentData.savedCard) {
                combine_array_id.savedCard = paymentData.savedCard;
            }
            
            combine_array_id.invoice_id = invoice_id.value;
            combine_array_id.company_id = company_id.value;


            $http.post('api/public/payment/chargeCreditCard',combine_array_id).success(function(result) 
            {
                $("#ajax_loader").hide();
                if(result.data.success=='1')
                {
                    $scope.allData.order_data[0].total_payments = result.data.amt.total_payments;
                    $scope.allData.order_data[0].balance_due = result.data.amt.balance_due;
                    if($scope.allData.order_data[0].grand_total > $scope.allData.order_data[0].total_payments){
                        $scope.showPaymentDetails = true;
                    }else{
                        $scope.showPaymentDetails = false;
                        $scope.allData.order_data[0].approval_id = result.data.amt.approval_id;
                    }
                    $http.get('api/public/invoice/getInvoiceHistory/'+$stateParams.id+'/'+sessionService.get('company_id')+'/0').success(function(resultData) {
                        $scope.siData = resultData.data.allData;
                    });

                    $http.get('api/public/invoice/getInvoiceCards/'+$stateParams.id+'/'+sessionService.get('company_id')+'/0').success(function(result123) {
                        if(result123.data.success == '1') {
                            $scope.cardsAll = result123.data.allData;
                        }
                    });
                    //$scope.paymentData = null;
                    /*if(!paymentData.storeCard) {
                        paymentData.creditFname = null;
                        paymentData.creditLname = null;
                        paymentData.creditCard = null;
                        paymentData.cvv = null;
                        paymentData.expMonth = null;
                        paymentData.expYear = null;
                        paymentData.amount = null;
                        paymentData.street = null;
                        paymentData.suite = null;
                        paymentData.city = null;
                        paymentData.state = null;
                        paymentData.zip = null;
                        paymentData.storeCard = null;
                    }else{
                        paymentData.amount = null;
                        paymentData.cvv = null;
                    }*/
                    paymentData.creditFname = null;
                    paymentData.creditLname = null;
                    paymentData.creditCard = null;
                    paymentData.cvv = null;
                    paymentData.expMonth = null;
                    paymentData.expYear = null;
                    paymentData.amount = null;
                    paymentData.street = null;
                    paymentData.suite = null;
                    paymentData.city = null;
                    paymentData.state = null;
                    paymentData.zip = null;
                    paymentData.storeCard = null;

                    notifyService.notify('success',"Payment made Successfully");
                }
                else
                {
                    notifyService.notify('error',"Payment could not be made. Please verify your card details with Authorized.net.");
                }
                return false;
                
            });
        }
        $scope.deleteHistory = function(payment_id,method)
        {
            $("#ajax_loader").show();
            if(method=='Credit Card'){
                var invoice_id = document.createElement('input');
                invoice_id.name = 'invoice_id';
                invoice_id.setAttribute('value', $stateParams.id);

                var company_id = document.createElement('input');
                company_id.name = 'company_id';
                company_id.setAttribute('value', sessionService.get('company_id'));

                var combine_array = {};
                combine_array.payment_id = payment_id;
                combine_array.company_id = company_id.value;
                combine_array.invoice_id = invoice_id.value;
                $http.post('api/public/payment/refundTransaction',combine_array).success(function(result) 
                {
                    
                    if(result.data.success=='1')
                    {
                        var vm = this;
                        var UpdateArray = {};
                        UpdateArray.table ='payment_history';
                        UpdateArray.data = {is_delete:0};
                        UpdateArray.cond = {payment_id:payment_id};

                        $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(resultUpdate) 
                        {
                            if(resultUpdate.data.success=='1')
                            {
                                $http.get('api/public/invoice/getInvoiceHistory/'+$stateParams.id+'/'+sessionService.get('company_id')+'/0').success(function(result123) {
                                    $scope.siData = result123.data.allData;

                                    var combine_array_id = {};
                                    combine_array_id.invoice_id = $stateParams.id;
                                    combine_array_id.company_id = sessionService.get('company_id');

                                    $http.post('api/public/order/paymentInvoiceCash',combine_array_id).success(function(resultUpdate) 
                                    {
                                        $("#ajax_loader").hide();
                                        $scope.allData.order_data[0].total_payments = resultUpdate.data.amt.total_payments;
                                        $scope.allData.order_data[0].balance_due = resultUpdate.data.amt.balance_due;
                                        if($scope.allData.order_data[0].grand_total > $scope.allData.order_data[0].total_payments){
                                            $scope.showPaymentDetails = true;
                                            if($scope.allData.order_data[0].approval_id == 2885){

                                                var UpdateArray = {};
                                                UpdateArray.table ='orders';
                                                UpdateArray.data = {approval_id:2491};
                                                UpdateArray.cond = {id:$scope.allData.order_data[0].id};
                                                $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(resultUpdateStatus) 
                                                {
                                                    if(resultUpdateStatus.data.success=='1')
                                                    {
                                                        $scope.allData.order_data[0].approval_id = 2491;
                                                    }
                                                });
                                            }
                                        }else{
                                            $scope.showPaymentDetails = false;
                                            $scope.allData.order_data[0].approval_id = resultUpdate.data.amt.approval_id;
                                        }
                                    });
                                });
                            }
                            else
                            {
                                //notifyService.notify('error',resultUpdate.data.message);
                                $("#ajax_loader").hide();
                                notifyService.notify('error',"Refund Transaction Failed. Please try again after a few hours.");
                            }

                        });
                        $("#ajax_loader").hide();
                        notifyService.notify('success', "Record Deleted Successfully!");
                    }
                    else
                    {
                        $("#ajax_loader").hide();
                        notifyService.notify('error',result.data.message);
                    }
                });
            }else{
                var vm = this;
                var UpdateArray = {};
                UpdateArray.table ='payment_history';
                UpdateArray.data = {is_delete:0};
                UpdateArray.cond = {payment_id:payment_id};

                $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
                {
                    if(result.data.success=='1')
                    {
                       $http.get('api/public/invoice/getInvoiceHistory/'+$stateParams.id+'/'+sessionService.get('company_id')+'/0').success(function(result123) {
                            $scope.siData = result123.data.allData;

                            var combine_array_id = {};
                            combine_array_id.invoice_id = $stateParams.id;
                            combine_array_id.company_id = sessionService.get('company_id');

                            $http.post('api/public/order/paymentInvoiceCash',combine_array_id).success(function(resultUpdate) 
                            {
                                $scope.allData.order_data[0].total_payments = resultUpdate.data.amt.total_payments;
                                $scope.allData.order_data[0].balance_due = resultUpdate.data.amt.balance_due;
                                if($scope.allData.order_data[0].grand_total > $scope.allData.order_data[0].total_payments){
                                    $scope.showPaymentDetails = true;
                                    if($scope.allData.order_data[0].approval_id == 2885){
                                        var UpdateArray = {};
                                        UpdateArray.table ='orders';
                                        UpdateArray.data = {approval_id:2491};
                                        UpdateArray.cond = {id:$scope.allData.order_data[0].id};
                                        $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(resultUpdateStatus) 
                                        {
                                            if(resultUpdateStatus.data.success=='1')
                                            {
                                                $scope.allData.order_data[0].approval_id = 2491;
                                            }
                                        });
                                    }
                                }else{
                                    $scope.showPaymentDetails = false;
                                }
                                $("#ajax_loader").hide();
                            });
                        });
                        notifyService.notify('success', "Record Deleted Successfully!");
                    }
                    else
                    {
                        $("#ajax_loader").hide();
                        notifyService.notify('error',result.data.message);
                    }
                });
            }            
        }
    }
})();
