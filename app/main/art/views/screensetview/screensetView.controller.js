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
        vm.createScreenDetail = createScreenDetail;
        $scope.company_id = sessionService.get('company_id');


        $scope.screenset_id = $stateParams.id;

        $scope.GetOrderScreenSet = function() 
        {
            $http.get('api/public/art/GetscreenColor/'+$scope.screenset_id).success(function(result) 
            {
                if(result.data.success == '1') 
                {
                    $scope.ScreenSets = result.data.records;
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


     
        function createScreenDetail(ev, settings) {
            $mdDialog.show({
                controller: 'createNewScreenController',
                controllerAs: 'vm',
                templateUrl: 'app/main/art/dialogs/createScreenDetail/createScreenDetail.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    event: ev
                }
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
