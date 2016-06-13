(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('AffiliateController', AffiliateController);
            

    /** @ngInject */
    function AffiliateController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter)
    {
            $scope.company_id = sessionService.get('company_id');
            var originatorEv;
            var vm = this ;

            vm.deleteAffiliate = deleteAffiliate ;
            vm.uploadCSV = uploadCSV ;
            vm.addAffiliate = addAffiliate ;
            vm.editAffiliate = editAffiliate ;

            vm.openMenu = function ($mdOpenMenu, ev) {
                originatorEv = ev;
                $mdOpenMenu(ev);
            };

            $scope.getAffiliate = function (){
                    $("#ajax_loader").show();
                $http.get('api/public/admin/company/getAffiliate/'+$scope.company_id+"/0").success(function(result) 
                    {

                        if(result.data.success=='1')
                        {
                            $scope.affilate_array = result.data.data;
                        }
                        else
                        {
                            notifyService.notify("error", result.data.message); 
                        }
                        $("#ajax_loader").hide();
     
                    });
            }

            $http.get('api/public/client/SelectionData/'+$scope.company_id).success(function(Response) 
            {   
                if(Response.data.success=='1')
                {   
                    $scope.states_all  = Response.data.result.state;
                    $scope.AllPriceGrid=Response.data.result.AllPriceGrid;
                }
            });


            $scope.getAffiliate();
            function deleteAffiliate(ev, settings)
            {
                $mdDialog.show({
                    controller: 'DeleteAffiliateDialogController',
                    controllerAs: 'vm',
                    templateUrl: 'app/main/settings/dialogs/deleteAffiliate/deleteAffiliate-dialog.html',
                    parent: angular.element($document.body),
                    targetEvent: ev,
                    clickOutsideToClose: true,
                    locals: {
                        event: ev
                    }
                });
            }

            function addAffiliate(ev)
            {
                $("#ajax_loader").show();
                $mdDialog.show({
                    controller: function ($scope, params){
                        $scope.params = params 
                        $scope.states_all  = $scope.params.states_all;
                        $scope.AllPriceGrid= $scope.params.AllPriceGrid;
                        $("#ajax_loader").hide();
                        $scope.affiliate = {};
                        $scope.affiliate.status='1';
                        $scope.addAffilite = function (affiliate)
                        {
                            $("#ajax_loader").show();
                            affiliate.company_id = $scope.params.company_id;
                           // console.log(affiliate); return false;
                            $http.post('api/public/admin/company/addAffilite',affiliate).success(function(result) 
                            {   
                                if(result.data.success=='1')
                                {   
                                    notifyService.notify("success", result.data.message);
                                    $mdDialog.hide();                                
                                }
                                else
                                {
                                    notifyService.notify("error", result.data.message); 
                                }
                                $("#ajax_loader").hide();
                            });
                        }
                        $scope.closeDialog = function() 
                        {
                            $mdDialog.hide();
                        }    
                    },
                    controllerAs: 'vm',
                    templateUrl: 'app/main/settings/dialogs/addAffiliate/addAffiliate-dialog.html',
                    parent: angular.element($document.body),
                    targetEvent: ev,
                    clickOutsideToClose: true,
                    locals: {
                        params:$scope,
                        event: ev
                    },
                    onRemoving : $scope.getAffiliate
                });
            }
// ============= REMOVE TABLE RECORD WITH CONDITION ============= // 
        $scope.RemoveFields = function(table,cond_field,cond_value){
              
                var delete_data = {};
                
                $scope.name_filed = cond_field;
                var obj = {};
                obj[$scope.name_filed] =  cond_value;
                delete_data.cond = angular.copy(obj);
                
                delete_data.table =table;
                var permission = confirm("Are you sure to delete this Record ?");
                if (permission == true) 
                {
                    $("#ajax_loader").show();
                    $http.post('api/public/common/DeleteTableRecords',delete_data).success(function(result) 
                    {
                        if(result.data.success=='1')
                        {
                            notifyService.notify('success',result.data.message);
                            $scope.getAffiliate();
                        }
                        else
                        {
                             notifyService.notify('error',result.data.message);
                        }
                        $("#ajax_loader").hide();
                    });
                }
            }


            function uploadCSV(ev, settings)
            {
                $mdDialog.show({
                    controller: 'UploadCSVDialogController',
                    controllerAs: 'vm',
                    templateUrl: 'app/main/settings/dialogs/uploadCSV/uploadCSV-dialog.html',
                    parent: angular.element($document.body),
                    targetEvent: ev,
                    clickOutsideToClose: true,
                    locals: {
                        event: ev
                    }
                });
            }

            function editAffiliate(ev, aff_id)
            {
                $("#ajax_loader").show();
                $mdDialog.show({
                    controller: function ($scope, params,aff_id){
                        $scope.params = params 
                        $scope.states_all  = $scope.params.states_all;
                        $scope.AllPriceGrid= $scope.params.AllPriceGrid;
                        $scope.aff_id = aff_id;
                        $http.get('api/public/admin/company/getAffiliate/'+$scope.params.company_id+"/"+$scope.aff_id).success(function(result) 
                        {
                            if(result.data.success=='1')
                            {   
                                $scope.affiliate = result.data.data[0];                                
                            }
                            else
                            {
                                notifyService.notify("error", result.data.message); 
                            }
                            $("#ajax_loader").hide();
                        });

                        $scope.SaveAffilite = function (affiliate)
                        {
                            $("#ajax_loader").show();
                            affiliate.company_id = $scope.params.company_id;
                           // console.log(affiliate); return false;
                            $http.post('api/public/admin/company/UpdateAffilite',affiliate).success(function(result) 
                            {   
                                if(result.data.success=='1')
                                {   
                                    notifyService.notify("success", result.data.message);
                                    $mdDialog.hide();
                                }
                                else
                                {
                                    notifyService.notify("error", result.data.message); 
                                }
                                $("#ajax_loader").hide();
                            });
                        }
                        $scope.closeDialog = function() 
                        {
                            $mdDialog.hide();
                        }    
                    },
                    controllerAs: 'vm',
                    templateUrl: 'app/main/settings/dialogs/editAffiliate/editAffiliate-dialog.html',
                    parent: angular.element($document.body),
                    targetEvent: ev,
                    clickOutsideToClose: true,
                    locals: {
                        aff_id:aff_id,
                        params:$scope,
                        event: ev
                    },
                    onRemoving : $scope.getAffiliate
                });
            }
    }


       
})();
