(function ()
{
    'use strict';

    angular
            .module('app.receiving')
            .controller('ReceivingInfoController', ReceivingInfoController);
            

    /** @ngInject */
    function ReceivingInfoController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService)
    {

    	var vm = this;
         var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
        vm.openReceivingInformationDialog = openReceivingInformationDialog;
        $scope.po_id = $stateParams.id;
        $scope.company_id = sessionService.get('company_id');

        var misc_list_data = {};
        var condition_obj = {};
        condition_obj['company_id'] =  sessionService.get('company_id');
        misc_list_data.cond = angular.copy(condition_obj);

        $http.post('api/public/common/getAllMiscDataWithoutBlank',misc_list_data).success(function(result, status, headers, config) {
                $scope.miscData = result.data.records;
        });
        
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

        $scope.GetPodata = function ()
        {
            $("#ajax_loader").show();
            $http.get('api/public/purchase/GetPoReceived/'+$scope.po_id+'/'+$scope.company_id).success(function(result) 
            {
                $("#ajax_loader").hide();
                if(result.data.success=='1')
                {
                    $scope.po_data = result.data.records.po_data;
                    $scope.poline = result.data.records.receive;
                    $scope.order_total = result.data.order_total[0];
                    if($scope.po_data.complete=='0')
                    {
                        notifyService.notify('error','Receive order is not created yet.');
                        $state.go('app.purchaseOrder.companyPO',{id: $scope.po_data.po_id});
                        return false;
                    }
                }
                else
                {
                    notifyService.notify('error',result.data.message);
                    $state.go('app.receiving');
                    return false;
                }
            });
        }
        $scope.GetPodata();
        $scope.UpdateTableField = function(field_name,field_value,id,original,extra,param)
                    {
                        if($scope.po_data.is_complete=='1')
                        {
                            notifyService.notify('error', 'Receive order is locked, Changes not accecptable.');
                            return false;
                        }
                        if(extra=='short')
                        {
                            if(parseInt(field_value)>parseInt(original))
                            {
                                notifyService.notify('error', 'Defective quantity should not be more then Received quantity');
                                return false;
                            }
                        }
                        if(extra=='received')
                        {
                            if(parseInt(field_value)>parseInt(original))
                            {
                                notifyService.notify('error', 'Received quantity should not be more then Ordered quantity');
                                return false;
                            }
                            if(parseInt(param)>parseInt(field_value))
                            {
                                notifyService.notify('error', 'Defective quantity should not be more then Received quantity');
                                return false;
                            }
                        }

                        var UpdateArray = {};

                        UpdateArray.table ='purchase_order_line';

                        var condition_obj = {};
                        condition_obj[field_name] =  field_value;
                        UpdateArray.data = angular.copy(condition_obj);

                        UpdateArray.cond = {id: id};
                        UpdateArray.date_field = extra;
                        //$("#ajax_loader").show();
                       // console.log(UpdateArray); return false;
                        $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
                            if(result.data.success=='1')
                            {
                                notifyService.notify('success', result.data.message);
                                $scope.GetPodata();
                            }
                            else
                            {
                                notifyService.notify('error', result.data.message);
                            }
                        });
                    }
        function openReceivingInformationDialog(ev,order_id)
        {
            $mdDialog.show({
                controller: 'InformationController',
                controllerAs: 'vm',
                templateUrl: 'app/main/receiving/dialogs/information/information.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: false,
                locals: {
                    order_id: order_id,
                    event: ev
                },
                onRemoving : $scope.orderDetail
            });
        }

        $scope.changeReceiveData = function (ev)
        {
        

            $("#ajax_loader").show();
            $mdDialog.show({
                controllerAs: $scope,
                controller:function ($scope, params)
                {
                    $scope.params = params;
                    
                    $scope.main_po =  $scope.params.po_data;

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
                templateUrl: 'app/main/receiving/dialogs/receivedata.html',
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
    }
    
    
})();
