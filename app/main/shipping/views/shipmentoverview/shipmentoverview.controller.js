(function ()
{
    'use strict';

    angular
            .module('app.shipping')
            .controller('shipmentOverviewController', shipmentOverviewController);

    /** @ngInject */
    function shipmentOverviewController($document, $window, $timeout, $mdDialog)
    {
        var vm = this;
        //Dummy models data
        vm.shipDetails = [{
                "orderId": "1234",
                "orderName": "Name of Order",
                "shippingBy": "xx/xx/xxxx",
                "inHandBy": "xx/xx/xxxx",
                "status": "Ready To Ship",
                "shippingId": "1234",
                "product": "Stoli Tech hood",
                "boxType": "Standard",
                "shippingType": "UPS",
                "clientId": "12345",
                "clientName": "Codal",
                "fullyShipped": "xx/xx/xxxx",
                "dateShipped": "xx/xx/xxxx",
             }];
        vm.distrLoc = [
            {"locationName": "Location Name", "locationAttn": "Name", "locationAddress": "1234 N Main St. Chicago, IL 60611 - USA", "locationPhone": "+91 123456789"},
            {"locationName": "Location Name", "locationAttn": "Name", "locationAddress": "1234 N Main St. Chicago, IL 60611 - USA", "locationPhone": "+91 123456789"}
        ];
        vm.boxShipmnt = [
            {"boxId": "34", "unitPacked": "72", "trackNo": "xxxxxxxxxxxxxx"},
            {"boxId": "34", "unitPacked": "72", "trackNo": "xxxxxxxxxxxxxx"},
            {"boxId": "34", "unitPacked": "72", "trackNo": "xxxxxxxxxxxxxx"}
        ];
    }
})();
