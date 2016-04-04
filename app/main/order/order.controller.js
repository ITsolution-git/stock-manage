(function () {
    'use strict';

    angular
            .module('app.order')
            .controller('OrderController', OrderController);           

    /** @ngInject */
    function OrderController(OrderData, $mdDialog, $document,DTOptionsBuilder) {
        var vm = this;
        // Data
        vm.orders = OrderData.data;        
 
        vm.dtOptions = {
            dom: '<"top">rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true,
//            bFilter: false,
//            fnRowCallback: rowCallback
        };
        vm.searchQuery = "";
        // Methods
        vm.openOrderDialog = openOrderDialog;
        vm.dtInstanceCB = dtInstanceCB;
        vm.searchTable = searchTable;
        //////////
        function openOrderDialog(ev, order)
        {
            $mdDialog.show({
                controller: 'OrderDialogController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/order/order-dialog.html',
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
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
        }
        function searchTable() {
            var query = vm.searchQuery;
            vm.tableInstance.search(query).draw();
        }

    }
   


})();
