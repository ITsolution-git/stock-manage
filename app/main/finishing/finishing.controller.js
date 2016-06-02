(function () {
    'use strict';

    angular
            .module('app.finishing')
            .controller('FinishingController', FinishingController);

    /** @ngInject */
    function FinishingController($q, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$scope,$http,sessionService) {
        var vm = this;
         vm.searchQuery = "";

         this.condition = '';

        this.conditions = ('Yes No').split(' ').map(function (state) { return { abbrev: state }; });
        
        vm.editFinishing = editFinishing ;

        function editFinishing(ev, finishing)
            {
                $mdDialog.show({
                    controller: 'EditFinishingDialogController',
                    controllerAs: 'vm',
                    templateUrl: 'app/main/finishing/dialogs/editFinishing/editFinishing-dialog.html',
                    parent: angular.element($document.body),
                    targetEvent: ev,
                    clickOutsideToClose: true,
                    locals: {
                        Finishing: finishing,
                        Finishing: vm.finishing,
                        event: ev
                    }
                });
            }

        // Data
        //vm.receiving = ReceivingData.data;
        //Datatable
        vm.dtOptions = {
            dom: '<"top">rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };
        // Methods
       vm.dtInstanceCB = dtInstanceCB;
        vm.searchTable = searchTable;

        // -> Filter menu
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