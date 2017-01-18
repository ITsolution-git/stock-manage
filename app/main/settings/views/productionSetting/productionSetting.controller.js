(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('productionSettingScreen', productionSettingScreen);
            

    /** @ngInject */
    function productionSettingScreen($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {
        
        var vm = this;
        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
        $scope.company_id = sessionService.get('company_id');

        // CHECK THIS MODULE ALLOW OR NOT FOR ROLES
        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='CA' || $scope.role_slug=='AM' || $scope.role_slug=='FM' || $scope.role_slug=='PU' )
        {
            $scope.allow_access = 1;  // THESE ROLES CAN ALLOW TO EDIT
        }
        else
        {
            $scope.allow_access = 1; // CAN BE EDIT BY ANYONE
        }        


                /* TESTY PAGINATION */     
        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };

        $scope.filterBy = {
          'search': '',
          'name': '',
          'function': 'machine_list'
        };
        $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
        };

       $scope.getResource = function (params, paramsObj, search)
        {   
            $scope.params = params;
            $scope.params.company_id = $scope.company_id;
            $scope.params.machine_type = 0; // 0 ScreenPrint
            $scope.paramsObj = paramsObj;

            var company_data = {};
            company_data.cond ={params:$scope.params};

            $("#ajax_loader").show();     
           return $http.post('api/public/common/getTestyRecords',company_data).success(function(result) 
            {
                $("#ajax_loader").hide();
                $scope.success  = result.success;
                if(result.success=='1')
                {
                    return {
                      'rows': result.rows,
                      'header': result.header,
                      'pagination': result.pagination,
                      'sortBy': result.sortBy,
                      'sortOrder': result.sortOrder
                    }
                }
                else
                {
                    notifyService.notify('error',result.message);
                }
                
            });
        }
        
        $scope.Machine = function (ev,id,action)
        {
                $mdDialog.show({
                controller: function($scope,params){
                   
                    $scope.params = params;
                    $scope.machine_id = id;
                    $scope.company_id = params.company_id;
                    $scope.allow_access = params.allow_access;
                    $scope.states_all = params.states_all;
                    $scope.removeOS = [];
                    $scope.removeIPH = [];
                    $scope.alliphData = [];
                    $scope.allfactorData = [];
                    $scope.action = action;
                    
                    $scope.addIPHData = function()
                    {
                        $scope.alliphData.push({value:'',pos_no:''});
                    }
                    $scope.addOSData = function()
                    {
                        $scope.allfactorData.push({order_size:'',factor:''});
                    }
                    $scope.remove_IPH = function(index,id)
                    {
                        $scope.alliphData.splice(index,1);
                        if(!angular.isUndefined(id))
                        {
                            $scope.removeIPH.push(id);
                        }
                    }
                    $scope.remove_OS = function(index,id)
                    {
                        $scope.allfactorData.splice(index,1);
                        if(!angular.isUndefined(id))
                        {
                            $scope.removeOS.push(id);
                        }
                    }
    

                    $scope.alliphDataAll =  function() {

                            var allData = {};
                            allData.table ='iph';
                            allData.sort ='pos_no';
                            allData.sortcond ='asc';
                            allData.cond ={is_delete:1,status:1,company_id:sessionService.get('company_id'),machine_id:$scope.machine_id}

                            $http.post('api/public/common/GetTableRecords',allData).success(function(result)
                            {   
                                if(result.data.success=='1')
                                {   
                                    $scope.alliphData = result.data.records;
                                }   
                            });
                    } 
                    $scope.allOrderSizeFactor =  function() 
                    {
                            var allData = {};
                            allData.table ='order_size_factor';
                            allData.sort ='order_size';
                            allData.sortcond ='asc';
                            allData.cond ={is_delete:1,status:1,company_id:sessionService.get('company_id'),machine_id:$scope.machine_id}

                            $http.post('api/public/common/GetTableRecords',allData).success(function(result)
                            {   
                                if(result.data.success=='1')
                                {   
                                    $scope.allfactorData = result.data.records;
                                } 
                            });
                    }  
                    if(action=='edit')
                    {
                        var companyData = {};
                        companyData.table ='machine';
                        companyData.cond = {id:id};
                        // GET CLIENT TABLE CALL
                        $http.post('api/public/common/GetTableRecords',companyData).success(function(result) 
                        {   
                            if(result.data.success=='1')
                            {  
                                $scope.machine = result.data.records[0];
                                $scope.machine.run_rate = $scope.machine.run_rate*100;
                                $scope.machine.setup_time = $scope.machine.setup_time*60;
                            }
                            else
                            {
                                notifyService.notify( "error", result.data.message);
                            }
                            $("#ajax_loader").hide();
                        });
                        $scope.alliphDataAll();
                        $scope.allOrderSizeFactor();
                    }                       


                    $scope.UpdateMachineData = function(machine,alliphData,allfactorData){
                            
                        var MachineArray = {};
                        MachineArray.machine_id=$scope.machine_id;
                        MachineArray.company_id=$scope.company_id;
                        MachineArray.machineData=machine;
                        MachineArray.allfactorData=allfactorData;
                        MachineArray.alliphData=alliphData;
                        MachineArray.removeOS=$scope.removeOS;
                        MachineArray.removeIPH=$scope.removeIPH;
                        MachineArray.action=$scope.action;

                        //$("#ajax_loader").show();
                        $http.post('api/public/production/UpdateMachineRecords',MachineArray).success(function(result) {
                            if(result.data.success=='1')
                            {
                                notifyService.notify('success', result.data.message);
                                $scope.closeDialog();
                            }
                            else
                            {
                                notifyService.notify('error', result.data.message);
                                $scope.closeDialog();
                            }
                            $("#ajax_loader").hide();
                        });
                    }
                    $scope.closeDialog = function() 
                    {
                        $mdDialog.hide();
                    } 

                },
                templateUrl: 'app/main/settings/dialogs/machine/editmachine.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    params:$scope,
                    event: ev
                },
                onRemoving : $scope.reloadCallback
            });
        }
        $scope.deleteMachine = function (ev,id)
        {
            var UpdateArray = {};
            UpdateArray.table ='machine';
            UpdateArray.data = {is_delete:0};
            UpdateArray.cond = {id: id};
            
            var permission = confirm(AllConstant.deleteMessage);
            if (permission == true) 
            {
                $("#ajax_loader").show();
                $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
                    if(result.data.success=='1')
                    {
                        notifyService.notify('success', result.data.message);
                        //$scope.reloadCallback();
                    }
                    else
                    {
                        notifyService.notify('error', result.data.message);
                    }
                    $("#ajax_loader").hide();
                });
            }
        }
        $scope.changeStatus = function(status,id)
        {
            var UpdateArray = {};
            UpdateArray.table ='machine';
            UpdateArray.data = {operation_status:status};
            UpdateArray.cond = {id: id};
            
            $("#ajax_loader").show();
            $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
                if(result.data.success=='1')
                {
                    notifyService.notify('success', result.data.message);
                }
                else
                {
                    notifyService.notify('error', result.data.message);
                }
                $("#ajax_loader").hide();
            });
        }


        $scope.GetShiftList= function()
        {
            var shiftData = {};
            shiftData.table ='labor';
            shiftData.cond = {company_id:$scope.company_id,is_delete:1,shift_type:0};
            // GET CLIENT TABLE CALL
            $http.post('api/public/common/GetTableRecords',shiftData).success(function(result) 
            {   
                if(result.data.success=='1')
                {  
                    $scope.shift_data = result.data.records;
                }
                else
                {
                    notifyService.notify( "error", result.data.message);
                }
                $("#ajax_loader").hide();
            });
        }
        $scope.openaddLaborDialog= function(ev,labor_id)
        {
            $mdDialog.show({
                controller: 'AddLaborController',
                controllerAs: 'vm',
                templateUrl: 'app/main/settings/dialogs/labor/addlabor.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: false,
                locals: {
                    labor_id: labor_id,
                    event: ev
                 },
                 onRemoving : $scope.GetShiftList
            });
        }
        $scope.GetShiftList();

         $scope.delete_labor = function (ev,id)
        {
            var UpdateArray = {};
            UpdateArray.table ='labor';
            UpdateArray.data = {is_delete:0};
            UpdateArray.cond = {id: id};
            
            var permission = confirm(AllConstant.deleteMessage);
            if (permission == true) 
            {
                $("#ajax_loader").show();
                $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
                    if(result.data.success=='1')
                    {
                        notifyService.notify('success', "Record Deleted Successfully.");
                        $scope.GetShiftList();
                    }
                    else
                    {
                        notifyService.notify('error', result.data.message);
                    }
                    $("#ajax_loader").hide();
                });
            }
        }


        
    
    }
    
})();


