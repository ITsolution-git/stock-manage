(function () {
    'use strict';

    angular
            .module('app.order')
            .controller('OrderController', OrderController);


    /** @ngInject */
    function OrderController(OrderData, $q, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder) {
        var vm = this;

        // Data
        vm.orders = OrderData.data;
        //Datatable
        vm.dtOptions = {
            dom: '<"top">rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };
        
        vm.searchQuery = "";

        // Methods
        vm.openOrderDialog = openOrderDialog;
        vm.dtInstanceCB = dtInstanceCB;
        vm.searchTable = searchTable;


        // -> Filter menu
        vm.toggle = true;
        vm.openRightMenu = function () {
            $mdSidenav('right').toggle();
        };
        //1. sales rep
        vm.salesCheck = [{"v": true, "name": "Nick Santo"}, {"v": false, "name": "Kemal Baxi"}, {"v": false, "name": "Kemal Baxi"}, {"v": false, "name": "Kemal Baxi"}, {"v": false, "name": "Kemal Baxi"}, {"v": false, "name": "Nick Santo"}, {"v": false, "name": "Nick Santo"}, ];
        //2. ship date
        vm.shipDate;
        vm.shipdate = false;
        //3. create date
        vm.createDate;
        vm.createdate = false;
        //4. company
        vm.companyCheck = [{"v": false, "name": "Checkbox 1"}, {"v": true, "name": "Checkbox 2"}, {"v": false, "name": "Checkbox 3"}, {"v": false, "name": "Checkbox 4"}, {"v": false, "name": "Checkbox 5"}, {"v": false, "name": "Checkbox 6"}, {"v": false, "name": "Checkbox 7"}, ];
        vm.companyfilter = false;
        //5. order id
        vm.searchOrder;
        vm.ordersId = [{"id": "27"}, {"id": "35"}, {"id": "12"}];
        vm.orderfilter = false;
        //6. data range
        vm.rangeFrom;
        vm.rangeTo;
        vm.datefilter = false;

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
            jQuery('.dev-rdetail').on('click', function () {
                var $tr = $(this).closest('tr');
                var row = datatableObj.row($tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    $tr.removeClass('shown');
                } else {
                    var rowHtml=$tr.find("div.dev-rdetail-data").html();
                    row.child(rowHtml).show();
                    $tr.addClass('shown').next('tr').addClass('table-desc').children('td').addClass('collpas');
                }
            });
        }
        function searchTable() {
            var query = vm.searchQuery;
            vm.tableInstance.search(query).draw();
        }

    }

})();
