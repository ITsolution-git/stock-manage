(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('SpiltAffiliateController', SpiltAffiliateController);

    /** @ngInject */
    function SpiltAffiliateController($document, $window, $timeout, $mdDialog, $stateParams, $scope, $http, sessionService, AllConstant)
    {
        $scope.NoImage = AllConstant.NoImage;
        $scope.order_id = $stateParams.id;
        $scope.company_id = sessionService.get('company_id');

        var combine_array_id = {};
        combine_array_id.id = $scope.order_id;
        combine_array_id.company_id = $scope.company_id;

        $http.post('api/public/order/orderDetail',combine_array_id).success(function(result, status, headers, config) {
            if(result.data.success == '1') {
               $scope.order = result.data.records[0];
            }
        });

        var affiliate_data = {};
        affiliate_data ={'company_id':$scope.company_id,'id':$scope.order_id}
        $http.post('api/public/affiliate/getAffiliateData',affiliate_data).success(function(result) {
            
            if(result.data.success == '1') 
            {
                $scope.spiltOrderInformation = result.data.records;
                $scope.spiltOrderList = result.data.affiliateList;
            }
        });

        var vm = this;
         $scope.openaddDesignDialog = openaddDesignDialog;
          $scope.openaddSplitAffiliateDialog = openaddSplitAffiliateDialog;
       
        
//        Datatable Options
        $scope.dtOptions = {
            dom: '<"top">rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };
        var originatorEv;
        $scope.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
        $scope.dtInstanceCB = dtInstanceCB;
        //methods
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            $scope.tableInstance = datatableObj;
        }
         function openaddDesignDialog(ev, order)
        {
            $mdDialog.show({
                controller: 'AddDesignController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/addDesign/addDesign.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    Order: order,
                    Orders: $scope.orders,
                    event: ev
                }
            });
        }
        function openaddSplitAffiliateDialog(ev, order)
        {
            $mdDialog.show({
                controller: 'AddSplitAffiliateController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/addSplitAffiliate/addSplitAffiliate.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    Order: $scope,
                    Orders: $scope.orders,
                    event: ev
                }
            });
        }
    }
})();
