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
        $scope.valid_phone = AllConstant.VALID_PHONE;
      var vm = this;

    vm.openChangePasswordialog = openChangePasswordialog;
    
    $scope.company_id =sessionService.get("company_id");
    $scope.user_id = sessionService.get("user_id");
    $scope.role_slug = sessionService.get('role_slug');

    //console.log($state.current.url);
    if($state.current.url=='/userProfile')
    {
        $scope.profile_id = $scope.user_id;
        $scope.allow_access = 1;
    }
    else
    {
        $scope.profile_id = $scope.company_id;
        if($scope.role_slug=='CA')
        {
            $scope.allow_access = 1;
        }
        else
        {
            $scope.allow_access = 0;
        }
    }
    


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
      $http.get('api/public/admin/company/edit/'+$scope.profile_id+'/'+$scope.company_id).success(function(Listdata) 
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
    $scope.UpdateTableField = function(field_name,field_value,table_name,cond_field,cond_value,extra,param,validation)
    {
        console.log(field_name); console.log(field_value);
        console.log(Object.keys(validation).length);
        
        if($scope.allow_access=='0')
        {
            notifyService.notify('error','You have no rights to Edit.');
            return false;
        }
        if(!angular.isUndefined(validation) && Object.keys(validation).length>0 )
        {
            notifyService.notify('error','Please enter valid Input.');
            return false;
        }
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
               //notifyService.notify('error', result.data.message);
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
        $scope.onLoad=function()
        {
            $scope.showtcprofileimg = true;
        }; 
        // ============= UPLOAD IMAGE ============= // 
        $scope.ImagePopup = function (column_name,folder_name,table_name,default_image,primary_key_name,primary_key_value,image_name,is_drag,drag_image) 
        {

                $scope.column_name=column_name;
                $scope.table_name=table_name;
                $scope.folder_name=folder_name;
                $scope.primary_key_name=primary_key_name;
                $scope.primary_key_value=primary_key_value;
                $scope.default_image=default_image;
                $scope.unlink_url = image_name;
                $scope.is_drag = is_drag;
                $scope.drag_image = drag_image;
                ///console.log(drag_image); return false;
                if(drag_image!=null)
                {
                    $mdDialog.show({
                       //controllerAs: $scope,
                        controller: function($scope,params){
                                $scope.params = params;
                                $scope.logo_image = 'ok';
                                if($scope.params.is_drag=='image_drag' && $scope.params.drag_image!=null)
                                {
                                    $scope.logo_image = $scope.params.drag_image[0];
                                }
                                
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
                                            // IF PROFILE IMAGE CHANGED, SET API CALL.
                                            if(params.column_name=="profile_photo")
                                            {
                                                sessionService.AccessService('ALL','true','1');
                                            }
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
                        templateUrl: 'app/main/image/'+is_drag+'.html',
                        parent: angular.element($document.body),
                        clickOutsideToClose: false,
                            locals: {
                                params:$scope
                            },
                        onRemoving :  $scope.GetCompany
                    });
                }

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
                    if(result.data.success=='1')
                    {
                        notifyService.notify('success', result.data.message);
                    }
                    else if(result.data.success=='2')
                    {
                        notifyService.notify('error', result.data.message);
                    }
                    else
                    {
                        notifyService.notify('error',"Please connect to quickbook first");
                    }
                   });
            }

    }
})();
