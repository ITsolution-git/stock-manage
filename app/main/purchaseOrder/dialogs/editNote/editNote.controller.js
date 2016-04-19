(function ()
{
    'use strict';

    angular
        .module('app.purchaseOrder')
        .controller('EditNoteController', EditNoteController);

    /** @ngInject */
    function EditNoteController($mdDialog)
    {
        var vm = this;
        vm.title = 'Edit Note';
       

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