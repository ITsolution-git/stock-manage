(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('AddProductController', AddProductController);

    /** @ngInject */
    function AddProductController($scope, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService)
    {
        var vm = this;

         var companyData = {};
            companyData.cond ={company_id :sessionService.get('company_id'),is_delete :'1',status :'1',vendor_id :0};
            companyData.table ='products';

                $http.post('api/public/common/GetTableRecords',companyData).success(function(result) {
        
                        if(result.data.success == '1') 
                        {
                            $scope.allProduct =result.data.records;
                        } 
                        else
                        {
                            $scope.allProduct=[];
                        }
                });


                $scope.changeProduct = function(id,color_id){

                     var product_arr = {}
                     product_arr = {'id':id};
                       $http.post('api/public/product/getProductDetailColorSize',product_arr).success(function(result) {
                      

                      $scope.productId =result.data.product_id;
                      $scope.productColorSize =result.data.productColorSizeData;

                       if(color_id != 0) {
                        $scope.sizeAll = result.data.productColorSizeData[color_id];

                      }

                   
                    });
                }


        // Data
        vm.addProduct={
          "productName":"",
         "s":"",
         "m":"",
         "l":"",
         "xl":"",
         "notes":""
        };
        
        

        // Methods
    
        vm.closeDialog = closeDialog;
     
        /**
         * Close dialog
         */
        function closeDialog()
        {
            $mdDialog.hide();
        }
    }
})();