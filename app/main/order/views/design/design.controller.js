(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('DesignController', DesignController);

    /** @ngInject */
    function DesignController($window, $timeout,$filter,$scope,$stateParams, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService,$log)
    {

     
       $scope.designDetail = function(){

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
        }

        $scope.designPosition = function(){

        var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            
            $http.post('api/public/order/getDesignPositionDetail',combine_array_id).success(function(result, status, headers, config) {
               
                if(result.data.success == '1') {
                    $scope.order_design_position = result.data.order_design_position;
                }
                
            });
        }


        var misc_list_data = {};
        var condition_obj = {};
        condition_obj['company_id'] =  sessionService.get('company_id');
        misc_list_data.cond = angular.copy(condition_obj);

        $http.post('api/public/common/getAllMiscDataWithoutBlank',misc_list_data).success(function(result, status, headers, config) {
                  $scope.miscData = result.data.records;
        });


        $scope.designDetail();
        $scope.designPosition();

        var vm = this;
        //Dummy models data
        
      
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
         vm.openAddProductDialog = openAddProductDialog;
          vm.openaddDesignDialog = openaddDesignDialog;
          vm.openSearchProductDialog = openSearchProductDialog;
        //methods
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
        }
        
          function openAddProductDialog(ev, order)
        {
            $mdDialog.show({
                controller: 'AddProductController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/addProduct/addProduct.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    Order: order,
                    Orders: vm.orders,
                    event: ev
                }
            });
        }
          function openaddDesignDialog(ev, order)
        {
            $mdDialog.show({
                controller: 'AddDesignController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/addDesign/addDesign.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    Order: order,
                    Orders: vm.orders,
                    event: ev
                }
            });
        }
          function openSearchProductDialog(ev)
        {
            $mdDialog.show({
                controller: 'SearchProductController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/searchProduct/searchProduct.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,

            });
        }


        vm.productSearch = null;
        vm.productVendorLogo = [{"id": "1", "name": "Vendor Logo"},
            {"id": "1", "name": "Vendor Logo"},
            {"id": "1", "name": "Vendor Logo"},
            {"id": "1", "name": "Vendor Logo"},
            {"id": "1", "name": "Vendor Logo"},
            {"id": "1", "name": "Vendor Logo"},
            {"id": "1", "name": "Vendor Logo"}
        ];
    }
})();
