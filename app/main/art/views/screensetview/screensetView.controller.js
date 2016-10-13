(function ()
{
    'use strict';

    angular
            .module('app.art')
            .controller('screenSetViewController', screenSetViewController);

    /** @ngInject */
    function screenSetViewController($document,  $state,$window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter)
    {

        var vm = this;
        $scope.company_id = sessionService.get('company_id');

        // Model to JSON for demo purpose
        $scope.$watch('models', function(model) {
            $scope.modelAsJson = angular.toJson(model, true);
        }, true);
       
        $scope.screenset_id = $stateParams.id;
        //console.log($stateParams.id);
        if($stateParams.id=='' || angular.isUndefined($stateParams.id))
        {
            notifyService.notify('error','Invalid Parameters.');
            $state.go('app.art');
            return false;
        }

        // CHECK THIS MODULE ALLOW OR NOT FOR ROLES
        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='SU')
        {
            $scope.allow_access = 0; // OTHER ROLES CAN NOT ALLOW TO EDIT, CAN VIEW ONLY
        }
        else
        {
            $scope.allow_access = 1;  // THESE ROLES CAN ALLOW TO EDIT
        }

          var misc_list_data = {};
        var condition_obj = {};
        condition_obj['company_id'] =  sessionService.get('company_id');
        misc_list_data.cond = angular.copy(condition_obj);

        $http.post('api/public/common/getAllMiscDataWithoutBlank',misc_list_data).success(function(result, status, headers, config) {
                $scope.miscData = result.data.records;
        });


        // INTIAL CALL TO RETRIVE ALL SCREENSET DATA
        $scope.GetOrderScreenSet = function() 
        {
            $("#ajax_loader").show();
            $http.get('api/public/art/GetscreenColor/'+$scope.screenset_id).success(function(result) 
            {
                if(result.data.success == '1') 
                {
                    $scope.ScreenSets = result.data.records;
                    $scope.ScreenSets_new = 
                    {
                        data_all: result.data.records,
                        selected: null,
                    };

                    $scope.getColors = result.data.getColors;
                    $scope.screen_allcolors = result.data.allcolors;
                    
                }
                else
                {
                    notifyService.notify('error',result.data.message);
                    /*$state.go('app.art');
                    return false;*/
                }
                $("#ajax_loader").hide();
            });
        }
        $scope.GetOrderScreenSet(); /// CALL WHEN PAGE LOAD FIRST TIME.

        // DRAG AND DROP FUNCTION CALL WHEN EVEN CALL
        $scope.change_sort = function ()
        {
            if($scope.allow_access==0){return false;}
            $("#ajax_loader").show();
            $http.post('api/public/art/change_sortcolor',$scope.ScreenSets_new.data_all).success(function(result) 
            {
                $scope.GetOrderScreenSet();
            });
            }

        // UPDATE COLOR SCREEN DETAIL
        $scope.UpdateColorScreen = function(ev, colordata) 
        {
            if($scope.allow_access==0){return false;}
                $mdDialog.show({
                    controller: function ($scope, params,colordata)
                                {
                                    //alert(position_id);
                                    $scope.params = params;
                                    $scope.color_screen = colordata;
                                    $scope.ink_array = params.miscData.art_type;
                                    //console.log($scope.color_screen); 
                                    $scope.closeDialog = function() 
                                    {
                                        $mdDialog.hide();
                                    } 
                                    $scope.Savecolor_screen = function (var_all)
                                    {
                                       // console.log(var_all);
                                        $http.post('api/public/art/UpdateColorScreen',var_all).success(function(result) 
                                        {
                                            if(result.data.success == '1') 
                                            {
                                                $scope.closeDialog();
                                                notifyService.notify('success','Screen Updated successfully.');
                                            }
                                            else
                                            {
                                                notifyService.notify('error',result.data.message);
                                            }
                                        });
                                    }

                        },
                    controllerAs: 'vm',
                    templateUrl: 'app/main/art/dialogs/createScreenDetail/createScreenDetail.html',
                    parent: angular.element($document.body),
                    targetEvent: ev,
                    clickOutsideToClose: true,
                    locals: {
                        params:$scope,
                        colordata:colordata,
                        event: ev
                    },
                    onRemoving : $scope.GetOrderScreenSet
                });
        }
        
        // MENO OPTION OPEN CODE
        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
       
    
    // ============= UPLOAD IMAGE ============= // 
        $scope.ImagePopup = function (column_name,folder_name,table_name,default_image,primary_key_name,primary_key_value,image_name,extra_params) 
        {
                if($scope.allow_access==0){return false;}
                $scope.column_name=column_name;
                $scope.table_name=table_name;
                $scope.folder_name=folder_name;
                $scope.primary_key_name=primary_key_name;
                $scope.primary_key_value=primary_key_value;
                $scope.default_image=default_image;
                $scope.unlink_url = image_name;
                $scope.extra_params = extra_params;

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
                                Image_data.image_path = params.company_id+"/"+params.folder_name+"/"+params.extra_params;
                                Image_data.cond = params.primary_key_name;
                                Image_data.value = params.primary_key_value;
                                Image_data.unlink_url = params.unlink_url;
                                //console.log(Image_data); return false;
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
                    onRemoving : $scope.GetOrderScreenSet
                });

        };
    // ============= DELETE IMAGE ============= // 
        $scope.deleteImage=function(column_name,folder_name,table_name,default_image,primary_key_name,primary_key_value,extra_params)
        {
            if(default_image == '') 
            {

                var data = {"status": "error", "message": "Please upload image first."}
                          notifyService.notify(data.status, data.message);
                          return false;
            }
              var permission = confirm(AllConstant.deleteMessage);

            if (permission == true) {

                var image_data = {};
                image_data.table =table_name

                var obj = {};
                obj[column_name] =  '';
                image_data.data = angular.copy(obj);

                var cond_arr = {};
                cond_arr[primary_key_name] =  primary_key_value;
                image_data.cond =angular.copy(cond_arr);


                image_data.image_delete =  $scope.company_id+'/'+folder_name+'/' + extra_params +'/'+default_image;
            
                $http.post('api/public/common/deleteImage',image_data).success(function(result) 
                {

                    if(result.data.success=='1')
                    {
                        notifyService.notify("success", result.data.message);
                        $scope.GetOrderScreenSet();
                    }
                    else
                    {
                        notifyService.notify("error", result.data.message); 
                    }
 
                });
            }
        }
        $scope.printPdf=function()
        {
            if($scope.allow_access==0){return false;}
            var pass_array = {order_id:$scope.ScreenSets[0].order_id,company_id:$scope.company_id,screen_id:$scope.screenset_id }
            var target;
            var form = document.createElement("form");
            form.action = 'api/public/art/PressInstructionPDF';
            form.method = 'post';
            form.target = target || "_blank";
            form.style.display = 'none';

            var input_screenset = document.createElement('input');
            input_screenset.name = 'art';
            input_screenset.setAttribute('value', JSON.stringify(pass_array));
            form.appendChild(input_screenset);

            document.body.appendChild(form);
            form.submit();  
        };
        $scope.UpdateTableField = function(field_value)
        {
            if($scope.allow_access==0){return false;}
            var vm = this;
            var UpdateArray = {};
            UpdateArray.table ='artjob_screensets';
            UpdateArray.data = {approval:field_value};
            UpdateArray.cond = {id:$scope.screenset_id};
            $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
            {
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
        

}
})();
