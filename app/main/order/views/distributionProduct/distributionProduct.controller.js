(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('DistributionProductController', DistributionProductController);

    /** @ngInject */
    function DistributionProductController(Addresses,$document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {
        var vm = this;
        vm.orderOverview = {
            productName: "American Apparel Crew Neck",
            vendor: "American Apparel",
            sku: "#######",
            description: "Lorem spunm text that describe the product."
        };
        vm.orderOverviewSize = {
            s: "",
            m: "",
            l: "",
            xl: ""
        };
        vm.orderOverviewLocation = {
            description: "Description Text",
            attn: "ATTN",
            location: "1234 N Main St. Chicago, IL 60611 - USA",
            phone: "Phone"
        };
        vm.orderOverviewDescription = {
            description: "Description Text",
            attn: "ATTN",
            location: "1234 N Main St. Chicago, IL 60611 - USA",
            phone: "Phone"
        };

        vm.locationSelect = {
            "locationOption":
                    [
                        {"option": "Section 1"},
                        {"option": "Section 2"},
                        {"option": "Section 3"}
                    ],
            "location": false,
            "locationView":""

        };


        vm.distributionDistributed = {
            "productshipped": "800",
            "Total": "100",
        };
        vm.distributionLocation = {
            "location": "231",
        };
        vm.distProducts = [
            {productName: "Product Name 1", jobName: "Job Name1", job: "#", totalAllocated: "0/120"},
            {productName: "Product Name 2", jobName: "Job Name1.1", job: "#", totalAllocated: "0/120"},
            {productName: "Product Name 3", jobName: "Job Name1.2", job: "#", totalAllocated: "80/120"}
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

        //Dummy models data

        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
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
