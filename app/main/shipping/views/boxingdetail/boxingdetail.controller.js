(function ()
{
    'use strict';

    angular
            .module('app.shipping')
            .controller('boxingdetailController', boxingdetailController);

    /** @ngInject */
    function boxingdetailController($document,$window,$timeout,$mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {
        var vm = this;
        $scope.shipping_id = $stateParams.id;
        //Dummy models data
        vm.boxDetails = [{
                "boxId": "1234",
                "numBox": "1234",
                "unitPackaged": "Product1",
                "trackNumber": "1234",
             },{
                "boxId": "1234",
                "numBox": "1234",
                "unitPackaged": "Product1",
                "trackNumber": "1234",
             },{
                "boxId": "1234",
                "numBox": "1234",
                "unitPackaged": "Product1",
                "trackNumber": "1234",
             },{
                "boxId": "1234",
                "numBox": "1234",
                "unitPackaged": "Product1",
                "trackNumber": "1234",
             }];
        vm.productDetail = [{
                "boxId": "18067",
                "sizeGroup": "Men's",
                "product": "Product1",
                "productColor": "Black",
                "size": "M",
                "boxedQty": "30",
                "actual": "0",
             },{
                "boxId": "18067",
                "sizeGroup": "Men's",
                "product": "Product1",
                "productColor": "Black",
                "size": "M",
                "boxedQty": "30",
                "actual": "0",
             },{
                "boxId": "18067",
                "sizeGroup": "Men's",
                "product": "Product1",
                "productColor": "Black",
                "size": "M",
                "boxedQty": "30",
                "actual": "0",
             },{
                "boxId": "18067",
                "sizeGroup": "Men's",
                "product": "Product1",
                "productColor": "Black",
                "size": "M",
                "boxedQty": "30",
                "actual": "0",
             }];
    }
})();
