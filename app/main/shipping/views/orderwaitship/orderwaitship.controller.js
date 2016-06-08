(function ()
{
    'use strict';

    angular
            .module('app.shipping')
            .controller('orderWaitController', orderWaitController);

    /** @ngInject */
    function orderWaitController($document, $window, $timeout, $mdDialog)
    {
        var vm = this;
        //Dummy models data
        vm.orderItems = [
            {
                "sizeGroup": "Mens",
                "product": "12345",
                "size": "M",
                "color": "Black",
                "productDescription": "The Text describes the product that you are going to select",
                "qtyOrdered": "20",
                "remainingDistribute": "10"
             },
             {
                "sizeGroup": "Mens",
                "product": "12345",
                "size": "M",
                "color": "Black",
                "productDescription": "The Text describes the product that you are going to select",
                "qtyOrdered": "20",
                "remainingDistribute": "10"
             },
             {
                "sizeGroup": "Mens",
                "product": "12345",
                "size": "M",
                "color": "Black",
                "productDescription": "The Text describes the product that you are going to select",
                "qtyOrdered": "20",
                "remainingDistribute": "10"
             }];
        vm.addresses = [
            {"location": "Location Name", "shortCode": "ATTN", "full": "1234 N Main St. Chicago, IL 60611 - USA", "phone": "+ 91 123456789"},
            {"location": "Location Name", "shortCode": "ATTN", "full": "1234 N Main St. Chicago, IL 60611 - USA", "phone": "+ 91 123456789"},
            {"location": "Location Name", "shortCode": "ATTN", "full": "1234 N Main St. Chicago, IL 60611 - USA", "phone": "+ 91 123456789"}
        ];
    }
})();
