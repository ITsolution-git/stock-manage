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
        //Dummy models data
        
        vm.screensetPOinfo = {
            "client": "Client Name",
            "orderName": "12345",
            "orderDate": "12/05/2016",
            "contract": "Joshi Goodman",
            "affiliate": "Affiliate Name",
            "affiliateArrival": "xx/xx/xxxx",
            "affiliateDeadline": "xx/xx/xxxx"
        };
        vm.colorsetinfo = [
            {"threadColor": "Black", "threadCount": "30", "stroke": "3", "inkType": "Type1", "headLocation": "Top", "Squeegee": "Type"},
            {"threadColor": "Black", "threadCount": "30", "stroke": "3", "inkType": "Type1", "headLocation": "Top", "Squeegee": "Type"},
            {"threadColor": "Black", "threadCount": "30", "stroke": "3", "inkType": "Type1", "headLocation": "Top", "Squeegee": "Type"}
        ];
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
