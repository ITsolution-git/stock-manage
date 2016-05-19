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

        var affiliate_data = {};
        affiliate_data.table ='affiliates';
        affiliate_data.cond ={'company_id':$scope.company_id,'order_id':$scope.order_id}
        $http.post('api/public/affiliate/getAffiliateDetail',affiliate_data).success(function(result) {
            
            if(result.data.success == '1') 
            {
                $scope.allAffiliate =result.data.records;
            } 
            else
            {
                $scope.allVendors=[];
            }
        });

        // Data
        $scope.designSelect = {
            "designOption":
                    [
                        {"option": "Design 1"},
                        {"option": "Design 2"},
                        {"option": "Design 3"}
                    ],
            "design": ""

        };
        $scope.productSelect = {
            "productOption":
                    [
                        {"option": "Product 1"},
                        {"option": "Product 2"},
                        {"option": "Product 3"}
                    ],
            "design": ""

        };
        $scope.affiliateSelect = {
            "affiliateOption":
                    [
                        {"option": "Affiliate 1"},
                        {"option": "Affiliate 2"},
                        {"option": "Affiliate 3"}
                    ],
            "design": ""

        };
        $scope.splitAffiliateSize={
          "s":"",
         "m":"",
         "l":"",
         "xl":"",
        
        };
       
         $scope.splitAffiliateDialog={
          "affiliateTotal":"200",
         "affiliateNotTotal":"800",
         "shopInvoice":"$1,000",
         "affilateInvoice":"$800",
         "additonalCharges":"$100",
         "total":"$200",
         additionalCharges:"",
         notes:"",
         
        
        };
        
        

        // Methods
    
        $scope.closeDialog = closeDialog;
     
        /**
         * Close dialog
         */
        function closeDialog()
        {
            $mdDialog.hide();
        }
    }
})();