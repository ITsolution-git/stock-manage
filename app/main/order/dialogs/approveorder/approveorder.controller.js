(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('approveOrderDiallogController', approveOrderDiallogController);

    /** @ngInject */
    function approveOrderDiallogController(order_number,sns_shipping,client_id,$mdDialog,$document, $window, $timeout,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {
        var vm = this;
        vm.title = 'Order Approved';
        $scope.cancel = function () {
            $mdDialog.hide();
        };
        /**
         * Close dialog
         */
        function closeDialog()
        {
            $mdDialog.hide();
        }


        var invoice_data = {};
        invoice_data.cond ={order_id:$stateParams.id};
        invoice_data.table ='invoice';
        
        $http.post('api/public/common/GetTableRecords',invoice_data).success(function(result) {
            
            if(result.data.success == '1') 
            {
                $scope.invoice_id = result.data.records[0].id;
                $scope.qb_invoice_id = result.data.records[0].qb_id;
            } 
            else
            {
                $scope.invoice_id = 0;
            }
        });

        $scope.save = function () {
          
            if($scope.invoice ==  undefined && $scope.qb == true) {
                notifyService.notify('error','Please select Create Invoice to sync with Quickbook');
                    return false;
            }


            if($scope.sns == true) {

                if(order_number != '') {
                    notifyService.notify('error','You have already posted order to S&S');
                    return false;
                }

                var combine_array_id = {};
                combine_array_id.id = $stateParams.id;
                combine_array_id.company_id = sessionService.get('company_id');
                combine_array_id.company_name = sessionService.get('company_name');
                combine_array_id.sns_shipping = sns_shipping;
                
                $("#ajax_loader").show();
               
                $http.post('api/public/order/snsOrder',combine_array_id).success(function(result) 
                {
                    $("#ajax_loader").hide();
                    if(result.data.success=='1')
                    {
                        notifyService.notify('success',result.data.message);
                    }
                    else
                    {
                        notifyService.notify('error',result.data.message);
                        return false;
                    }
                });
            }

             if($scope.invoice == true && $scope.invoice_id == 0)
            {
               
                var combine_array = {};
                combine_array.order_id = $stateParams.id;
                
                $("#ajax_loader").show();
               
                $http.post('api/public/order/createInvoice',combine_array).success(function(result) 
                {
                    $("#ajax_loader").hide();
                    if(result.data.success=='1')
                    {
                        $scope.invoice_id = result.data.invoice_id;
                        $scope.qb_invoice_id = result.data.qb_invoice_id;

                        $mdDialog.hide();
                        $state.go('app.invoices.singleInvoice',{id: $scope.invoice_id});

                         if($scope.invoice == true && $scope.qb == true && $scope.qb_invoice_id == 0) {

                            var combine_array_id = {};
                                combine_array_id.id = $stateParams.id;
                                combine_array_id.company_id = sessionService.get('company_id');
                                combine_array_id.client_id = client_id;
                                combine_array_id.invoice_id = $scope.invoice_id;
                                
                               $("#ajax_loader").show();
                               
                                 $http.post('api/public/order/addInvoice',combine_array_id).success(function(result) 
                                {
                                  $("#ajax_loader").hide();

                                   if(result.data.success=='0') {
                                      notifyService.notify('error',result.data.message);
                                    }

                                    $mdDialog.hide();
                                    $state.go('app.invoices.singleInvoice',{id: $scope.invoice_id});
                                  
                                });
                        } else {

                             $mdDialog.hide();
                             $state.go('app.invoices.singleInvoice',{id: $scope.invoice_id});
                        }
                    }
                });
            }

            if($scope.invoice == true && $scope.qb == true && $scope.qb_invoice_id == 0) {

               
                var combine_array_id = {};
                    combine_array_id.id = $stateParams.id;
                    combine_array_id.company_id = sessionService.get('company_id');
                    combine_array_id.client_id = client_id;
                    combine_array_id.invoice_id = $scope.invoice_id;
                    combine_array_id.payment = $scope.payment_terms;
                    
                   $("#ajax_loader").show();
                   
                     $http.post('api/public/order/addInvoice',combine_array_id).success(function(result) 
                    {
                      $("#ajax_loader").hide();

                       if(result.data.success=='0') {
                          notifyService.notify('error',result.data.message);
                        }

                        $mdDialog.hide();
                        $state.go('app.invoices.singleInvoice',{id: $scope.invoice_id});
                      
                    });
            }

           

            if($scope.invoice_id > 0)
            {
                $mdDialog.hide();
                $state.go('app.invoices.singleInvoice',{id: $scope.invoice_id});
            }
        }
    }
})();