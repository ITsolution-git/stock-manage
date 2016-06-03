(function ()
{
    'use strict';

    angular
        .module('app.art')
        .controller('createNewScreenController', createNewScreenController);

    /** @ngInject */
    function createNewScreenController($mdDialog,$controller,$state,  event,$scope,sessionService,$resource)
    {
        var vm = this;

       this.userState = '';

        this.states = ('AL AK AZ AR CA CO CT DE FL GA HI ID IL IN IA KS KY LA ME MD MA MI MN MS ' +
            'MO MT NE NV NH NJ NM NY NC ND OH OK OR PA RI SC SD TN TX UT VT VA WA WV WI ' +
            'WY').split(' ').map(function (state) { return { abbrev: state }; });


        this.affilliatePriceGrid = '';

        this.priceGrids = ('Price_Grid_1 Price_Grid_2 Price_Grid_3').split(' ').map(function (state) { return { abbrev1: state }; });
        
        $scope.cancel = function () {
            $mdDialog.hide();
        };

        
        /**
         * Close dialog
         */
        function closeDialog()
        {
            $mdDialog.hide();
        }
    }
})();