(function ()
{
    'use strict';

    angular
            .module('app.art')
            .controller('orderViewController', orderViewController);

    /** @ngInject */
    function orderViewController($document,  $state,$window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter)
    {
        var vm = this;
        //vm.createNewScreen = createNewScreen;
        vm.generateArtForm = generateArtForm;
        //vm.openClientEmailPopup = openClientEmailPopup;
        $scope.company_id = sessionService.get('company_id');
        $scope.display_number = $stateParams.id;

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
        
        $scope.updateOrderStatus = function(name,value,id)
        {
            if($scope.allow_access==0){return false;}
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

        $scope.GetOrderScreenSet = function() 
        {
            $("#ajax_loader").show();
            var GetScreenArray = {company_id:$scope.company_id, display_number:$scope.display_number};
            $http.post('api/public/art/ScreenSets',GetScreenArray).success(function(result) 
            {
                if(result.data.success == '1') 
                {
                    $scope.ScreenSets = result.data.records;
                    $scope.order_id = $scope.ScreenSets[0].order_id;
                    $scope.ScreenSets_new = 
                    {
                        data_all: result.data.records,
                        selected: null,
                    };


                }
                else
                {
                    notifyService.notify('error',result.data.message);
                    $state.go('app.art');
                    return false;
                }
                $("#ajax_loader").hide();
            });
        }
        $scope.GetOrderScreenSet();
    
        $scope.change_sort = function ()
        {
            if($scope.allow_access==0){return false;}
            $("#ajax_loader").show();
            $http.post('api/public/art/change_sortscreen',$scope.ScreenSets_new.data_all).success(function(result) 
            {
                $scope.GetOrderScreenSet();
            });
        }



        $scope.createNewScreen =function (ev, position_id) {
            if($scope.allow_access==0){return false;}
            $mdDialog.show({
                controller: function ($scope, params,position_id)
                            {
                                //alert(position_id);
                                $scope.params = params;
                                $scope.display_number= params.display_number;
                                $scope.ink_array = params.miscData.art_type;
                                $scope.position_id
                                $scope.GetDetail =function () {
                                    $http.get('api/public/art/GetScreenset_detail/'+position_id).success(function(result) 
                                    {
                                        if(result.data.success == '1') 
                                        {
                                            $scope.details_screenset = result.data.records;
                                            $scope.getColors = result.data.getColors;
                                            $scope.screen_allcolors = result.data.allcolors;
                                            $scope.simulateQuery = false;
                                            $scope.isDisabled    = false;
                                            $scope.states        = loadAll();
                                            $scope.querySearch   = querySearch;
                                        }
                                        else
                                        {
                                            notifyService.notify('error',result.data.message);
                                        }
                                    });
                                }
                                $scope.GetDetail();
                                function querySearch (query) 
                                {
                                    var results = query ? $scope.states.filter( createFilterFor(query) ) : $scope.states, deferred;
                                    if ($scope.simulateQuery) 
                                    {
                                        deferred = $q.defer();
                                        $timeout(function () { deferred.resolve( results ); }, Math.random() * 1000, false);
                                        return deferred.promise;
                                    } 
                                    else 
                                    {
                                        return results;
                                    }
                                }
                                function loadAll() 
                                {
                                    var allStates = $scope.screen_allcolors;
                                    return allStates;
                                }
                                function createFilterFor(query) 
                                {
                                    var lowercaseQuery = angular.lowercase(query);
                                    // console.log(lowercaseQuery);
                                    return function filterFn(state) 
                                    {
                                        return (state.name.indexOf(lowercaseQuery) === 0);
                                    };
                                }
                                $scope.closeDialog = function() 
                                {
                                    $mdDialog.hide();
                                } 
                                $scope.initial_add_color = [];
                                $scope.add_color = function(id,color_name)
                                {
                                    if( !angular.isUndefined(id))
                                    {
                                        $('#remove_color').val('');
                                        $scope.initial_add_color.push({id:id,color_name:color_name,thread_color:'',inq:''});
                                    }
                                    //console.log($scope.initial_add_color);
                                }
                                $scope.add_thread = function(thread_color,key)
                                {
                                    if( !angular.isUndefined(key) && !angular.isUndefined(thread_color))
                                    {
                                        $scope.initial_add_color[key].thread_color = thread_color;
                                    }
                                }
                                $scope.add_inq = function(inq,key)
                                {
                                    if( !angular.isUndefined(key) && !angular.isUndefined(inq))
                                    {
                                        $scope.initial_add_color[key].inq = inq;
                                    }
                                }
                                $scope.CreateScreenset = function(alldata)
                                {
                                    alldata = {alldata:alldata,add_screen_color:$scope.initial_add_color,remove_screen_color:$scope.screen_id_removed,change_color:$scope.getColors,display_order:$scope.display_number};
                                    
                                    $http.post('api/public/art/create_screen',alldata).success(function(result) 
                                    {
                                        if(result.data.success == '1') 
                                        {
                                            $scope.closeDialog();
                                            notifyService.notify('success','Screenset Updated successfully.');
                                        }
                                        else
                                        {
                                            notifyService.notify('error',result.data.message);
                                        }
                                    });
                                
                                }
                                $scope.remove_added = function(index)
                                {
                                    $scope.initial_add_color.splice(index,1);
                                }
                                $scope.check_savedata = function (flag)
                                {
                                    if(flag==1)
                                    {
                                        $("#check_alert").hide();
                                        $("#check_confirm").show();
                                    }
                                    else
                                    {
                                        $("#check_confirm").hide();
                                        $("#check_alert").show();
                                    }
                                }
                                $scope.AddNewColor= function(position_id,company_id,color_name)
                                {
                                        //console.log(color_name);
                                        $mdDialog.show({
                                                 controller: function ($scope, position_id,company_id,color_name,params)
                                                    {
                                                        $scope.child = params;
                                                        $scope.params = {};
                                                        $scope.params.name = color_name;
                                                        $scope.hidepopup = function() 
                                                        {
                                                            $mdDialog.hide();
                                                            $scope.child.createNewScreen(ev, position_id);
                                                            
                                                        } 
                                                        $scope.InsertTableData = function(insert_data,extra,cond)
                                                        {
                                                            $("#ajax_loader").show();
                                                            var InserArray = {};        // INSERT RECORD ARRAY
                                                            InserArray.data = insert_data;
                                                            InserArray.table ='color';
                                                            InserArray.data.company_id=company_id; 
                                                            $http.post('api/public/common/InsertRecords',InserArray).success(function(result) 
                                                            { 
                                                                if(result.data.success=='1')
                                                                {   notifyService.notify('success',result.data.message); 
                                                                    //$scope.GetDetail();
                                                                    $mdDialog.hide();
                                                                    $scope.child.createNewScreen(ev, position_id);
                                                                }
                                                                else
                                                                { notifyService.notify('error',result.data.message); }
                                                                $("#ajax_loader").hide();
                                                            });
                                                        }
                                                    },
                                                templateUrl: 'app/main/art/dialogs/createScreen/AddNewColor.html',
                                                clickOutsideToClose: true,
                                                locals: {
                                                    position_id:position_id,
                                                    company_id:company_id,
                                                    color_name:color_name,
                                                    params: $scope.params
                                                }
                                        });
                                   
                                }

                                $scope.screen_id_removed = [];
                                $scope.remove_selected = function(index,id)
                                {
                                    if( !angular.isUndefined(id))
                                    {
                                        $scope.getColors.splice(index,1);
                                        $scope.screen_id_removed.push({id:id});
                                    }
                                }
                    },
                controllerAs: 'vm',
                templateUrl: 'app/main/art/dialogs/createScreen/createScreen-dialog.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    params:$scope,
                    position_id:position_id,
                    event: ev
                },
                onRemoving : $scope.GetOrderScreenSet
            });
        }
        $scope.openClientEmailPopup = function(ev)
        {
            if($scope.allow_access==0){return false;}
            $mdDialog.show({
                controller: function ($scope, params)
                {
                    $scope.mail=params.ScreenSets[0].billing_email;
                    $scope.company_id=params.company_id;
                    $scope.order_id=params.order_id;
                    //console.log($scope.mail);
                    $scope.closeDialog = function() 
                    {
                        $mdDialog.hide();
                    }
                    $scope.printPdf=function(flag,email,options)
                    {
                        $mdDialog.hide();
                        var pass_array = {order_id:$scope.order_id,company_id:$scope.company_id,flag:flag,email:email}
                        if(flag=='1')
                        {
                            var k = confirm("Do you want to send Art approval PDF to client?");
                            if(k==false)
                            {
                                return false;
                            }
                        }
                        var target;
                        var form = document.createElement("form");
                        form.action = 'api/public/art/ArtApprovalPDF';
                        form.method = 'post';
                        form.target = target || "_blank";
                        form.style.display = 'none';

                        var input_screenset = document.createElement('input');
                        input_screenset.name = 'art';
                        input_screenset.setAttribute('value', JSON.stringify(pass_array));
                        form.appendChild(input_screenset);

                        var input_pdf = document.createElement('input');
                        input_pdf.name = 'pdf_token';
                        input_pdf.setAttribute('value', 'pdf_token');
                        form.appendChild(input_pdf);

                        document.body.appendChild(form);
                        form.submit();  

                    };
                },
                controllerAs: 'vm',
                templateUrl: 'app/main/art/dialogs/EmailPopup/EmailPopup.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    params:$scope,
                    event: ev
                }
            });
        }

        $scope.Artpressall=function(ev)
        {
            if($scope.allow_access==0){return false;}
            $mdDialog.show({
                controller: function ($scope, params)
                {
                    $scope.params = params
                    $scope.company_id=params.company_id;
                    $scope.order_id=params.order_id;
                    $scope.flag = 1;
                    $scope.closeDialog = function() 
                    {
                        $mdDialog.hide();
                    }
                    $scope.printPdf=function(options)
                    {
                        if($scope.allow_access==0){return false;}
                        var pass_array = {order_id:$scope.order_id,company_id:$scope.company_id,options:options}
                        var target;
                        var form = document.createElement("form");
                        form.action = 'api/public/art/PressInstructionAllPDF';
                        form.method = 'post';
                        form.target = target || "_blank";
                        form.style.display = 'none';

                        var input_screenset = document.createElement('input');
                        input_screenset.name = 'art';
                        input_screenset.setAttribute('value', JSON.stringify(pass_array));
                        form.appendChild(input_screenset);

                        var input_pdf = document.createElement('input');
                        input_pdf.name = 'pdf_token';
                        input_pdf.setAttribute('value', 'pdf_token');
                        form.appendChild(input_pdf);

                        document.body.appendChild(form);
                        form.submit();  
                    };
                },
                controllerAs: 'vm',
                templateUrl: 'app/main/art/dialogs/EmailPopup/artpress.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    params:$scope,
                    event: ev
                }
            });

        };


        function generateArtForm(ev, settings) {
            if($scope.allow_access==0){return false;}
            $mdDialog.show({
                 controller: function ($scope, params){
                            $scope.closeDialog = function() 
                            {
                                $mdDialog.hide();
                            } 
                    },
                controllerAs: 'vm',
                templateUrl: 'app/main/art/dialogs/generateArtForm/generateArtForm-dialog.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    params:$scope,
                    event: ev
                }
            });
        }
        // Datatable Options
       
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
       

        $scope.UpdateTableField = function(field_value,order_id)
        {
            if($scope.allow_access==0){return false;}
            var vm = this;
            var UpdateArray = {};
            UpdateArray.table ='art';
            UpdateArray.data = {approval:field_value};
            UpdateArray.cond = {order_id:order_id};
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
            if($scope.allow_access==0){return false;}
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

    }
})();
