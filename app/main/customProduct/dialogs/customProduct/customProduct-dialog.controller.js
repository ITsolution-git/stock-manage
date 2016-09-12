(function ()
{
    'use strict';

    angular
        .module('app.customProduct')
        .controller('CustomProductDialogController', CustomProductDialogController);
/** @ngInject */
    function CustomProductDialogController($scope, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService,$filter,$stateParams,AllConstant)
    {

     
        var product_id = $stateParams.id;
        $scope.product_id = $stateParams.id;
        $scope.company_id = sessionService.get('company_id');
        $scope.NoImage = AllConstant.NoImage;


         $scope.vendorList = function(){

          var vendor_data = {};
           vendor_data.cond ={company_id :sessionService.get('company_id'),is_delete :'1',status :'1'};
           vendor_data.table ='vendors';
           vendor_data.sort='id';
           vendor_data.sortcond='desc';

          
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
      }

       $scope.vendorList();

         

        if(product_id == 0) {

            var product_data = {};
            var productData = {};
            productData.company_id =sessionService.get('company_id');
           
            product_data.data = productData;
           
            product_data.data.created_date = $filter('date')(new Date(), 'yyyy-MM-dd');
            product_data.data.vendor_id =0;
            product_data.data.name ='';
           

            product_data.table ='products'

            $http.post('api/public/common/InsertRecords',product_data).success(function(result) {
               
                var id = result.data.id;
                
                getProductDetailByIdAll(id);
                 $scope.product_id_new  = id;
                 
                
            });
                       
                 
            } else {

                 getProductDetailByIdAll(product_id);
                 $scope.product_id_new  = product_id;
                // console.log($scope.product_data);return false;

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
                                    
                                        var image_path_url = Image_data.image_path;
                                        var path = AllConstant.base_path;
                                        $scope.params.product_image =result.data.records;
                                        $scope.params.product_image_url =path+'api/public/uploads/'+image_path_url+'/'+result.data.records;
                                        
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


         $scope.deleteImage=function(e,column_name,folder_name,table_name,default_image,primary_key_name,primary_key_value)
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
                       

                     $scope.product_image_url ='';
                     $scope.product_image ='';
                });
            }

          e.stopPropagation(); // Stop event from bubbling up
        }




         function getProductDetailByIdAll(id)
          {
             
             var combine_array_id = {}
                     combine_array_id.id = id;
                     combine_array_id.design_id = 0;
                     combine_array_id.company_id =sessionService.get('company_id');

              $http.post('api/public/product/getProductDetailColorSize',combine_array_id).success(function(result) {
                      
                      $scope.product_image_url =result.data.product_image_url;
                      $scope.product_image =result.data.product_image;

                      $scope.productName =result.data.product_name;
                      $scope.product_description =result.data.product_description;
                      $scope.productId =result.data.product_id;
                      $scope.vendor_id =result.data.vendor_id;
                      $scope.productColorSize =result.data.productColorSizeData;
                   
                      
               });
          }



        $scope.updateProduct = function(column_name,id,value,table_name,match_condition)
        {

            if(column_name == 'vendor_id' && value == '-1') {
              $scope.addVendor();
              return false;
            }
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
               return true;
            });
        }


        
        $scope.addcolorsize = function(product_id,color_id,size_id){
          
            if(product_id !=0 && color_id == 0 && size_id ==0) {
              var combine_array_id = {};

              combine_array_id.product_id = product_id;
              combine_array_id.color_id = color_id;
              combine_array_id.size_id = size_id;
              combine_array_id.company_id =sessionService.get('company_id');
              
              $http.post('api/public/product/addcolorsize',combine_array_id).success(function(result, status, headers, config) {
              
                  if(result.data.success == '1') {
                     getProductDetailByIdAll(product_id);
                  } 
              });
          } else if(product_id !=0 && color_id != 0 && size_id ==0) {

              var combine_array_id = {};

              combine_array_id.product_id = product_id;
              combine_array_id.color_id = color_id;
              combine_array_id.size_id = size_id;
              combine_array_id.company_id =sessionService.get('company_id');
              
              $http.post('api/public/product/addcolorsize',combine_array_id).success(function(result, status, headers, config) {
              
                  if(result.data.success == '1') {
                     getProductDetailByIdAll(product_id);
                  } 
              });
          }   

        }

       
        
        $scope.removeColorSize =  function(product_id,color_id,size_id){
          
          var permission = confirm("Are you sure want to delete this record ? Clicking Ok will delete record permanently.");

            if (permission == true) {

                var combine_array_id = {};
                    combine_array_id.product_id = product_id;
                    combine_array_id.color_id = color_id;
                    combine_array_id.size_id = size_id;
                    combine_array_id.company_id =sessionService.get('company_id');
                    
                    
                    $http.post('api/public/product/deleteSizeLink',combine_array_id).success(function(result, status, headers, config) {
                       
                        if(result.data.success == '1') {
                            getProductDetailByIdAll(product_id);
                        } 
                        
                    });
              }

        };

        $scope.cancel = function() {
                         window.history.back();
                  }


        var state = {};
        state.table ='state';

        $http.post('api/public/common/GetTableRecords',state).success(function(result) 
        {   
            if(result.data.success=='1')
            {   
              $scope.states_all = result.data.records;
            }
        });


        $scope.addVendor = function()
        {
            $mdDialog.show({
                //controller: 'AddEmployeeDialogController',
                controller: function($scope,params){
                    $scope.params = params;
                    $scope.states_all = params.states_all;
                    $scope.addVendor = function (vendor) 
                    {
            var InserArray = {}; // INSERT RECORD ARRAY

                  InserArray.data = vendor;
                  InserArray.data.company_id = $scope.params.company_id;
                  InserArray.table ='vendors';            

                  // INSERT API CALL
                  $http.post('api/public/common/InsertRecords',InserArray).success(function(Response) 
                  {   
                    if(Response.data.success=='1')
                      {
                        notifyService.notify('success',Response.data.message);
                        
                         $scope.params.updateProduct('vendor_id',$scope.params.product_id_new,Response.data.id,'products','id');

                         $scope.params.vendorList();
                         $scope.params.vendor_id = Response.data.id;
                        
                        $scope.closeDialog();
                      }
                      else
                      {
                        notifyService.notify('error',Response.data.message);
                      }  
                  });
                    } 
                    $scope.closeDialog = function() 
                    {
                     
                        $mdDialog.hide();
                    } 

                },
                templateUrl: 'app/main/settings/dialogs/vendor/addvendor.html',
                parent: angular.element($document.body),
                clickOutsideToClose: true,
                locals: {
                    params:$scope
                }
            });
        }
    }
})();