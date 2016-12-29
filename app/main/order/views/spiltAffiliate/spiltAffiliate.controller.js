(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('SpiltAffiliateController', SpiltAffiliateController);

    /** @ngInject */
    function SpiltAffiliateController($document, $window, $timeout, $mdDialog, $stateParams, $scope, $http, sessionService, AllConstant)
    {
        $scope.NoImage = AllConstant.NoImage;
         $scope.role_slug = sessionService.get('role_slug');
        $scope.user_id = sessionService.get('user_id');
        $scope.allowSA = 0;


         // change display number to order Id for fetching the order data
          var order_data = {};
           order_data.cond ={company_id :sessionService.get('company_id'),display_number:$stateParams.id};
           order_data.table ='orders';
          
          $http.post('api/public/common/GetTableRecords',order_data).success(function(result) {
            
              
              if(result.data.success == '1') 
              {
                  $scope.vendorRecord =result.data.records;
                  $scope.order_id = result.data.records[0].id;
                  $scope.display_number = result.data.records[0].display_number;

                  if($scope.role_slug == 'SM' && $scope.user_id == result.data.records[0].login_id) {

                    $scope.allowSA = 1;
                  } 



                  if($scope.role_slug=='SU' || $scope.role_slug=='AT')
                    {
                        $scope.allow_access = 0; // OTHER ROLES CAN NOT ALLOW TO EDIT, CAN VIEW ONLY
                    }
                    else if($scope.role_slug =='SM' && $scope.allowSA == 1)
                    {
                        $scope.allow_access = 1;  // THESE ROLES CAN ALLOW TO EDIT

                    } else if($scope.role_slug =='SM' && $scope.allowSA == 0)
                    {
                        $scope.allow_access = 0;  // THESE ROLES CAN ALLOW TO EDIT

                    } else {

                         $scope.allow_access = 1; // THESE ROLES CAN ALLOW TO EDIT
                    }



                    $scope.orderDetail();
                    $scope.affiliate();
                   

              } 
              else
              {
                   $state.go('app.order');
              }
          });

          $scope.orderDetail = function(){

             $scope.company_id = sessionService.get('company_id');

                var combine_array_id = {};
                combine_array_id.id = $scope.order_id;
                combine_array_id.company_id = $scope.company_id;

                $http.post('api/public/order/orderDetail',combine_array_id).success(function(result, status, headers, config) {
                    if(result.data.success == '1') {
                       $scope.order = result.data.records[0];
                    }
                });

          }


           $scope.affiliate = function(){

            var affiliate_data = {};
            affiliate_data ={'company_id':$scope.company_id,'id':$scope.order_id}
            $http.post('api/public/affiliate/getAffiliateData',affiliate_data).success(function(result) {
                
                if(result.data.success == '1') 
                {
                    $scope.spiltOrderInformation = result.data.records;
                    $scope.spiltOrderList = result.data.affiliateList;
                }
            });

          }

       

       

        var vm = this;
         $scope.openaddDesignDialog = openaddDesignDialog;
          $scope.openaddSplitAffiliateDialog = openaddSplitAffiliateDialog;
       
        
//        Datatable Options
        $scope.dtOptions = {
            dom: '<"top">rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };
        var originatorEv;
        $scope.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
        $scope.dtInstanceCB = dtInstanceCB;
        //methods
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            $scope.tableInstance = datatableObj;
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
                    Orders: $scope.orders,
                    event: ev
                }
            });
        }
        function openaddSplitAffiliateDialog(ev, order)
        {
            $mdDialog.show({
                controller: 'AddSplitAffiliateController',
                controllerAs: 'vm',
                templateUrl: 'app/main/order/dialogs/addSplitAffiliate/addSplitAffiliate.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    Order: $scope,
                    Orders: $scope.orders,
                    event: ev
                }
            });
        }
    }
})();
