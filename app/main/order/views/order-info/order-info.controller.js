(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('OrderInfoController', OrderInfoController);

    /** @ngInject */
    function OrderInfoController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {

         // change display number to order Id for fetching the order data
          var order_data = {};
           order_data.cond ={company_id :sessionService.get('company_id'),display_number:$stateParams.id};
           order_data.table ='orders';
          
          $http.post('api/public/common/GetTableRecords',order_data).success(function(result) {
            
              
              if(result.data.success == '1') 
              {
                  $scope.vendorRecord =result.data.records;
                  $scope.order_id = result.data.records[0].id;

                    $scope.orderDetail();
                    $scope.designDetail();
                    $scope.listAffiliate();

              } 
              else
              {
                   $state.go('app.order');
              }
          });



        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='SU' || $scope.role_slug=='AT')
        {
            $scope.allow_access = 0; // OTHER ROLES CAN NOT ALLOW TO EDIT, CAN VIEW ONLY
        }
        else
        {
            $scope.allow_access = 1;  // THESE ROLES CAN ALLOW TO EDIT
        }


        $scope.orderDetail = function(){
            $("#ajax_loader").show();
            
            var combine_array_id = {};
            combine_array_id.id = $scope.order_id;
            combine_array_id.company_id = sessionService.get('company_id');
            $scope.order_id = $scope.order_id;
            $scope.company_id = sessionService.get('company_id');
            

            $http.post('api/public/order/orderDetail',combine_array_id).success(function(result, status, headers, config) {
                if(result.data.success == '1') {
                    $("#ajax_loader").hide();
                   $scope.order = result.data.records[0];
                   $scope.order_items = result.data.order_item;
                   if($scope.order.item_ship_charge == undefined || $scope.order.item_ship_charge == '')
                   {
                        $scope.order.item_ship_charge = 0;
                   }
                } else {
                    $state.go('app.order');
                }
            });
          }

        $scope.designDetail = function(){

            var combine_array_id = {};
            combine_array_id.id = $scope.order_id;
            combine_array_id.company_id = sessionService.get('company_id');

            $http.post('api/public/order/designListing',combine_array_id).success(function(result, status, headers, config) {
            
                if(result.data.success == '1') {
                   $scope.designs = result.data.records.all_design;
                   $scope.total_unit = result.data.records.total_unit;
                }
                else {
                    $scope.designs = [];
                    $scope.total_unit = 0;
                }

                if($scope.total_unit == undefined)
                {
                    $scope.total_unit = 0;            
                }
            });
        }

        $scope.listAffiliate = function(){

            var combine_array_id = {};
            combine_array_id.id = $scope.order_id;
            combine_array_id.company_id = sessionService.get('company_id');

            $http.post('api/public/affiliate/getAffiliateData',combine_array_id).success(function(result, status, headers, config) {
            
                if(result.data.success == '1') {
                   $scope.affiliateList = result.data.affiliateList;
                }
                else {
                    $scope.affiliateList = [];
                }
            });
        }

      

        $scope.checkDesign = function()
        {

            if($scope.designs.length == 0)
            {
                var data = {"status": "error", "message": "Please add design to split order"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            /*else if($scope.designs[0].size_data.length == 0)
            {
                var data = {"status": "error", "message": "Please add product to split order"}
                notifyService.notify(data.status, data.message);
                return false;
            }*/
            else
            {
                $state.go('app.order.spiltAffiliate',{id: $stateParams.id});
            }
        }
       
        var vm = this;
        vm.openaddDesignDialog = openaddDesignDialog;

        /* vm.orderDetails = OrderDataDetail.data.records;
         console.log(vm.orderDetails);*/

        vm.openaddSplitAffiliateDialog = openaddSplitAffiliateDialog;
        vm.openinformationDialog = openinformationDialog;
        vm.openApproveOrderDialog = openApproveOrderDialog;
      
        vm.purchases = [
            {"poid": "27", "potype": "Purchase Order", "clientName": "kensville", "vendor": "SNS", "dateCreated": "xx/xx/xxxx"},
            {"poid": "28", "potype": "Purchase Order", "clientName": "Design T-shirt", "vendor": "Nike", "dateCreated": "xx/xx/xxxx"},
        ];
        vm.receives = [
            {"roid": "27", "clientName": "kensville", "vendor": "SNS", "dateCreated": "xx/xx/xxxx"},
            {"roid": "28", "clientName": "Design T-shirt", "vendor": "Nike", "dateCreated": "xx/xx/xxxx"},
        ];
      
      
        vm.note = {
            "notes": "5",
        };
        vm.artwork = {
            "approved": "Approved"

        };
       
//        Datatable Options
        vm.dtOptions = {
            dom: '<"top"f>rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true,
//            scrollY:171
        };
        vm.dtOptionsPurchase = {
            dom: '<"top">rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true,
//            scrollY:103
        };
        vm.assign_item = assign_item;
        function assign_item(item,item_name,item_charge,item_id){
            
            var item_array = {'item':item,'item_name':item_name,'item_charge':item_charge,'item_id':item_id,'order_id':$scope.order_id,'company_id':sessionService.get('company_id')};

            $http.post('api/public/order/addRemoveToFinishing',item_array).success(function(result) {

                if(result.data.success == '1') {
                    $scope.orderDetail();
                }
                else {
                    var data = {"status": "error", "message": result.data.message}
                    notifyService.notify(data.status, data.message);               
                }
                
            });
        }
        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
        vm.dtInstanceCB = dtInstanceCB;
        //methods
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
        }
         function openaddDesignDialog(ev, event_id)
        {
            $mdDialog.show({
                controller: 'AddDesignController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/addDesign/addDesign.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: false,
                locals: {
                    event_id: event_id,
                    event: ev
                 },
                 onRemoving : $scope.designDetail
            });
        }


        function openinformationDialog(ev,order_id,client_id)
        {
            $mdDialog.show({
                controller: 'InformationController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/information/information.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: false,
                locals: {
                    order_id: order_id,
                    client_id: client_id,
                    event: ev
                },
                onRemoving : $scope.orderDetail
            });
        }
        function openaddSplitAffiliateDialog(ev, order)
        {
            $mdDialog.show({
                controller: 'AddSplitAffiliateController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/addSplitAffiliate/addSplitAffiliate.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    Order: order,
                    Orders: vm.orders,
                    event: ev
                  }
            });
        }
        function openApproveOrderDialog(ev,order_number,sns_shipping,client_id) {
            
            if($scope.total_unit > 0)
            {
                $mdDialog.show({
                    controller: 'approveOrderDiallogController',
                    controllerAs: 'vm',
                    templateUrl: 'app/main/order/dialogs/approveorder/approveorder.html',
                    parent: angular.element($document.body),
                    targetEvent: ev,
                    clickOutsideToClose: true,
                    locals: {
                        order_number:order_number,
                        sns_shipping:sns_shipping,
                        client_id:client_id,
                        event: ev
                    }
                });
            }
            else
            {
                var data = {"status": "error", "message": "Please add product to approve order"}
                notifyService.notify(data.status, data.message);
                return false;
            }
        }
        $scope.updateOrderCharge = function(column_name,id,value,table_name,match_condition)
        {
            var position_main_data = {};
            position_main_data.table =table_name;
            $scope.name_filed = column_name;
          
            var obj = {};
            obj[$scope.name_filed] =  value;
            position_main_data.data = angular.copy(obj);

            var condition_obj = {};
            condition_obj[match_condition] =  id;
            position_main_data.cond = angular.copy(condition_obj);
            position_main_data.company_id = sessionService.get('company_id');

            $http.post('api/public/order/updateOrderCharge',position_main_data).success(function(result) {
                $scope.orderDetail();
            });
            /*$http.post('api/public/common/UpdateTableRecords',position_main_data).success(function(result) {
            });*/
        }


        $http.post('api/public/common/getCompanyDetail',sessionService.get('company_id')).success(function(result) {
            if(result.data.success == '1') 
            {
                $scope.allCompanyDetail =result.data.records;
                $scope.oversize = $scope.allCompanyDetail[0].oversize_value;
            } 
            else
            {
                $scope.allCompanyDetail=[];
            }
        });
        
        var timeoutID = window.setTimeout(function zx() {
            var $table = $('table.scrol.table3'),
            $bodyCells = $table.find('tbody tr:first').children(),
            colWidth;
            $(window).resize(function () {
                // Get the tbody columns width array
                colWidth = $bodyCells.map(function () {
                    return $(this).width();
                }).get();

                // Set the width of thead columns
                $table.find('thead tr').children().each(function (i, v) {
                    $(v).width(colWidth[i]);
                });
            }).resize();
        }, 200);
        //Received
        var timeoutID = window.setTimeout(function yx() {
            var $table = $('table.scrol.table4'),
            $bodyCells = $table.find('tbody tr:first').children(),
            colWidth;
            $(window).resize(function () {
                // Get the tbody columns width array
                colWidth = $bodyCells.map(function () {
                    return $(this).width();
                }).get();

                // Set the width of thead columns
                $table.find('thead tr').children().each(function (i, v) {
                    $(v).width(colWidth[i]);
                });
            }).resize();
        }, 300);
        //Affiliate
        var timeoutID = window.setTimeout(function yz() {
            var $table = $('table.scrol.table5'),
                    $bodyCells = $table.find('tbody tr:first').children(),
                    colWidth;
            $(window).resize(function () {
                // Get the tbody columns width array
                colWidth = $bodyCells.map(function () {
                    return $(this).width();
                }).get();

                // Set the width of thead columns
                $table.find('thead tr').children().each(function (i, v) {
                    $(v).width(colWidth[i]);
                });
            }).resize();
        }, 340);

        $scope.printPdf=function()
        {



            if($scope.total_unit > 0)
            {
                var target;
            var form = document.createElement("form");
            form.action = 'api/public/invoice/createInvoicePdf';
            form.method = 'post';
            form.target = target || "_blank";
            form.style.display = 'none';

            var invoice_id = document.createElement('input');
            invoice_id.name = 'invoice_id';
            invoice_id.setAttribute('value', $scope.order.invoice_id);
            form.appendChild(invoice_id);


            var order_id = document.createElement('input');
            order_id.name = 'order_id';
            order_id.setAttribute('value', $scope.order_id);
            form.appendChild(order_id);

            var company_id = document.createElement('input');
            company_id.name = 'company_id';
            company_id.setAttribute('value', sessionService.get('company_id'));
            form.appendChild(company_id);

            var input_pdf = document.createElement('input');
            input_pdf.name = 'pdf_token';
            input_pdf.setAttribute('value', 'pdf_token');
            form.appendChild(input_pdf);

            document.body.appendChild(form);
            form.submit();
            } 
            else
            {
                notifyService.notify('error','Please add atleast one product.');
            }
            
        };

        $scope.openEmailPopup = function (ev,approval) {
    
            if($scope.total_unit > 0)
            {
                $mdDialog.show({
                    controller: 'openEmailController',
                    controllerAs: 'vm',
                    templateUrl: 'app/main/order/views/order-info/send-email.html',
                    parent: angular.element($document.body),
                    targetEvent: ev,
                    clickOutsideToClose: true,
                    locals: {
                        client_id: $scope.order.client_id,
                        order_id: $scope.order_id,
                        display_number: $stateParams.id,
                        paid: $scope.order.is_paid,
                        balance: $scope.order.balance_due,
                        approval: $scope.order.approval,
                        event: ev
                      }
                });
            }
            else
            {
                notifyService.notify('error','Please add atleast one product.');
            }
        };

       $scope.createPO = function() {
            
            var permission = confirm("Do you want to create PO ?");

            if(permission == true){

                var condition_obj = {};
                condition_obj.order_id = $scope.order_id;
                condition_obj.company_id = sessionService.get('company_id');

                $http.post('api/public/purchase/createPO',condition_obj).success(function(result) 
                {
                    if(result.data.success=='1')
                    {
                        $scope.orderDetail();
                        notifyService.notify('success',result.data.message);
                        $scope.order.is_complete = '0';

                    }
                    else
                    {
                        notifyService.notify('error',result.data.message);
                    }
                });
            }
        }

        $scope.updateTax = function()
        {
            var UpdateArray = {};
            UpdateArray.table ='orders';
            UpdateArray.data = {tax_rate:$scope.order.tax_rate};
            UpdateArray.cond = {id:$scope.order_id};

            $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
            {
                $scope.calculateAll($scope.order_id,$scope.company_id);
            });
        }

        $scope.UpdateTableField = function(field_name,field_value,table_name,cond_field,cond_value)
        {
            var vm = this;
            var UpdateArray = {};
            UpdateArray.table =table_name;
            
            $scope.name_filed = field_name;
            var obj = {};
            obj[$scope.name_filed] =  field_value;
            UpdateArray.data = angular.copy(obj);

            var condition_obj = {};
            condition_obj[cond_field] =  cond_value;
            UpdateArray.cond = angular.copy(condition_obj);
            UpdateArray.order_id = $scope.order_id;
            UpdateArray.company_id = sessionService.get('company_id');

            var permission = confirm(AllConstant.deleteMessage);
                if (permission == true)
                {

                $http.post('api/public/order/deleteOrderCommon',UpdateArray).success(function(result) {
                    if(result.data.success=='1')
                    {
                        notifyService.notify('success','Record Deleted Successfully.');
                        $scope.orderDetail();
                        $scope.designDetail();
                    }
                    else
                    {
                        notifyService.notify('error',result.data.message);
                    }
                   });
                 }
        }

        $scope.calculateAll = function(order_id,company_id)
        {
            $("#ajax_loader").show();
            $http.get('api/public/order/calculateAll/'+order_id+'/'+company_id).success(function(result) 
            {
                $("#ajax_loader").hide();
                $scope.orderDetail();
            });
        }
    }
})();
