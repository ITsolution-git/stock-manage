(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('PriceGridController', PriceGridController);
            

    /** @ngInject */
    function PriceGridController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,AllConstant,notifyService)
    {
        var originatorEv;
        var vm = this ;
        $scope.company_id = sessionService.get('company_id');
     

        $scope.priceList = function(){

             var company_id = sessionService.get('company_id');
                var price_list_data = {};
                var condition_obj = {};
                condition_obj['company_id'] =  company_id;
                price_list_data.cond = angular.copy(condition_obj);

                $http.post('api/public/admin/price',price_list_data).success(function(result, status, headers, config) {
                    $scope.price = result.data.records;                     
                });
        }

        $scope.priceList();

        $scope.updateTableField = function(field_name,field_value,table_name,cond_field,cond_value)
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
            var permission = confirm(AllConstant.deleteMessage);
            if (permission == true)
                {

                $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
                    if(result.data.success=='1')
                    {
                      notifyService.notify('success','Record Deleted Successfully.');
                        $scope.priceList();
                    }
                    else
                    {
                        notifyService.notify('error', result.data.message);
                    }
                });
             }
        } 
        

        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };

    }
    
})();
