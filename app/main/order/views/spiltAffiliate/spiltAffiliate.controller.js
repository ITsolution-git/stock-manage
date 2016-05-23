(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('SpiltAffiliateController', SpiltAffiliateController);

    /** @ngInject */
    function SpiltAffiliateController($document, $window, $timeout, $mdDialog, $stateParams, $scope, $http, sessionService)
    {
        $scope.order_id = $stateParams.id;
        $scope.company_id = sessionService.get('company_id');

        var affiliate_data = {};
        affiliate_data ={'company_id':$scope.company_id,'id':$scope.order_id}
        $http.post('api/public/affiliate/getAffiliateData',affiliate_data).success(function(result) {
            
            if(result.data.success == '1') 
            {
                $scope.allAffiliate =result.data.records['affiliate_data'];
                $scope.allDesign =result.data.records['design_detail'];
            } 
            else
            {
                $scope.allVendors=[];
            }
        });

        var vm = this;
         $scope.openaddDesignDialog = openaddDesignDialog;
          $scope.openaddSplitAffiliateDialog = openaddSplitAffiliateDialog;
        //Dummy models data
        $scope.spiltOrderInformation = {
            "customerPo": "######",
            "sales": "keval Baxi",
            "blind": "Yes",
            "accountManger": "Nancy McPhee",
            "mainContact": "Joshi Goodman",
            "priceGrid": "ABC Grid",
            "assign":"200",
            "total":"1000"
        };
        $scope.affilateProductSize={
            design:"Spiral Codal",
            product:"Product",
            notes:"Lorem spunm notes of information that the affiliate needs to know about the order.",
            s:"80",
            m:"20",
            l:"80",
            xl:"29",
            total:"200"
            
        };
       
        
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
