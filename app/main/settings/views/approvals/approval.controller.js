(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('approvalsController', approvalsController);
            

    /** @ngInject */
    function approvalsController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter)
    {
        $scope.company_id = sessionService.get('company_id');
        var vm = this ;

        var combine_array_id = {};
        combine_array_id.company_id = sessionService.get('company_id');

        $http.post('api/public/admin/getApprovalOrders',combine_array_id).success(function(result, status, headers, config) {
            if(result.data.success == '1') {
                $("#ajax_loader").hide();
               $scope.orders = result.data.records;
            }
        });
    }


       
})();
