(function ()
{
    'use strict';

    angular
            .module('app.shipping')
            .controller('shipmentOverviewController', shipmentOverviewController);

    /** @ngInject */
    function shipmentOverviewController($document,$window,$timeout,$mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {
        var vm = this;

        $scope.shipping_id = $stateParams.id;

        $scope.getShippingOverview = function()
        {
            $("#ajax_loader").show();
            var combine_array = {};
            combine_array.shipping_id = $scope.shipping_id;

            $http.post('api/public/shipping/getShippingOverview',combine_array).success(function(result) {

                $("#ajax_loader").hide();
                if(result.data.success == '1') 
                {
                    $scope.shippingBoxes =result.data.shippingBoxes;
                    $scope.shippingItems =result.data.shippingItems;
                    $scope.shipping =result.data.records[0];

                    if($scope.shipping.boxing_type == '0') {
                        $scope.shipping.boxing_type = 'Retail';
                    }
                    if($scope.shipping.boxing_type == '1') {
                        $scope.shipping.boxing_type = 'Standard';
                    }
                    if($scope.shipping.shipping_type_id == '1') {
                        $scope.shipping.shipping_type_id = 'USPS';
                    }
                    if($scope.shipping.shipping_type_id == '2') {
                        $scope.shipping.shipping_type_id = 'Fedex';
                    }
                }
            });
        }
        $scope.getShippingOverview();

        $scope.submitForm = function()
        {
            var target;
            var form = document.createElement("form");
            form.action = 'api/public/shipping/createLabel';
            form.method = 'post';
            form.target = target || "_blank";
            form.style.display = 'none';

            var shipping = document.createElement('input');
            shipping.name = 'shipping';
            shipping.setAttribute('value', JSON.stringify($scope.shipping));
            form.appendChild(shipping);

            document.body.appendChild(form);
            form.submit();
        }

        $scope.printLAbel = function()
        {
            if($scope.shipping_label == false || $scope.shipping_label == undefined)
            {
                notifyService.notify('error','Please select print option');
                return false;
            }

            $http.post('api/public/shipping/checkAddressValid',$scope.shipping).success(function(result) {

                if(result.data.success == '1')
                {
                    $scope.submitForm();
                }
                else
                {
                    notifyService.notify('error',result.data.message);
                    return false;
                }
            });

            /*var target;
            var form = document.createElement("form");
            form.action = 'api/public/shipping/createLabel';
            form.method = 'post';
            form.target = target || "_blank";
            form.style.display = 'none';

            var shipping = document.createElement('input');
            shipping.name = 'shipping';
            shipping.setAttribute('value', JSON.stringify($scope.shipping));
            form.appendChild(shipping);

            document.body.appendChild(form);
            form.submit();*/
        }
    }
})();
