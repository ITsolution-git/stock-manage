(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('AddSplitAffiliateController', AddSplitAffiliateController);

    /** @ngInject */
    function AddSplitAffiliateController($window, $timeout,$filter,$scope,$stateParams, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService,$log,AllConstant,Order)
    {
        $scope.title = 'Split Affiliate';

        $scope.company_id = sessionService.get('company_id');
        $scope.order_id = Order.order_id;
        $scope.design = 0;
        $scope.affiliate = 0;
        $scope.sizes = [];
        $scope.total_affiliate = 0;
        $scope.additional_charges = 0;
        $scope.total_not_assign = 0;
        $scope.notes = '';
        $scope.shop_invoice = Order.order.grand_total;
        $scope.affiliate_invoice = 0;
        $scope.total = 0;
        $scope.design_product_id = 0;

        $scope.finalCalcualtion = function()
        {
            if($scope.additional_charges < 0 || $scope.additional_charges == ''){
                $scope.additional_charges = 0;
            }
            
            var total = parseFloat($scope.shop_invoice) - parseFloat($scope.affiliate_invoice) + parseFloat($scope.additional_charges);
            $scope.total = total.toFixed(2);
        }

        $scope.finalCalcualtion();

        var affiliate_data = {};
        affiliate_data.table ='affiliates';
        affiliate_data.cond ={'company_id':$scope.company_id,'order_id':$scope.order_id}
        $http.post('api/public/affiliate/getAffiliateDetail',affiliate_data).success(function(result) {
            
            if(result.data.success == '1') 
            {
                $scope.allAffiliate =result.data.records['affiliate_data'];
                $scope.allDesign =result.data.records['design_detail'];
            } 
            else
            {
                $scope.allVendors=[];
            }
        });

        $scope.getDesignProduct = function(design_id)
        {
            var combine_array_id = {};
            combine_array_id.id = design_id;
            
            $http.post('api/public/affiliate/getAffiliateDesignProduct',combine_array_id).success(function(response, status, headers, config) {
                if(response.data.success == '1') {
                    $scope.productData = response.data.records;
                }
                else{
                    $scope.productData = [];                    
                }
            });
        }

        $scope.calculateAffiliate = function()
        {
            var affiliate_data = {};
            affiliate_data ={'design_id':$scope.design,'affiliate_id':$scope.affiliate,'sizeData':$scope.sizes}
            $http.post('api/public/affiliate/affiliateCalculation',affiliate_data).success(function(result) {
                if(result.success == '1')
                {
                    $scope.affiliate_invoice = result.affiliate_invoice;
                    $scope.finalCalcualtion();
                }
            });
        }

        $scope.getProductSize = function(design_product_id)
        {
            var size_data = {};
            size_data ={design_product_id:design_product_id}
            $scope.design_product_id = design_product_id;

            // GET CLIENT TABLE CALL
            $http.post('api/public/product/getProductSize',size_data).success(function(result)
            {   
                if(result.data.success=='1')
                {   
                    $scope.sizes = result.data.records;
                    $scope.calculateAffiliate();
                }
                else
                {
                    $scope.sizes = [];
                }
            });
        }

        $scope.save = function()
        {
            if($scope.design == '0')
            {
                var data = {"status": "error", "message": "Please select design"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if($scope.affiliate == '0')
            {
                var data = {"status": "error", "message": "Please select affiliate"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            if($scope.design_product_id == '0')
            {
                var data = {"status": "error", "message": "Please select product"}
                notifyService.notify(data.status, data.message);
                return false;
            }
            $scope.execute = 1;
/*            angular.forEach($scope.sizes, function(size) {
                if(size.affiliate_qnty > 0)
                {
                    $scope.execute = 1;
                }
            });*/
            
            if($scope.execute == 0)
            {
                var data = {"status": "error", "message": "Please enter quantity to create order"}
                notifyService.notify(data.status, data.message);
            }
            else
            {
                var affiliate_data = {'order_id':$scope.order_id,'design_id':$scope.design,'affiliate_id':$scope.affiliate,'sizes':$scope.sizes,'design_product_id':$scope.design_product_id,
                                    'total_affiliate':$scope.total_affiliate,'additional_charges':$scope.additional_charges,'total_not_assign':$scope.total_not_assign,
                                    'notes':$scope.notes,'shop_invoice':$scope.shop_invoice,'affiliate_invoice':$scope.affiliate_invoice,'total':$scope.total};

                //$("#ajax_loader").show();
                
                $http.post('api/public/affiliate/addAffiliate',affiliate_data).success(function(result) {
                    $("#ajax_loader").hide();
                    if(result.data.success == '1') 
                    {
                        $mdDialog.hide();
                        $state.go($state.current, $stateParams, {reload: true, inherit: false});
                    } 
                    else
                    {
                        $scope.allVendors=[];
                    }
                });
            }
        }
       
        $scope.closeDialog = closeDialog;

        function closeDialog()
        {
            $mdDialog.hide();
        }
    }
})();