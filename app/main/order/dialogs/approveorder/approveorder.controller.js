(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('approveOrderDiallogController', approveOrderDiallogController);

    /** @ngInject */
    function approveOrderDiallogController(order_number,$mdDialog,$document, $window, $timeout,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {

        
        var vm = this;
        vm.title = 'Order Approved';
        $scope.cancel = function () {
            $mdDialog.hide();
        };
        /**
         * Close dialog
         */
        function closeDialog()
        {
            $mdDialog.hide();
        }

         $scope.save = function (orderData) {



           if(orderData.sns == true) {

                if(order_number != '') {
                     notifyService.notify('error','You have already posted order to S&S');
                     return false;
                }

                var combine_array_id = {};
                combine_array_id.id = $stateParams.id;
                combine_array_id.company_id = sessionService.get('company_id');
                combine_array_id.company_name = sessionService.get('company_name');
                
                
              $http.post('api/public/order/snsOrder',combine_array_id).success(function(result) 
                {

                    if(result.data.success=='1')
                    {
                        notifyService.notify('success',result.data.message);
                    }
                    else
                    {
                        notifyService.notify('error',result.data.message);
                         $mdDialog.hide();
                         return false;
                    }

                    $mdDialog.hide();
                    $state.go($state.current, $stateParams, {reload: true, inherit: false});
                    return false;
                    
                });

            }  else {

                 $mdDialog.hide();
                return false;
            }

        };
    }
})();