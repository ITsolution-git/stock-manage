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
        $scope.vendor_id = 0;

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



        $scope.addPosition = function(){

            var position_data_insert = {};
            position_data_insert.table ='order_design_position'
            position_data_insert.data ={design_id:$stateParams.id}

            $http.post('api/public/common/InsertRecords',position_data_insert).success(function(result) {
                
               $scope.designPosition();
               
            });
        }


          $scope.updateDesignPosition = function(column_name,id,value,table_name,match_condition)
        {
           
          var position_main_data = {};
          position_main_data.table =table_name;
          $scope.name_filed = column_name;
          
          var obj = {};
          obj[$scope.name_filed] =  value;
          position_main_data.data = angular.copy(obj);


          var condition_obj = {};
          condition_obj[match_condition] =  id;
          position_main_data.cond = angular.copy(condition_obj);
          

            $http.post('api/public/common/UpdateTableRecords',position_main_data).success(function(result) {
               
            });
      
        }




        var misc_list_data = {};
        var condition_obj = {};
        condition_obj['company_id'] =  sessionService.get('company_id');
        misc_list_data.cond = angular.copy(condition_obj);

        $http.post('api/public/common/getAllMiscDataWithoutBlank',misc_list_data).success(function(result, status, headers, config) {
                  $scope.miscData = result.data.records;
        });

        var vendor_data = {};
        vendor_data.table ='vendors';
        vendor_data.cond ={'company_id':condition_obj['company_id']}
        $http.post('api/public/common/GetTableRecords',vendor_data).success(function(result) {
            
            if(result.data.success == '1') 
            {
                $scope.allVendors =result.data.records;
            } 
            else
            {
                $scope.allVendors=[];
            }
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
            if($scope.vendor_id > 0)
            {
                var data = {'productSearch': $scope.productSearch,'vendor_id': $scope.vendor_id, 'vendors': $scope.allVendors};

                $mdDialog.show({
                    controller: 'SearchProductController',
                    controllerAs: $scope,
                    templateUrl: 'app/main/order/dialogs/searchProduct/searchProduct.html',
                    parent: angular.element($document.body),
                    targetEvent: ev,
                    clickOutsideToClose: true,
                    locals: {
                        data: data,
                        event: ev
                    }
                });
            }
            else
            {
                var data = {"status": "error", "message": "Please select vendor"}
                notifyService.notify(data.status, data.message);
            }
        }

        $scope.checkVendor = function()
        {
            if($scope.vendor_id == '0')
            {
                var data = {"status": "error", "message": "Please select vendor"}
                notifyService.notify(data.status, data.message);
            }
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
