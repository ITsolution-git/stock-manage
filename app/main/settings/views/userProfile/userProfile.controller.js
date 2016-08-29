(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('UserProfileController', UserProfileController);

    /** @ngInject */


    function UserProfileController($window, $timeout,$filter,$scope, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService,$log,AllConstant)
    {
        $scope.NoImage = AllConstant.NoImage;
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
     
      $scope.GetCompany =  function() {
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


   $scope.GetCompany();
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
        
        if(param=='check_email')
        {
            var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;  
            if(!emailReg.test(field_value) || field_value.trim() == '')
            {  
               return false;
            }
            else
            {
                 $http.get('api/public/common/checkemail/'+field_value+'/'+$scope.user_id).success(function(result, status, headers, config) {
          
                    if(result.data.success=='2')
                    {
                      //return false;
                    }
                    else
                    {
                       notifyService.notify('error', "Email exist, Please choose different email address.");
                       return false;
                    }
                });
            }    
        }

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
           if(mail.trim() != '')
           {
               $http.get('api/public/common/checkemail/'+mail+'/'+$scope.user_id).success(function(result, status, headers, config) 
               {
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
                                $scope.closeDialog = function () {
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
        // ============= UPLOAD IMAGE ============= // 
        $scope.ImagePopup = function (column_name,folder_name,table_name,default_image,primary_key_name,primary_key_value,image_name) 
        {

                $scope.column_name=column_name;
                $scope.table_name=table_name;
                $scope.folder_name=folder_name;
                $scope.primary_key_name=primary_key_name;
                $scope.primary_key_value=primary_key_value;
                $scope.default_image=default_image;
                $scope.unlink_url = image_name;
                //console.log(primary_key_value); return false;
                $mdDialog.show({
                   //controllerAs: $scope,
                    controller: function($scope,params){
                            $scope.params = params;
                            $scope.SaveImageAll=function(image_array)
                            {
                                if(image_array == null)
                                {
                                    $mdDialog.hide();
                                    return false;
                                }

                                var Image_data = {};
                                Image_data.image_array = image_array;
                                Image_data.field = params.column_name;
                                Image_data.table = params.table_name;
                                Image_data.image_name = params.table_name+"-logo";
                                Image_data.image_path = params.company_id+"/"+params.folder_name+"/"+params.primary_key_value;
                                Image_data.cond = params.primary_key_name;
                                Image_data.value = params.primary_key_value;
                                Image_data.unlink_url = params.unlink_url;

                                $http.post('api/public/common/SaveImage',Image_data).success(function(result) {
                                    if(result.data.success=='1')
                                    {
                                        notifyService.notify("success", result.data.message);
                                        $mdDialog.hide();
                                    }
                                    else
                                    {
                                        notifyService.notify("error", result.data.message); 
                                    }
                                });
                            };
                            $scope.showtcprofileimg = false;
                            $scope.onLoad=function()
                                {
                                    $scope.showtcprofileimg = true;
                                }; 
                            $scope.removeProfileImage=function()
                                {
                                    $scope.showtcprofileimg = false;
                                }; 
                            $scope.closeDialog = function() 
                            {
                                $mdDialog.hide();
                            } 
                        },
                    templateUrl: 'app/main/image/image.html',
                    parent: angular.element($document.body),
                    clickOutsideToClose: false,
                        locals: {
                            params:$scope
                        },
                    onRemoving :  $scope.GetCompany
                });

        };

           $scope.qbClientSetup = function(){

                $("#ajax_loader").show();
                var companyId = {};

                companyId ={company_id :sessionService.get('company_id')};

                $http.post('api/public/common/AddEditClient',companyId).success(function(result) {
                    $("#ajax_loader").hide();
                            if(result != '0')
                            {
                                notifyService.notify('success',"Client Sync successfully");   
                            }
                            else
                            {
                                notifyService.notify('error',"Please connect to quickbook first");
                            }

                           

                           });
            }

            $scope.qbUpdateInovice = function(){
                $("#ajax_loader").show();
                var company_id = {};

                company_id ={company_id :sessionService.get('company_id')};

                $http.post('api/public/qbo/updateInvoicePayment',company_id).success(function(result) {
                $("#ajax_loader").hide();
                    if(result != '0')
                    {
                        notifyService.notify('success',"Invoice Payments Sync successfully");   
                    }
                    else
                    {
                        notifyService.notify('error',"Please connect to quickbook first");
                    }
                   });
            }

    }
})();
