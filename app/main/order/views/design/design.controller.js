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
        $scope.company_id = sessionService.get('company_id');
        $scope.valid_sns = 1;

        $scope.calculateAll = function(order_id,company_id)
        {
            $http.get('api/public/order/calculateAll/'+order_id+'/'+company_id).success(function(result) 
            {
                $scope.designProductData();
            });
        }

        $scope.orderDetail = function(){
            $("#ajax_loader").show();
            
            var combine_array_id = {};
            combine_array_id.id = $scope.order_id;
            combine_array_id.company_id = sessionService.get('company_id');

            $http.post('api/public/order/orderDetail',combine_array_id).success(function(result, status, headers, config) {
                if(result.data.success == '1') {
                    $("#ajax_loader").hide();
                   $scope.order = result.data.records[0];
                   $scope.order_items = result.data.order_item;
                } else {
                    $state.go('app.order');
                }
            });
        }

       $scope.designDetail = function(){
         $("#ajax_loader").show();
        var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            
            $http.post('api/public/order/designDetail',combine_array_id).success(function(result, status, headers, config) {
               
                $("#ajax_loader").hide();
                if(result.data.success == '1') {
                     
                    $scope.order_id = result.data.records[0].order_id;
                    $scope.price_id = result.data.records[0].price_id;


                     var allData = {};
                    allData.table ='price_grid';
                    allData.cond ={id:result.data.records[0].price_id}

                    
                    $http.post('api/public/common/GetTableRecords',allData).success(function(result) 
                    {   
                        if(result.data.success=='1')
                        {   
                            $scope.all_price_grid = result.data.records[0];
                        }
                        else
                        {
                           $scope.all_price_grid = '';
                        }
                    });



                    $scope.order_number = result.data.records[0].order_number;
                    $scope.is_complete = result.data.records[0].is_complete;
                    $scope.designInforamtion = result.data.records[0];

                    $scope.calculateAll($scope.order_id,$scope.company_id);
                    $scope.orderDetail();

                } else {
                    $state.go('app.order');
                }
                
            });
        }

        $scope.designDetail();

        $scope.designProductData = function(){
            $("#ajax_loader").show();
            var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            
            $http.post('api/public/product/designProduct',combine_array_id).success(function(result, status, headers, config) {
                
                $("#ajax_loader").hide();
                if(result.data.success == '1') {
                    $scope.productData = result.data.productData;
                    $scope.total_product = result.data.total_product;
                    $scope.total_price = result.data.total_price;
                }
                else{
                    $scope.productData = [];
                    $scope.total_product = 0;
                    $scope.total_price = 0;
                }
            });
        }

       $scope.designPosition = function(){

        var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            combine_array_id.company_id = sessionService.get('company_id');
            $scope.total_pos_qnty = 0;
            
            $http.post('api/public/order/getDesignPositionDetail',combine_array_id).success(function(result, status, headers, config) {
               
                if(result.data.success == '1') {

                    $scope.position_array_count = result.data.order_design_position.length;



                    $scope.order_design_position = result.data.order_design_position;
                    $scope.total_pos_qnty = result.data.total_pos_qnty;
                    $scope.total_screen_fees = result.data.total_screen_fees;
                }
                else{
                    $scope.order_design_position = [];
                    $scope.total_pos_qnty = 0;
                    $scope.total_screen_fees = 0;
                }
            });
        }

        $scope.designPosition();

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

        $scope.designPositionNew = function(){

        var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            combine_array_id.company_id = sessionService.get('company_id');
            $scope.total_pos_qnty = 0;
            
            $http.post('api/public/order/getDesignPositionDetail',combine_array_id).success(function(result, status, headers, config) {
               
                if(result.data.success == '1') {
                   
                    $scope.total_pos_qnty = result.data.total_pos_qnty;
                    $scope.total_screen_fees = result.data.total_screen_fees;
                }
                else
                {
                    $scope.total_pos_qnty = 0;
                    $scope.total_screen_fees = 0;
                }
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
            position_main_data.order_id = $scope.order_id;
            position_main_data.design_id = $stateParams.id;
            position_main_data.company_id = sessionService.get('company_id');
            position_main_data.column_name = $scope.name_filed;
            
            if(column_name == 'position_id') {
                position_main_data.position = $scope.miscData.position[value].value;
            } 


          
            $http.post('api/public/order/updatePositions',position_main_data).success(function(result) {

                        if(result.data.success == '2') 

                        {

                             var data = {"status": "error", "message": "This position already exists in this design."}
                             notifyService.notify(data.status, data.message);
                            
                             $scope.order_design_position[key].position_id = $scope.order_design_position[key].duplicate_position_id;
                             return false;
                        } 

                if(column_name == 'position_id') {
                    $scope.order_design_position[key].position_header_name = $scope.miscData.position[value].value;
                    $scope.order_design_position[key].duplicate_position_id = $scope.miscData.position[value].id;
                  
                }

                if(column_name == 'color_stitch_count') {
                    $scope.order_design_position[key].screen_fees_qnty = value;
                    $scope.order_design_position[key].stitch_header_name = value;
                  
                }

                $scope.order_design_position[key].total_price = ($scope.order_design_position[key].number_on_dark_qnty * $scope.all_price_grid['number_on_dark'] ) + ($scope.order_design_position[key].oversize_screens_qnty * $scope.all_price_grid['over_size_screens']) + ($scope.order_design_position[key].ink_charge_qnty * $scope.all_price_grid['ink_changes']) + ($scope.order_design_position[key].number_on_light_qnty * $scope.all_price_grid['number_on_light']) + ($scope.order_design_position[key].press_setup_qnty * $scope.all_price_grid['press_setup']) + ($scope.order_design_position[key].discharge_qnty * $scope.all_price_grid['discharge']) + ($scope.order_design_position[key].speciality_qnty * $scope.all_price_grid['specialty']) + ($scope.order_design_position[key].screen_fees_qnty * $scope.all_price_grid['screen_fees']) + ($scope.order_design_position[key].foil_qnty * $scope.all_price_grid['foil']);
                
                if($scope.order_design_position[key].total_price > 0) {

                  
                       $scope.order_design_position[key].total_price = $scope.order_design_position[key].total_price.toFixed(2); 

                }
                if(column_name == 'qnty') {
                    $scope.order_design_position[key].qnty_header_name = value ;
                  
                }


                var data = {"status": "success", "message": "Positions Updated Successfully."}
                notifyService.notify(data.status, data.message);
                $scope.designProductData();
                $scope.designPositionNew();
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
        vendor_data ={'company_id':condition_obj['company_id']}
        $http.post('api/public/product/getVendorByProductCount',vendor_data).success(function(result) {
            
            if(result.data.success == '1') 
            {
                $scope.allVendors =result.data.records;
            } 
            else
            {
                $scope.allVendors=[];
            }
        });

        var vm = this;

        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };

        vm.dtInstanceCB = dtInstanceCB;
        $scope.openAddProductDialog = openAddProductDialog;
        $scope.openaddDesignDialog = openaddDesignDialog;
        $scope.openSearchProductDialog = openSearchProductDialog;
        $scope.openPositionDialog = openPositionDialog;

        //methods
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
        }

        


        function openPositionDialog(ev,order_id,quantity)
        {
            
                $mdDialog.show({
                    controller: 'PositionDialogController',
                    controllerAs: $scope,
                    templateUrl: 'app/main/order/dialogs/position/position-dialog.html',
                    parent: angular.element($document.body),
                    targetEvent: ev,
                    clickOutsideToClose: true,
                    locals: {
                        event: ev,
                        order_id: order_id,
                        quantity: quantity
                    },
                    onRemoving : $scope.designPosition
                });
            }
           
        
        
        function openAddProductDialog(ev,controller, file,product_id,operation,color_id,is_supply,design_product_id,vendor_id,size_group_id)
        {
            /*if($scope.order_design_position.length == '0')
            {
                var data = {"status": "error", "message": "Please add position"}
                notifyService.notify(data.status, data.message);
                $scope.productSearch = '';
                return false;
            }
            else if($scope.total_pos_qnty == '0')
            {
                var data = {"status": "error", "message": "Please enter position quantity"}
                notifyService.notify(data.status, data.message);
                $scope.productSearch = '';
                return false;
            }*/

            $mdDialog.show({
                controller: controller,
                controllerAs: $scope,
                templateUrl: file,
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    product_id: product_id,
                    operation:operation,
                    design_id:$stateParams.id,
                    color_id:color_id,
                    vendor_id:vendor_id,
                    is_supply:is_supply,
                    size_group_id:size_group_id,
                    event: ev
                },
                onRemoving : $scope.designProductData
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
            $scope.vendorProducts = 0;
            if($scope.vendor_id == 1)
            {
                var combine_array_id = {};
                combine_array_id.company_id = sessionService.get('company_id');
                $("#ajax_loader").show();
                $http.post('api/public/product/checkSnsAuth',combine_array_id).success(function(result) {
                   
                    $("#ajax_loader").hide();
                    if(result.data.success == '0') {
                        var data = {"status": "error", "message": "Please enter valid credentials for S&S"}
                        notifyService.notify(data.status, data.message);
                        $scope.productSearch = '';
                        $scope.valid_sns = 0;
                    }
                });
            }
            if($scope.vendor_id > 0)
            {
                var vendor_data = {};
                vendor_data ={'vendor_id':$scope.vendor_id}
                $http.post('api/public/product/getProductCountByVendor',vendor_data).success(function(result) {
                    
                    if(result.data.success == '0')
                    {
                        var data = {"status": "error", "message": result.data.message}
                        notifyService.notify(data.status, data.message);
                        $scope.productSearch = '';
                    }
                    else
                    {
                        $scope.vendorProducts = 1;
                    }
                });
            }
        }

        $scope.checkValid = function()
        {
            if($scope.vendor_id == 0)
            {
                var data = {"status": "error", "message": "Please select vendor to add product"}
                notifyService.notify(data.status, data.message);
                $scope.productSearch = '';
            }
            else if($scope.vendorProducts == 0)
            {
                var data = {"status": "error", "message": "No products available for this vendor"}
                notifyService.notify(data.status, data.message);
                $scope.productSearch = '';
            }
            else if($scope.valid_sns == 0)
            {
                var data = {"status": "error", "message": "Please enter valid credentials for S&S"}
                notifyService.notify(data.status, data.message);
                $scope.productSearch = '';
            }
/*            else if($scope.order_design_position.length == '0')
            {
                var data = {"status": "error", "message": "Please add position"}
                notifyService.notify(data.status, data.message);
                $scope.productSearch = '';
            }
            else if($scope.total_pos_qnty == '0')
            {
                var data = {"status": "error", "message": "Please enter position quantity"}
                notifyService.notify(data.status, data.message);
                $scope.productSearch = '';
            }*/
        }

        // ============= UPLOAD IMAGE ============= // 
        $scope.ImagePopup = function (column_name,folder_name,table_name,default_image,primary_key_name,primary_key_value,image_name,key) 
        {

                $scope.column_name=column_name;
                $scope.table_name=table_name;
                $scope.folder_name=folder_name;
                $scope.primary_key_name=primary_key_name;
                $scope.primary_key_value=primary_key_value;
                $scope.default_image=default_image;
                $scope.company_id = sessionService.get('company_id');
                $scope.unlink_url = image_name;
                $scope.key = key;


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
                                        var array_key = params.key;
                                        var array_column_name = params.column_name;
                                        var array_column_name_url = params.column_name+'_url_photo';
                                        var image_path_url = Image_data.image_path;
                                        var path = AllConstant.base_path;
                                        
                                        params.order_design_position[array_key][array_column_name] = result.data.records;
                                        params.order_design_position[array_key][array_column_name_url] = path+'api/public/uploads/'+image_path_url+'/'+result.data.records;                                             ;
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
                        }
                });


        };


         $scope.deleteImage=function(e,column_name,folder_name,table_name,default_image,primary_key_name,primary_key_value,key)
         {
            
              if(default_image == '') {

                var data = {"status": "error", "message": "Please upload image first."}
                          notifyService.notify(data.status, data.message);
                           e.stopPropagation(); // Stop event from bubbling up
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
                                         
                     var column_name_url = column_name+'_url_photo';     
                     $scope.order_design_position[key][column_name] = '';
                     $scope.order_design_position[key][column_name_url] = '';
                });
            }

          e.stopPropagation(); // Stop event from bubbling up
        }

          $scope.openSearchProductViewDialogView = function(ev,product_id,product_image,description,vendor_name,operation,product_name,colorName,design_product_id,size_group_id,warehouse)
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
                    design_product_id:design_product_id,
                    size_group_id:size_group_id,
                    warehouse:warehouse,
                    event: ev
                },
                onRemoving : $scope.designProductData
               
            });
        }

        $scope.deleteAddProduct = function(product_id){

            var permission = confirm(AllConstant.deleteMessage);

            if (permission == true) {

                var combine_array_id = {};
                combine_array_id.design_id = $stateParams.id;
                combine_array_id.product_id = product_id;
                    
                $http.post('api/public/product/deleteAddProduct',combine_array_id).success(function(result, status, headers, config) {
                       
                    if(result.data.success == '1') {
                           notifyService.notify('success','Product Deleted Successfully.');
                           $scope.designProductData();
                    } 
                });
              }
          }

        $scope.update_override = function(product_id) {

            var override_data = {};
            override_data['productData'] = $scope.productData[product_id];
            override_data['company_id'] = sessionService.get('company_id');
            override_data['design_id'] = $stateParams.id;

            $http.post('api/public/order/updateOverride',override_data).success(function(result) {
                $scope.designProductData();
            });
        }


        $scope.UpdateTableField = function(field_name,field_value,table_name,cond_field,cond_value)
        {
            var vm = this;
            var UpdateArray = {};
            UpdateArray.table =table_name;
            
            $scope.name_filed = field_name;
            var obj = {};
            obj[$scope.name_filed] =  field_value;
            UpdateArray.data = angular.copy(obj);

            var condition_obj = {};
            condition_obj[cond_field] =  cond_value;
            UpdateArray.cond = angular.copy(condition_obj);
            UpdateArray.order_id = $scope.order_id;
            UpdateArray.company_id = $scope.company_id;

            var permission = confirm(AllConstant.deleteMessage);
                if (permission == true)
                {

                $http.post('api/public/order/deleteOrderCommon',UpdateArray).success(function(result) {
                    if(result.data.success=='1')
                    {
                        notifyService.notify('success','Record Deleted Successfully.');
                        $scope.designDetail();
                        $scope.designPosition();
                        $scope.designProductData();
                    }
                    else
                    {
                        notifyService.notify('error',result.data.message);
                    }
                   });
                 }
        } 
        
        $scope.update_markup = function(product_id)
        {
            var markup_data = {};
            markup_data['productData'] = $scope.productData[product_id];
            markup_data['company_id'] = sessionService.get('company_id');
            markup_data['design_id'] = $stateParams.id;

            $http.post('api/public/order/updateMarkup',markup_data).success(function(result) {
                $scope.designProductData();
            });
        }

        $scope.assign_item = function(item,item_name,item_charge,item_id,product){
            
            $("#ajax_loader").show();
            var item_array = {
                                'item':item,
                                'item_name':item_name,
                                'item_charge':item_charge,
                                'item_id':item_id,
                                'order_id':$scope.order_id,
                                'company_id':sessionService.get('company_id'),
                                'product':product
                            };

            $http.post('api/public/finishing/addRemoveToFinishing',item_array).success(function(result) {

                $("#ajax_loader").hide();
                if(result.data.success == '1') {
                    $scope.designProductData();
                }
                else {
                    var data = {"status": "error", "message": result.data.message}
                    notifyService.notify(data.status, data.message);               
                }
                
            });
        }

        $scope.confirmPricing = function()
        {
            $scope.calculateAll($scope.order_id,$scope.company_id);
        }
    }
})();