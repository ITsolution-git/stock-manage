(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('UserProfileController', UserProfileController);

    /** @ngInject */


    function UserProfileController($window, $timeout,$filter,$scope, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService,$log,AllConstant)
    {
      var vm = this;

    vm.openChangePasswordialog = openChangePasswordialog;
    
    $scope.company_id =sessionService.get("company_id");
    $scope.user_id = sessionService.get("user_id");

        $scope.cancel = function () {
            $mdDialog.hide();
        };
        

       $http.get('api/public/client/SelectionData/'+$scope.company_id).success(function(Response) 
        {   
            if(Response.data.success=='1')
            {   
                $scope.states_all   = Response.data.result.state;
                $scope.AllPriceGrid = Response.data.result.AllPriceGrid;
            }
        });


        /**
         * Close dialog
         */
        function closeDialog()
        {
            $mdDialog.hide();
        }


    //console.log($scope.app.company_roleid);
      // GET ADMIN ROLE LIST
      GetCompany();
      function GetCompany () {
      $("#ajax_loader").show();
      $http.get('api/public/admin/company/edit/'+$scope.user_id+'/'+$scope.company_id).success(function(Listdata) 
      {

            if(Listdata.data.success==1)
            {
              $scope.company = Listdata.data.records[0];
              $("#ajax_loader").hide();
            }
            else
            {
              notifyService.notify("error", "Something Wrong, Please try again !");
              $state.go('app.dashboard');
              $("#ajax_loader").hide();
              return false;
            }
              
             // console.log(Listdata); 
      });
    }
  
    // COMPANY EDIT TIME CALL
    $scope.UpdateTableField = function(field_name,field_value,table_name,cond_field,cond_value,extra,param)
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
        UpdateArray.date_field = extra;

            $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
                if(result.data.success=='1')
                {
                   notifyService.notify('success', result.data.message);
                }
                else
                {
                    notifyService.notify('error', result.data.message);
                }
            });
    }                          
                          $scope.checkemail = function () {

                               var mail = $('#comp_email').val();
                               //console.log(mail);
                               if(mail.trim() != '')
                               {
                                 $http.get('api/public/common/checkemail/'+mail+'/'+$scope.user_id).success(function(result, status, headers, config) {
          
                                            if(result.data.success=='2')
                                            {
                                              $("#company_email").hide();
                                              return false;
                                            }
                                            else
                                            {
                                              $("#company_email").val(result.data.message);
                                              $("#company_email").show();
                                              return false;
                                            }
                                       });
                               }
                              }   

        function openChangePasswordialog(ev, settings)
        {
            $("#ajax_loader").show();
            $mdDialog.show({

                controller: function($scope,params){
                                $("#ajax_loader").hide();
                                $scope.changePassword = function (data) 
                                {
                                    $("#ajax_loader").show();
                                    var pass_array={};
                                    pass_array = data;
                                    pass_array.user_id = params.user_id;
                                    //console.log(pass_array); return false;
                                    $http.post('api/public/admin/company/change_password',pass_array).success(function(result) {

                                        if(result.data.success=='1')
                                        {
                                           notifyService.notify('success', result.data.message);
                                           $mdDialog.hide();
                                        }
                                        else
                                        {
                                            notifyService.notify('error', result.data.message);
                                        }
                                        $("#ajax_loader").hide();

                                    });
                                }
                                $scope.cancel = function () {
                                    $mdDialog.hide();
                                };
                },
                controllerAs: 'vm',
                templateUrl: 'app/main/settings/dialogs/changePassword/changePassword-dialog.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    event: ev,
                    params:$scope
                }
                
            });
        }

    }
})();
