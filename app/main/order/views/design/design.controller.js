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

       $scope.designDetail = function(){
         $("#ajax_loader").show();
        var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            
            $http.post('api/public/order/designDetail',combine_array_id).success(function(result, status, headers, config) {
               
                if(result.data.success == '1') {
                    
                     $("#ajax_loader").hide();
                    $scope.order_id = result.data.records[0].order_id;

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
                  
//                    $scope.designProduct = result.data.records;
                    $scope.productData = result.data.productData;
//                    $scope.colorName = result.data.colorName;
//                    $scope.colorId = result.data.colorId;
//                    $scope.is_supply = result.data.is_supply;
//                    $scope.calculate_data = result.data.calculate_data[0];
//                    $scope.productData.product_image_view = "https://www.ssactivewear.com/"+$scope.productData.product_image;


                } else {
                    
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
                    $scope.order_design_position = result.data.order_design_position;
                    $scope.total_pos_qnty = result.data.total_pos_qnty;
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


        $scope.designPositionNew = function(){

        var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            combine_array_id.company_id = sessionService.get('company_id');
            $scope.total_pos_qnty = 0;
            
            $http.post('api/public/order/getDesignPositionDetail',combine_array_id).success(function(result, status, headers, config) {
               
                if(result.data.success == '1') {
                   
                    $scope.total_pos_qnty = result.data.total_pos_qnty;
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
          
            $http.post('api/public/order/updatePositions',position_main_data).success(function(result) {
                if(column_name == 'position_id') {
                    $scope.order_design_position[key].position_name = $scope.miscData.position[value].value;
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
        vendor_data.table ='vendors';
        /*vendor_data.cond ={'company_id':condition_obj['company_id']}*/
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

        var combine_array_id = {};
        combine_array_id.company_id = sessionService.get('company_id');

        $scope.valid_sns = 1;
        
        $http.post('api/public/product/checkSnsAuth',combine_array_id).success(function(result) {
           
            if(result.data.success == '0') {
                $scope.valid_sns = 0;
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
        
        function openAddProductDialog(ev,controller, file,product_id,operation,color_id,is_supply)
        {
            if($scope.order_design_position.length == '0')
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
            }

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
                    is_supply:is_supply,
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
            if($scope.valid_sns == 0)
            {
                var data = {"status": "error", "message": "Please enter valid credentials for S&S"}
                notifyService.notify(data.status, data.message);
                $scope.productSearch = '';
            }
            else if($scope.order_design_position.length == '0')
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
            }

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

        $scope.deleteAddProduct = function(){

            var permission = confirm(AllConstant.deleteMessage);

            if (permission == true) {

                var combine_array_id = {};
                    combine_array_id.id = $stateParams.id;
                    
                    
                    $http.post('api/public/product/deleteAddProduct',combine_array_id).success(function(result, status, headers, config) {
                       
                        if(result.data.success == '1') {
                           $scope.productData = {};
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

                $http.post('api/public/order/deletePositions',UpdateArray).success(function(result) {
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
    }
})();
