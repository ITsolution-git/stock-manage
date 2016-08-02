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


        $scope.screenset_id = $stateParams.id;

        $scope.GetOrderScreenSet = function() 
        {
            $http.get('api/public/art/GetscreenColor/'+$scope.screenset_id).success(function(result) 
            {
                if(result.data.success == '1') 
                {
                    $scope.ScreenSets = result.data.records;
                    $scope.getColors = result.data.getColors;
                    $scope.screen_allcolors = result.data.allcolors;
                    
                }
                else
                {
                    notifyService.notify('error',result.data.message);
                    /*$state.go('app.art');
                    return false;*/
                }
            });
        }
        $scope.GetOrderScreenSet();


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
    }
})();
