(function ()
{
    'use strict';

    angular
        .module('app.customProduct')
        .controller('CustomProductDialogController', CustomProductDialogController);
/** @ngInject */
    function CustomProductDialogController(product_id,$scope, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService)
    {

       $scope.example6model = [{id: 1}, {id: 3}]; $scope.example6data = [ {id: 1, label: "David"}, {id: 2, label: "Jhon"}, {id: 3, label: "Danny"}]; $scope.example6settings = {};

        /*$scope.colorsettings = {displayProp: 'name', idProp: 'id',enableSearch: true, scrollableHeight: '400px',showCheckAll:false,showUncheckAll:false,scrollable: true};
        $scope.colorcustomTexts = {buttonDefaultText: 'Select Colors'};

        if(product_id == 0) {

            var product_data = {};
            var productData = {};
            productData.company_id =sessionService.get('company_id');
           
            product_data.data = productData;
           
          //  product_data.data.created_date = $filter('date')(new Date(), 'yyyy-MM-dd');
            product_data.data.vendor_id =0;
            product_data.data.name ='Default Product';
           

            product_data.table ='products'

            $http.post('api/public/common/InsertRecords',product_data).success(function(result) {
                console.log(result.data.id);
                var id = result.data.id;
                
                getProductDetailByIdAll(id);
                 $scope.product_id_new  = id;
                 
                
            });
                       

           
                 
            } else {
                 getProductDetailByIdAll(product_id);
                // getProductDetailColorSize(value);

                 $scope.product_id_new  = product_id;
                // console.log($scope.product_data);return false;

        }


        get_color_data();
 
           $scope.colorEvents = {
                        onItemSelect: function(item) {
                            
                                var color_data = {};
                                color_data.color_id = item.id;
                                color_data.product_id = $scope.product_id_new;

                                $http.post('api/public/order/saveColorSize',color_data).success(function(Listdata) {
                                    getProductDetailByIdAll($scope.product_id_new);
                                });
                        },
                        onItemDeselect: function(item) {
                          
                               var color_data = {};
                                color_data.color_id = item.id;
                                color_data.product_id = $scope.product_id_new;

                                $http.post('api/public/order/deleteColorSize',color_data).success(function(Listdata) {
                                    getProductDetailByIdAll($scope.product_id_new);
                                });
                        } 
                        
             };



         function getProductDetailByIdAll(id)
          {
              product_arr = {'id':id};
              $http.post('api/public/order/productDetail',product_arr).success(function(result) {
                      $scope.allProductColorSize =result.data;
                   
                       if($scope.allProductColorSize.colorData.length == '0'){
                        $scope.allProductColorSize.colorData = [];
                  }
               });
              }


    function getProductDetailColorSize(product_id)
    {
        $("#ajax_loader").show();
        $http.get('api/public/order/getProductDetailColorSize/'+product_id).success(function(result, status, headers, config) 
        {
            if(result.data.success == '1') 
            {
                $scope.allProductColorSize =result.data.records;
            } 
            else
            {
                $scope.allProductColorSize=[];
                $scope.allProductColorSize.productColorSizeData=[];
                $scope.allProductColorSize.ColorData=[];
            }
            $("#ajax_loader").hide();
        });
    }



    function get_color_data()
    {
        var colorData = {};
        colorData.table ='color'
        colorData.cond ={status:1,is_delete:1}
        
        $http.post('api/public/common/GetTableRecords',colorData).success(function(result) {
            
            if(result.data.success == '1') 
            {
                $scope.allColor =result.data.records;
            } 
            else
            {
                $scope.allColor=[];
            }
        });
     }*/

        $scope.cancel = function () {
            $mdDialog.hide();
        };
    }
})();