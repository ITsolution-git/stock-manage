(function () {
    'use strict';

    angular
            .module('app.shipping')
            .controller('shippingController', shippingController);
    /** @ngInject */
    function shippingController(shippingData, $q, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$scope,$http,sessionService) {
        
        var vm = this;
        vm.searchQuery = "";

        $scope.company_id = sessionService.get('company_id');

        vm.ows = shippingData.shippingDetail.data;
        vm.oip = shippingData.shippingDetail.data1;

        var condition_obj = {};
        condition_obj.company_id =  sessionService.get('company_id');
        
        $http.post('api/public/shipping/listShipping',condition_obj).success(function(Listdata) {
            $scope.waitingData = Listdata.data.records.waiting;
            $scope.progressData = Listdata.data.records.progress;
            $scope.shippedData = Listdata.data.records.shipped;
        });
    }
})();
