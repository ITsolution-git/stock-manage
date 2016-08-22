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
        $scope.GetOrderScreenSet();

        $scope.change_sort = function ()
        {
            $("#ajax_loader").show();
            $http.post('api/public/art/change_sortcolor',$scope.ScreenSets_new.data_all).success(function(result) 
            {
                $scope.GetOrderScreenSet();
            });
        }

         $scope.UpdateColorScreen = function(ev, colordata) 
         {
            $mdDialog.show({
                controller: function ($scope, params,colordata)
                            {
                                //alert(position_id);
                                $scope.params = params;
                                $scope.color_screen = colordata;
                                //console.log($scope.color_screen); 
                                $scope.screen_allcolors = $scope.params.screen_allcolors;
                                $scope.simulateQuery = false;
                                $scope.isDisabled    = false;
                                $scope.states        = loadAll();
                                $scope.querySearch   = querySearch;
                      
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
                                    return function filterFn(state) 
                                    {
                                        return (state.name.indexOf(lowercaseQuery) === 0);
                                    };
                                }
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
        //        Datatable Options
        vm.dtOptions = {
            dom: '<"top"f>rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };
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
