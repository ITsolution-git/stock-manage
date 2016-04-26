(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('DesignController', DesignController);

    /** @ngInject */
    function DesignController($window, $timeout,$filter,$scope,$stateParams, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService,$log)
    {
        $scope.productSearch = '';
     
        var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            
            $http.post('api/public/order/designDetail',combine_array_id).success(function(result, status, headers, config) {
               
                if(result.data.success == '1') {
                    
                    
                    result.data.records[0].hands_date = new Date(result.data.records[0].hands_date);
                    result.data.records[0].shipping_date = new Date(result.data.records[0].shipping_date);
                    result.data.records[0].start_date = new Date(result.data.records[0].start_date);

                    $scope.designInforamtion = result.data.records[0];

                }
                
            });

        var vm = this;
        //Dummy models data
        
        vm.positionSelect = [{
                front: {id: "type1"}
            },
            {
                placement: {id: "type2"}
            },
            {
                dtgSize: {id: "type3"}
            },
            {
                dtgOn: {id: "type4"}
            }

        ];
        vm.positionOptions = [
            {abbrev: "Option-1"},
            {abbrev: "Option-2"}
        ];
        vm.positionInput = {
            "description": "",
            "colorCount": "",
            "foil": "",
            "numdesk": "",
            "oversizeSecrren": "",
            "qunty": "",
            "linkChange": "",
            "Numlight": "",
            "pressSetup": "",
            "discharge": "",
            "speciality": "",
            "oversize": "",
            "screenFree": "",
            "notes": ""
        };
        vm.size={
            s:"80",
            m:"100",
            l:"75",
            xl:"90"
        };
        vm.garmentCost={
            averageGarmentCost:"$2.25",
            markupDefault:"0%",
            averageGarmentPrice:"$3.83",
            PrintCharges:"$0.00",
            totalLineCharge:"$3.85",
            markup:"54",
            perItem:"0",
            saleTotal:"$3",
            overide:"10.5"
          
        };
        
        vm.position = [{
                "front": {
                    "desc": "Description", "notes": ""
                },
                "placementType": {
                    "colorCount": "", "qnty": '', "discharge": "", "foil": "", "inkChange": '', "speciality": ""
                },
                "dtgSize": {
                    "numberOnDark": '', "Number on Light": '', "Oversize": ''
                },
                "dtgOn": {
                    "oversizeScreen": "", "pressSetup": "", "screenFee": ""
                }
            }];
        vm.dtOptions = {
            dom: '<"top"f>rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };
        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };

        vm.dtInstanceCB = dtInstanceCB;
        $scope.openAddProductDialog = openAddProductDialog;
        $scope.openaddDesignDialog = openaddDesignDialog;
        $scope.openSearchProductDialog = openSearchProductDialog;

        //methods
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
        }
        
        function openAddProductDialog(ev, order)
        {
            $mdDialog.show({
                controller: 'AddProductController',
                controllerAs: $scope,
                templateUrl: 'app/main/order/dialogs/addProduct/addProduct.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    Order: order,
                    Orders: $scope.orders,
                    event: ev
                }
            });
        }
        function openaddDesignDialog(ev, order)
        {
            $mdDialog.show({
                controller: 'AddDesignController',
                controllerAs: $scope,
                templateUrl: 'app/main/order/dialogs/addDesign/addDesign.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    Order: order,
                    Orders: $scope.orders,
                    event: ev
                }
            });
        }
        function openSearchProductDialog(ev)
        {
            $mdDialog.show({
                controller: 'SearchProductController',
                controllerAs: $scope,
                templateUrl: 'app/main/order/dialogs/searchProduct/searchProduct.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    productSearch: $scope.productSearch,
                    event: ev
                }
            });
        }

        $scope.productVendorLogo = [{"id": "1", "name": "Vendor Logo"},
            {"id": "1", "name": "Vendor Logo"},
            {"id": "1", "name": "Vendor Logo"},
            {"id": "1", "name": "Vendor Logo"},
            {"id": "1", "name": "Vendor Logo"},
            {"id": "1", "name": "Vendor Logo"},
            {"id": "1", "name": "Vendor Logo"}
        ];
    }
})();
