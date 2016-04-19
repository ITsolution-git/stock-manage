(function ()
{
    'use strict';

    angular
            .module('app.purchaseOrder')
            .controller('ViewNoteController', ViewNoteController);

    /** @ngInject */
    function ViewNoteController($document, $window, $timeout, $mdDialog)
    {
        var vm = this;
         vm.openaddNoteDialog = openaddNoteDialog;
         vm.openeditNoteDialog = openeditNoteDialog;
        //Dummy models data
     
        vm.notes = [
            {"dateCreated": "xx/xx/xxxx", "noteName": "Note Name", "noteDescription": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo."},
            {"dateCreated": "xx/xx/xxxx", "noteName": "Note Name", "noteDescription": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo."},
            {"dateCreated": "xx/xx/xxxx", "noteName": "Note Name", "noteDescription": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo."}
            ];
         function openaddNoteDialog(ev, order)
        {
            $mdDialog.show({
                controller: 'AddNoteController',
                controllerAs: 'vm',
                templateUrl: 'app/main/purchaseOrder/dialogs/addNote/addNote.html',
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
         function openeditNoteDialog(ev, order)
        {
            $mdDialog.show({
                controller: 'EditNoteController',
                controllerAs: 'vm',
                templateUrl: 'app/main/purchaseOrder/dialogs/editNote/editNote.html',
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
      
        
//        Datatable Options
        vm.dtOptions = {
            dom: '<"top"f>rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
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
    }
})();
