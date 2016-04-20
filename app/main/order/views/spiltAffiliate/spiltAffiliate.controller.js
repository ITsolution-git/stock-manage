(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('SpiltAffiliateController', SpiltAffiliateController);

    /** @ngInject */
    function SpiltAffiliateController($document, $window, $timeout, $mdDialog)
    {
        var vm = this;
         vm.openaddDesignDialog = openaddDesignDialog;
          vm.openaddSplitAffiliateDialog = openaddSplitAffiliateDialog;
        //Dummy models data
        vm.spiltOrderInformation = {
            "customerPo": "######",
            "sales": "keval Baxi",
            "blind": "Yes",
            "accountManger": "Nancy McPhee",
            "mainContact": "Joshi Goodman",
            "priceGrid": "ABC Grid",
            "assign":"200",
            "total":"1000"
        };
        vm.affilateProductSize={
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
        vm.dtOptions = {
            dom: '<"top">rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
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
                    Orders: vm.orders,
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
                    Order: order,
                    Orders: vm.orders,
                    event: ev
                }
            });
        }
    }
})();
