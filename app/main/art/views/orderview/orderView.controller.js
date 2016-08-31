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
        vm.createNewScreen = createNewScreen;
        vm.generateArtForm = generateArtForm;
        $scope.company_id = sessionService.get('company_id');
        $scope.order_id = $stateParams.id;

        $scope.GetOrderScreenSet = function() 
        {
            $("#ajax_loader").show();
            var GetScreenArray = {company_id:$scope.company_id, order_id:$scope.order_id};
            $http.post('api/public/art/ScreenSets',GetScreenArray).success(function(result) 
            {
                if(result.data.success == '1') 
                {
                    $scope.ScreenSets = result.data.records;
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
            $("#ajax_loader").show();
            $http.post('api/public/art/change_sortscreen',$scope.ScreenSets_new.data_all).success(function(result) 
            {
                $scope.GetOrderScreenSet();
            });
        }



        function createNewScreen(ev, position_id) {

            $mdDialog.show({
                controller: function ($scope, params,position_id)
                            {
                                //alert(position_id);
                                $scope.params = params;
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
                                        $scope.initial_add_color.push({id:id,color_name:color_name});
                                    }
                                }
                                $scope.CreateScreenset = function(alldata)
                                {
                                    alldata = {alldata:alldata,add_screen_color:$scope.initial_add_color,remove_screen_color:$scope.screen_id_removed};
                                    
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
        function generateArtForm(ev, settings) {
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
        $scope.printPdf=function(mail)
        {
            
            var pass_array = {order_id:$scope.order_id,company_id:$scope.company_id,mail:mail}
            if(mail=='1')
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

            document.body.appendChild(form);
            form.submit();  
        };

        $scope.UpdateTableField = function(field_value,order_id)
        {
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

    }
})();
