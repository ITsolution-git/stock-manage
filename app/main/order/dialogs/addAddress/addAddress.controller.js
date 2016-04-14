(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('AddAddressController', AddAddressController);

    /** @ngInject */
    function AddAddressController($mdDialog)
    {
        var vm = this;
        vm.title = 'Add New Distribution Address';

        // Data
        vm.orderInfo={
          "customerPo":"######",
         "sales":"keval Baxi",
         "blind":"Yes",
         "accountManger":"Nancy McPhee",
         "mainContact":"Joshi Goodman",
         "priceGrid":"ABC Grid"
        };
        vm.addAddress={
          "description":"",
         "attn":"",
         "address1":"",
         "address2":"",
         "city":"",
         "zipcode":"",
         "phone":""
        };
        vm.addOrder={
            companyName:"Company Name",
            jobName:""
        };
          vm.stateSelect = {
            "stateOption":
                    [
                        {"option": "State 1"},
                        {"option": "State 2"},
                        {"option": "State 3"}
                    ],
            "state": ""

        };
          vm.countrySelect = {
            "countryOption":
                    [
                        {"option": "Country 1"},
                        {"option": "Country 2"},
                        {"option": "Country 3"}
                    ],
            "country": ""

        };
   
        vm.addDesign={
            compnayName:"",
            "front":"",
            back:"",
            sideLeft:"",
            sideRight:"",
            top:"",
            bottom:""
            
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