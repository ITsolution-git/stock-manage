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


        // change display number to order Id for fetching the order data
          var order_data = {};
           order_data.cond ={company_id :sessionService.get('company_id'),display_number:$stateParams.id};
           order_data.table ='orders';
          
          $http.post('api/public/common/GetTableRecords',order_data).success(function(result) {
            
              
              if(result.data.success == '1') 
              {
                  $scope.order_id = result.data.records[0].id;
                    $scope.invoiceData();
                   
              } 
              
          });


            $scope.invoiceData = function(){

                  var invoice_data = {};
                    invoice_data.cond ={order_id:$scope.order_id};
                    invoice_data.table ='invoice';
                    
                    $http.post('api/public/common/GetTableRecords',invoice_data).success(function(result) {
                        
                        if(result.data.success == '1') 
                        {
                           
                            $scope.invoice_display_number = result.data.records[0].display_number;
                            $scope.invoice_id = result.data.records[0].id;
                            $scope.qb_invoice_id = result.data.records[0].qb_id;
                        } 
                        else
                        {
                            $scope.invoice_id = 0;
                        }
                    });
            }

      

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
                combine_array_id.id = $scope.order_id;
                combine_array_id.company_id = sessionService.get('company_id');
                combine_array_id.company_name = sessionService.get('company_name');
                combine_array_id.sns_shipping = sns_shipping;
                combine_array_id.user_id = sessionService.get('user_id');
                
                $("#ajax_loader").show();
               
                $http.post('api/public/order/snsOrder',combine_array_id).success(function(result) 
                {
                    $("#ajax_loader").hide();
                    if(result.data.success=='1')
                    {
                        notifyService.notify('success',result.data.message);
                        $mdDialog.hide();
                        return false;
                    }
                    else
                    {
                        notifyService.notify('error',result.data.message);
                        $mdDialog.hide();
                        return false;
                    }
                });
            }

             if($scope.invoice == true && $scope.invoice_id == 0)
            {

               if($scope.payment_terms ==  undefined) {
                    notifyService.notify('error','Please select Payment Terms for creating invoice');
                    return false;
                }
               
                var combine_array = {};
                combine_array.order_id = $scope.order_id;
                combine_array.payment = $scope.payment_terms;
                 combine_array.company_id = sessionService.get('company_id');

                
                
               
                $http.post('api/public/order/createInvoice',combine_array).success(function(result) 
                {
                    
                    if(result.data.success=='1')
                    {
                        $scope.invoice_id = result.data.invoice_id;
                        $scope.qb_invoice_id = result.data.qb_invoice_id;
                        $scope.invoice_display_number = result.data.display_number;

                        $mdDialog.hide();
                        $state.go('app.invoices.singleInvoice',{id: $scope.invoice_display_number});

                         if($scope.invoice == true && $scope.qb == true) {

                            var combine_array_id = {};
                                combine_array_id.id = $scope.order_id;
                                combine_array_id.company_id = sessionService.get('company_id');
                                combine_array_id.client_id = client_id;
                                combine_array_id.invoice_id = $scope.invoice_id;
                                combine_array_id.payment = $scope.payment_terms;
                                combine_array_id.quickbook_id = $scope.qb_invoice_id;
                                
                               $("#ajax_loader").show();
                               
                                 $http.post('api/public/order/addInvoice',combine_array_id).success(function(result) 
                                {
                                  
                                   if(result.data.success=='0') {
                                      notifyService.notify('error',result.data.message);
                                    }

                                    $mdDialog.hide();
                                    $state.go('app.invoices.singleInvoice',{id: $scope.invoice_display_number});
                                    $("#ajax_loader").hide();
                                  
                                });
                        } else {

                             $mdDialog.hide();
                             $state.go('app.invoices.singleInvoice',{id: $scope.invoice_display_number});
                        }
                    }
                });
            }


            if($scope.invoice == true && $scope.invoice_id > 0)
            {

                 if($scope.payment_terms ==  undefined) {
                    notifyService.notify('error','Please select Payment Terms for creating invoice');
                    return false;
                }
               
                var combine_array = {};
                combine_array.order_id = $scope.order_id;
                combine_array.payment = $scope.payment_terms;
                combine_array.invoice_id = $scope.invoice_id;
                combine_array.company_id = sessionService.get('company_id');
                
                
               
                $http.post('api/public/order/updateInvoicePayment',combine_array).success(function(result) 
                {
                   
                   
                });
               
            }

            if($scope.invoice == true && $scope.qb == true && $scope.invoice_id > 0) {

               

                if($scope.payment_terms ==  undefined) {
                    notifyService.notify('error','Please select Payment Terms for creating invoice');
                    return false;
                }
               
                var combine_array_id = {};
                    combine_array_id.id = $scope.order_id;
                    combine_array_id.company_id = sessionService.get('company_id');
                    combine_array_id.client_id = client_id;
                    combine_array_id.invoice_id = $scope.invoice_id;
                    combine_array_id.payment = $scope.payment_terms;
                    combine_array_id.quickbook_id = $scope.qb_invoice_id;
                    
                   $("#ajax_loader").show();
                   
                     $http.post('api/public/order/addInvoice',combine_array_id).success(function(result) 
                    {
                     
                       if(result.data.success=='0') {
                          notifyService.notify('error',result.data.message);
                        }

                        $mdDialog.hide();
                        $state.go('app.invoices.singleInvoice',{id: $scope.invoice_display_number});
                         $("#ajax_loader").hide();
                      
                    });
            }

           

            if($scope.invoice_id > 0)
            {
                $mdDialog.hide();
                $state.go('app.invoices.singleInvoice',{id: $scope.invoice_display_number});
            }
        }
    }
})();