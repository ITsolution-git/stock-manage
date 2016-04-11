(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('DistributionController', DistributionController);

    /** @ngInject */
    function DistributionController($document, $window, $timeout, $mdDialog)
    {
        var vm = this;
        vm.openaddAddressDialog = openaddAddressDialog;
        vm.distributionDistributed = {
            "productshipped": "800",
            "Total": "100",
        };
        vm.distributionLocation = {
            "location": "231",
        };
        vm.distProducts = [
            {productName: "Product Name 1", jobName: "Job Name1", job: "#", totalAllocated: "0/120", buttn: "Distributed"},
            {productName: "Product Name 2", jobName: "Job Name1.1", job: "#", totalAllocated: "0/120", buttn: "Distributed"},
            {productName: "Product Name 3", jobName: "Job Name1.2", job: "#", totalAllocated: "80/120", buttn: "Edit"}
        ]

                ;
        vm.distlocations = [
            {loactionName: "Location Name", ATTN: "Name", Address: "1234 N Main St. Chicago, IL 60611 - USA", Phone: "555-555-555"},
            {loactionName: "Location Name", ATTN: "Name", Address: "1234 N Main St. Chicago, IL 60611 - USA", Phone: "555-555-555"},
            {loactionName: "Location Name", ATTN: "Name", Address: "1234 N Main St. Chicago, IL 60611 - USA", Phone: "555-555-555"}
        ]

                ;
        vm.distInfo = {
            customerPO: "######",
            sales: "Keval Baxi",
            blind: "Yes",
            accountManager: "Nancy McPhee",
            mainContact: "Joshi Goodman",
            priceGrid: "ABC Grid",
        };
        function openaddAddressDialog(ev, order)
        {
            $mdDialog.show({
                controller: 'AddAddressController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/addAddress/addAddress.html',
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

        //Dummy models data


        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
        vm.dtOptions = {
            dom: '<"top"f>rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };
        vm.dtInstanceCB = dtInstanceCB;
        vm.openAddProductDialog = openAddProductDialog;
        //methods
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
        }

        function openAddProductDialog(ev, order)
        {
            $mdDialog.show({
                controller: 'AddProductController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/addProduct/addProduct.html',
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


        vm.productSearch = null;

    }
})();
