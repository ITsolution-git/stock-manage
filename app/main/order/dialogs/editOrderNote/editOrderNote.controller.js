(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('EditOrderNoteController', EditOrderNoteController);

    /** @ngInject */
    function EditOrderNoteController($mdDialog)
    {
        var vm = this;
        vm.title = 'Edit Order Note';
       

        // Data
         vm.notes = {
             'noteName':'NoteName',
             'notedecs':'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum laoreet. Proin gravida dolor sit amet lacus accumsan et viverra justo commodo.',
         };
                       // Methods
    
        vm.closeDialog = closeDialog;
             /**
         * Close dialog
         */
        function closeDialog()
        {
            $mdDialog.hide();
        }
    }
})();