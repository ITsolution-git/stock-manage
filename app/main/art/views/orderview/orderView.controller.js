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
            var GetScreenArray = {company_id:$scope.company_id, order_id:$scope.order_id};
            $http.post('api/public/art/ScreenSets',GetScreenArray).success(function(result) 
            {
                if(result.data.success == '1') 
                {
                    $scope.ScreenSets = result.data.records;
                }
                else
                {
                    notifyService.notify('error',result.data.message);
                    $state.go('app.art');
                    return false;
                }
            });
        }
        $scope.GetOrderScreenSet();


        $scope.screensetPOinfo  = {
            "client": "Client Name",
            "orderName": "12345",
            "orderDate": "12/05/2016",
            "contract": "Joshi Goodman",
            "affiliate": "Affiliate Name",
            "affiliateArrival": "xx/xx/xxxx",
            "affiliateDeadline": "xx/xx/xxxx"
        };
        vm.screensetinfo = [
            {"Position": "Front", "NumberColors": "30", "FrameSize": "32", "Width": "10", "PrintLocation": "Top", "NumberScreens": "10", "LinesPerInch": "10", "Height": "20"},
            {"Position": "Front", "NumberColors": "30", "FrameSize": "32", "Width": "10", "PrintLocation": "Top", "NumberScreens": "10", "LinesPerInch": "10", "Height": "20"},
            {"Position": "Front", "NumberColors": "30", "FrameSize": "32", "Width": "10", "PrintLocation": "Top", "NumberScreens": "10", "LinesPerInch": "10", "Height": "20"}
        ];
        function createNewScreen(ev, settings) {
            $mdDialog.show({
                controller: function ($scope, params){
                            $scope.closeDialog = function() 
                            {
                                $mdDialog.hide();
                            } 
                    },
                controllerAs: 'vm',
                templateUrl: 'app/main/art/dialogs/createScreen/createScreen-dialog.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    params:$scope,
                    event: ev
                }
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
        //        Datatable Options
       
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
