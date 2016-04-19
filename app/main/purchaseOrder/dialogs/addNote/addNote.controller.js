(function ()
{
    'use strict';

    angular
        .module('app.purchaseOrder')
        .controller('AddNoteController', AddNoteController);

    /** @ngInject */
    function AddNoteController($mdDialog)
    {
        var vm = this;
        vm.title = 'Add New Note';
               // Data
      vm.addNotes={
          notes:"",
          desc:""
      }
   
       
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