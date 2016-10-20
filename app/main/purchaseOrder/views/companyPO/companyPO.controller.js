(function ()
{
    'use strict';

    angular
            .module('app.purchaseOrder')
            .controller('CompanyPOController', CompanyPOController);

    /** @ngInject */
    function CompanyPOController($document, $window,$state, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter)
    {
        var vm = this;
        $scope.display_number = $stateParams.id;
        $scope.company_id = sessionService.get('company_id');

        var misc_list_data = {};
        var condition_obj = {};
        condition_obj['company_id'] =  sessionService.get('company_id');
        misc_list_data.cond = angular.copy(condition_obj);

        $http.post('api/public/common/getAllMiscDataWithoutBlank',misc_list_data).success(function(result, status, headers, config) {
                $scope.miscData = result.data.records;
        });

        $scope.GetPodata = function ()
        {
            $("#ajax_loader").show();
            $http.get('api/public/purchase/GetPodata/'+$scope.display_number+'/'+$scope.company_id).success(function(result) 
            {
                $("#ajax_loader").hide();
                if(result.data.success=='1')
                {
                    $scope.po_data = result.data.records.po_data;
                    $scope.po_id = $scope.po_data.po_id;
                    $scope.poline = result.data.records.poline;
                    $scope.order_total = result.data.records.order_total[0];
                }
                else
                {
                    notifyService.notify('error',result.data.message);
                    $state.go('app.purchaseOrder');
                    return false;
                }
                
            });
        }
        $scope.GetPodata();

        $scope.changeLinedata = function (line_id,qnty_ordered,unit_price,qnty,ev)
        {
        

            $("#ajax_loader").show();
            $mdDialog.show({
                controllerAs: $scope,
                controller:function ($scope, params)
                {
                    $scope.params = params;
                    
                    $scope.line_data = {};
                    $scope.line_data.line_id=line_id;
                    $scope.line_data.qnty_ordered=qnty_ordered;
                    $scope.line_data.unit_price=unit_price;
                    $scope.line_data.qnty=qnty;

                    $("#ajax_loader").hide();
                    $scope.saveLinedata = function (line_data)
                    {
                       // $("#ajax_loader").show();
                        //console.log(line_data.qnty);
                        //console.log(line_data.qnty_ordered);
                        if(parseInt(line_data.qnty_ordered) > parseInt(line_data.qnty))
                        {
                            notifyService.notify('error',"More then "+line_data.qnty+" quantity not allow.");
                            return false;

                        }
                        var purchase_array = {};
                        purchase_array.qnty_ordered = line_data.qnty_ordered;
                        purchase_array.unit_price = line_data.unit_price;
                        purchase_array.po_id = $scope.params.po_id;
                        purchase_array.id =  line_data.line_id;
                       // console.log(purchase_array);
                        $http.post('api/public/purchase/EditOrderLine',purchase_array).success(function(result) 
                        {
                            $("#ajax_loader").hide();
                            if(result.data.success=='1')
                            {
                               notifyService.notify('success',result.data.message);
                               //$mdDialog.hide();
                            }
                            else
                            {
                                notifyService.notify('error',result.data.message);
                            }
                        });
                    }
                    $scope.closeDialog = function() 
                    {
                        $mdDialog.hide();
                    } 
                },
                templateUrl: 'app/main/purchaseOrder/dialogs/purchaseline/changeLinedata.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: false,
                    locals: {
                        params:$scope,
                        event: ev
                    },
                onRemoving : $scope.GetPodata
            });
        }
        $scope.changeDropship = function (vendor_instruction,ev)
        {
        
            $("#ajax_loader").show();
            $mdDialog.show({
                controllerAs: $scope,
                controller:function ($scope, params)
                {
                    $scope.params = params;
                    $scope.po_id = $scope.params.po_id;
                    $scope.vendor_instruction = vendor_instruction;
                    $("#ajax_loader").hide();
                    $scope.SaveRecords = function(vendor_instruction){
                            
                        var UpdateArray = {};
                        UpdateArray.table ='purchase_order';
                        UpdateArray.data = {vendor_instruction:vendor_instruction};
                        UpdateArray.cond = {po_id: $scope.po_id};

                        $("#ajax_loader").show();
                        $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
                        {
                            if(result.data.success=='1')
                            {
                                notifyService.notify('success', result.data.message);
                               // $scope.closeDialog();
                            }
                            else
                            {
                                notifyService.notify('error', result.data.message);
                            }
                            $("#ajax_loader").hide();
                        });
                    } 
                    $scope.closeDialog = function() 
                    {
                        $mdDialog.hide();
                    } 
                },
                templateUrl: 'app/main/purchaseOrder/dialogs/purchaseline/Dropship_instruction.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: false,
                    locals: {
                        params:$scope,
                        event: ev
                    },
                onRemoving : $scope.GetPodata
            });
        }
        $scope.changeVendorcharge = function (vendor_charge,ev)
        {
        
            $("#ajax_loader").show();
            $mdDialog.show({
                controllerAs: $scope,
                controller:function ($scope, params)
                {
                    $scope.params = params;
                    $scope.po_id = $scope.params.po_id;
                    $scope.vendor_charge = vendor_charge;
                    $("#ajax_loader").hide();
                    $scope.SaveRecords = function(vendor_charge){
                            
                        var UpdateArray = {};
                        UpdateArray.table ='purchase_order';
                        UpdateArray.data = {vendor_charge:vendor_charge};
                        UpdateArray.cond = {po_id: $scope.po_id};

                        $("#ajax_loader").show();
                        $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
                        {
                            if(result.data.success=='1')
                            {
                                notifyService.notify('success', result.data.message);
                               // $scope.closeDialog();
                            }
                            else
                            {
                                notifyService.notify('error', result.data.message);
                            }
                            $("#ajax_loader").hide();
                        });
                    } 
                    $scope.closeDialog = function() 
                    {
                        $mdDialog.hide();
                    } 
                },
                templateUrl: 'app/main/purchaseOrder/dialogs/purchaseline/VendorCharge.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: false,
                    locals: {
                        params:$scope,
                        event: ev
                    },
                onRemoving : $scope.GetPodata
            });
        }
        
        
        $scope.changePoData = function (ev)
        {
        

            $("#ajax_loader").show();
            $mdDialog.show({
                controllerAs: $scope,
                controller:function ($scope, params)
                {
                    $scope.params = params;
                    
                    $scope.main_po =  $scope.params.po_data;
                   // console.log($scope.main_po);

                    var vendor = {};
                    vendor.table ='vendors';
                    vendor.cond ={company_id:$scope.params.company_id,company_id:'0'};
                    vendor.sort='name_company';
                    vendor.sortcond='asc';

                    $http.post('api/public/common/GetTableRecords',vendor).success(function(result) 
                    {   
                        if(result.data.success=='1')
                        {   
                            $scope.vendor_all = result.data.records;
                        }
                    });

                    $("#ajax_loader").hide();
                    $scope.UpdateTableField = function(field_name,field_value,extra)
                    {
                        var UpdateArray = {};

                        UpdateArray.table ='purchase_order';

                        var condition_obj = {};
                        condition_obj[field_name] =  field_value;
                        UpdateArray.data = angular.copy(condition_obj);

                        UpdateArray.cond = {po_id: $scope.params.po_id};
                        UpdateArray.date_field = extra;
                        $("#ajax_loader").show();
                       // console.log(UpdateArray); return false;
                        $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
                            if(result.data.success=='1')
                            {
                                notifyService.notify('success', result.data.message);
                                //$scope.closeDialog();
                            }
                            else
                            {
                                notifyService.notify('error', result.data.message);
                               // $scope.closeDialog();
                            }
                            $("#ajax_loader").hide();
                        });
                    }
                    $scope.closeDialog = function() 
                    {
                        $mdDialog.hide();
                    } 
                },
                templateUrl: 'app/main/purchaseOrder/dialogs/purchaseline/podata.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: false,
                    locals: {
                        params:$scope,
                        event: ev
                    },
                onRemoving : $scope.GetPodata
            });
        }   
        

        vm.dtOptions = {
            dom: '<"top"f>rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };
         $scope.CreateRo = function(po_id,extra)
         {
            if(extra=='0')
            {
                var UpdateArray = {};
                UpdateArray.table ='purchase_order';
                UpdateArray.data = {complete:'1'};
                UpdateArray.cond = {po_id: $scope.po_id};

                $("#ajax_loader").show();
                $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
                {
                    if(result.data.success=='1')
                    {
                        notifyService.notify('success', 'Receiving PO created.');
                        //$state.go('app.receiving.receivingInfo({id:po_id})');
                        $state.go('app.receiving.receivingInfo',{id: $scope.display_number});
                    }
                    else
                    {
                        notifyService.notify('error', result.data.message);
                    }
                    $("#ajax_loader").hide();
                });
            }
            else
            {
                $state.go('app.receiving.receivingInfo',{id: $scope.display_number});
            }
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
    }
})();
