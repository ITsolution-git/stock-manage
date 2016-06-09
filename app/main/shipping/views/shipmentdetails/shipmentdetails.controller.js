(function ()
{
    'use strict';

    angular
            .module('app.shipping')
            .controller('shipmentController', shipmentController);

    /** @ngInject */
    function shipmentController($document, $window, $timeout, $mdDialog)
    {
        var vm = this;
        //Dummy models data
        vm.shipDetail = [{
                "shippingId": "1234",
                "orderId": "1234",
                "product": "Product1",
                "clientId": "1234",
                "client": "Codal",
                "shippingby": "xx/xx/xx",
                "inHandBy": "xx/xx/xx",
             }];
        vm.productDetail = [
            {"sizeGroup": "Mens", "product": "12345", "size": "M", "color": "Black", "distributed":"20", "shipped": "0", "boxedQty":"72", "remainingtoBox":"0"},
            {"sizeGroup": "Mens", "product": "12345", "size": "M", "color": "Black", "distributed":"20", "shipped": "0", "boxedQty":"72", "remainingtoBox":"0"}
        ];
        vm.selectedOrderItems = [
            {"sizeGroup": "Mens", "product": "12345", "size": "M", "color": "Black"}
        ];
    }
})();
