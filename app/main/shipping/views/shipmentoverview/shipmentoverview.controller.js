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

        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='AT' || $scope.role_slug=='SU')
        {
            $scope.allow_access = 0;
        }
        else
        {
            $scope.allow_access = 1;
        }

        $scope.shipping_id = $stateParams.id;

        var company_id = sessionService.get('company_id');


         var combine_array_id = {};
        combine_array_id.company_id = sessionService.get('company_id');

        $http.post('api/public/common/getCompanyDetail',combine_array_id).success(function(result) {
                            
            if(result.data.success == '1') 
            {
                $scope.allCompanyDetail =result.data.records;
            } 
            else
            {
                $scope.allCompanyDetail=[];
            }
        });

        $scope.getShippingOverview = function()
        {
            $("#ajax_loader").show();
            var combine_array = {};
            combine_array.shipping_id = $scope.shipping_id;
            combine_array.company_id = company_id;


            $http.post('api/public/shipping/getShippingOverview',combine_array).success(function(result) {

                $("#ajax_loader").hide();
                if(result.data.success == '1') 
                {
                    $scope.shippingBoxes =result.data.shippingBoxes;
                    $scope.shippingItems =result.data.shippingItems;
                    $scope.shipping =result.data.records[0];
                    $scope.shipping.company_id = company_id;

                    if($scope.shipping.boxing_type == '0') {
                        $scope.shipping.boxing_type = 'Retail';
                    }
                    if($scope.shipping.boxing_type == '1') {
                        $scope.shipping.boxing_type = 'Standard';
                    }
                    if($scope.shipping.shipping_type_id == '1') {
                        $scope.shipping.shipping_type_name = 'UPS';
                    }
                    if($scope.shipping.shipping_type_id == '2') {
                        $scope.shipping.shipping_type_name = 'Fedex';
                    }
                    if($scope.shipping.shipping_type_id == '3') {
                        $scope.shipping.shipping_type_name = 'Local Messenger';
                    }

                    if($scope.shipping.shipping_status == '1') {
                        $scope.shipping.shipping_status_name = 'Waiting To Ship';
                    }
                    if($scope.shipping.shipping_status == '2') {
                        $scope.shipping.shipping_status_name = 'Order In Process';
                    }
                    if($scope.shipping.shipping_status == '3') {
                        $scope.shipping.shipping_status_name = 'Shipped';
                    }
                } else {

                    $state.go('app.shipping');
                    return false;
                }
            });
        }

        var allData = {};
        allData.table ='shipping';
        allData.cond ={display_number:$stateParams.id,company_id:sessionService.get('company_id')}

        $http.post('api/public/common/GetTableRecords',allData).success(function(result)
        {   
            if(result.data.success=='1')
            {   
                $scope.shipping_id = result.data.records[0].id;
                $scope.display_number = result.data.records[0].display_number;
                $scope.getShippingOverview();
            }
        });

        $scope.submitForm = function(image)
        {
            //window.location.href = 'data:image/png;base64,' + image;

            window.open(
              'data:image/png;base64,' + image,
              '_blank' // <- This is what makes it open in a new window.
            );

/*            var target;
            var form = document.createElement("form");
            form.action = 'data:image/png;base64,' + image;

            var shipping = document.createElement('input');
            shipping.name = 'shipping';
            shipping.setAttribute('value', JSON.stringify($scope.shipping));
            form.appendChild(shipping);

            document.body.appendChild(form);
            form.submit();*/
        }

        $scope.printLAbel = function()
        {
            if($scope.shipping.tracking_number != '')
            {
                notifyService.notify('error','Shipping label is already created');
                return false;
            }
            if($scope.shippingBoxes.length == 0)
            {
                notifyService.notify('error','Please create box to print label');
                return false;
            }
            if($scope.shipping.address == '')
            {
                notifyService.notify('error','address is compulsory');
                return false;
            }
            if($scope.shipping.address2 == '')
            {
                notifyService.notify('error','address2 is compulsory');
                return false;
            }
            if($scope.shipping.city == '')
            {
                notifyService.notify('error','city is compulsory');
                return false;
            }
            if($scope.shipping.code == '')
            {
                notifyService.notify('error','state is compulsory');
                return false;
            }
            if($scope.shipping.zipcode == '')
            {
                notifyService.notify('error','zipcode is compulsory');
                return false;
            }
            if($scope.shipping.shipping_type_id == '' || $scope.shipping.shipping_type_id == 0)
            {
                notifyService.notify('error','Please select shipping method');
                return false;
            }

            $("#ajax_loader").show();
            var combine_array = {};
            combine_array.shipping = $scope.shipping;
            combine_array.shippingBoxes = $scope.shippingBoxes;
            $http.post('api/public/shipping/checkAddressValid',combine_array).success(function(result) {

                $("#ajax_loader").hide();
                if(result.data.success == '1')
                {
                    $scope.getShippingOverview();
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

        $scope.print_pdf = function(method)
        {
            if($scope.shippingBoxes.length == 0)
            {
                notifyService.notify('error','Please create box to generate pdf');
                return false;
            }

            var target;
            var form = document.createElement("form");
            form.action = 'api/public/shipping/createPDF';
            form.method = 'post';
            form.target = '_blank';
            form.style.display = 'none';

            var print_type = document.createElement('input');
            print_type.name = 'print_type';
            print_type.setAttribute('value', method);
            form.appendChild(print_type);

            var shipping = document.createElement('input');
            shipping.name = 'shipping';
            shipping.setAttribute('value', JSON.stringify($scope.shipping));
            form.appendChild(shipping);

            var shipping_items = document.createElement('input');
            shipping_items.name = 'shipping_items';
            shipping_items.setAttribute('value', JSON.stringify($scope.shippingItems));
            form.appendChild(shipping_items);

            var shipping_boxes = document.createElement('input');
            shipping_boxes.name = 'shipping_boxes';
            shipping_boxes.setAttribute('value', JSON.stringify($scope.shippingBoxes));
            form.appendChild(shipping_boxes);

            var input_company_detail = document.createElement('input');
            input_company_detail.name = 'company_detail';
            input_company_detail.setAttribute('value', JSON.stringify($scope.allCompanyDetail));
            form.appendChild(input_company_detail);

            var input_pdf = document.createElement('input');
            input_pdf.name = 'pdf_token';
            input_pdf.setAttribute('value', 'pdf_token');
            form.appendChild(input_pdf);

            document.body.appendChild(form);
            form.submit();
        }

        $scope.box_shipment = function()
        {
            $("#ajax_loader").show();
            $http.post('api/public/shipping/CreateBoxShipment',$scope.shippingItems).success(function(result) {

                if(result.data.success == '1') {
                    var data = {"status": "success", "message": "Boxes created Successfully."}
                    notifyService.notify(data.status, data.message);
                }
                else
                {
                    var data = {"status": "info", "message": "Delete all boxes in the boxes tab to rebox shipment."}
                    notifyService.notify(data.status, data.message);
                }
                $("#ajax_loader").hide();
                $state.go('app.shipping.boxingdetail',{id: $stateParams.id});
            });
        }

        $scope.viewLabelPDF = function()
        {
            var target;
            var form = document.createElement("form");
            form.action = 'api/public/shipping/vewLabelPDF';
            form.method = 'post';
            form.target = '_blank';
            form.style.display = 'none';

            var shipping_id = document.createElement('input');
            shipping_id.name = 'shipping_id';
            shipping_id.setAttribute('value', $scope.shipping_id);
            form.appendChild(shipping_id);

            var input_pdf = document.createElement('input');
            input_pdf.name = 'pdf_token';
            input_pdf.setAttribute('value', 'pdf_token');
            form.appendChild(input_pdf);

            document.body.appendChild(form);
            form.submit();
        }
    }
})();
