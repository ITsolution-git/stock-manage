(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('DesignController', DesignController);

    /** @ngInject */


    function DesignController($window, $timeout,$filter,$scope,$stateParams, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService,$log,AllConstant)
    {
        $scope.NoImage = AllConstant.NoImage;
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

                } else {
                    $state.go('app.order');
                }
                
            });
        }

        $scope.designProductData = function(){

        var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            
            $http.post('api/public/product/designProduct',combine_array_id).success(function(result, status, headers, config) {
               
                if(result.data.success == '1') {
                  
                    $scope.designProduct = result.data.records;
                    $scope.productData = result.data.productData.product[0];
                    $scope.colorName = result.data.colorName;
                    $scope.productData.product_image_view = "https://www.ssactivewear.com/"+$scope.productData.product_image;


                } else {
                    
                }
                
            });
        }

       $scope.designPosition = function(){

        var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            combine_array_id.company_id = sessionService.get('company_id');
            
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
                var data = {"status": "success", "message": "Positions Added Successfully."}
                     notifyService.notify(data.status, data.message);
               
            });
        }


          $scope.updateDesignPosition = function(column_name,id,value,table_name,match_condition,key)
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
                if(column_name == 'position_id') {
                    $scope.order_design_position[key].position_name = $scope.miscData.position[value].value;

                }
                var data = {"status": "success", "message": "Positions Updated Successfully."}
                     notifyService.notify(data.status, data.message);
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
        $scope.designProductData();

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


        // ============= UPLOAD IMAGE ============= // 
        $scope.ImagePopup = function (column_name,folder_name,table_name,default_image,primary_key_name,primary_key_value,image_name) 
        {

                $scope.column_name=column_name;
                $scope.table_name=table_name;
                $scope.folder_name=folder_name;
                $scope.primary_key_name=primary_key_name;
                $scope.primary_key_value=primary_key_value;
                $scope.default_image=default_image;
                $scope.company_id = sessionService.get('company_id');
                $scope.unlink_url = image_name;


                $mdDialog.show({
                   //controllerAs: $scope,
                    controller: function($scope,params){
                            $scope.params = params;
                            $scope.SaveImageAll=function(image_array)
                            {
                                 if(image_array == null) {
                                    $mdDialog.hide();
                                    return false;
                                  }

                                var Image_data = {};
                                Image_data.image_array = image_array;
                                Image_data.field = params.column_name;
                                Image_data.table = params.table_name;
                                Image_data.image_name = params.table_name+"-logo";
                                Image_data.image_path = params.company_id+"/"+params.folder_name+"/"+params.primary_key_value;
                                Image_data.cond = params.primary_key_name;
                                Image_data.value = params.primary_key_value;
                                Image_data.unlink_url = params.unlink_url;

                                $http.post('api/public/common/SaveImage',Image_data).success(function(result) {
                                    if(result.data.success=='1')
                                    {
                                        notifyService.notify("success", result.data.message);
                                        $mdDialog.hide();
                                    }
                                    else
                                    {
                                        notifyService.notify("error", result.data.message); 
                                    }
                                });
                            };
                            $scope.showtcprofileimg = false;
                            $scope.onLoad=function()
                                {
                                    $scope.showtcprofileimg = true;
                                }; 
                            $scope.removeProfileImage=function()
                                {
                                    $scope.showtcprofileimg = false;
                                }; 
                            $scope.closeDialog = function() 
                            {
                                $mdDialog.hide();
                            } 
                        },
                    templateUrl: 'app/main/image/image.html',
                    parent: angular.element($document.body),
                    clickOutsideToClose: false,
                        locals: {
                            params:$scope
                        },
                    onRemoving : $scope.designPosition
                });


        };


         $scope.deleteImage=function(column_name,folder_name,table_name,default_image,primary_key_name,primary_key_value)
         {

              if(default_image == '') {

                var data = {"status": "error", "message": "Please upload image first."}
                          notifyService.notify(data.status, data.message);
                          return false;
              }

              
              var permission = confirm(AllConstant.deleteImage);

            if (permission == true) {

                  var order_main_data = {};
                  order_main_data.table =table_name
                  $scope.name_filed = column_name;

                  var obj = {};
                  obj[$scope.name_filed] =  '';
                  order_main_data.data = angular.copy(obj);

                  var cond = {};
                  cond[primary_key_name] =  primary_key_value;
                  order_main_data.cond = angular.copy(cond);

                
                 order_main_data.image_delete =  sessionService.get('company_id')+'/'+folder_name+'/' + primary_key_value +'/'+default_image;
            
                $http.post('api/public/common/deleteImage',order_main_data).success(function(result) {
                    $mdDialog.hide();

                    var data = {"status": "success", "message": "Image Deleted Successfully"}
                                            notifyService.notify(data.status, data.message); 
                     $scope.designPosition;
                });

                
            }


        }

          $scope.openSearchProductViewDialogView = function(ev,product_id,product_image,description,vendor_name,operation,product_name,colorName)
        {
         
            $mdDialog.show({
                controller: 'SearchProductViewController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/searchProductView/searchProductView.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    product_id: product_id,
                    product_image:product_image,
                    description:description,
                    vendor_name:vendor_name,
                    operation:operation,
                    product_name:product_name,
                    colorName:colorName,
                    design_id:$stateParams.id,
                    event: ev
                },
                onRemoving : $scope.designProductData
               
            });
        }

       
    }
})();
