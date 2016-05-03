(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('openEmailController', openEmailController);
            

    /** @ngInject */
    function openEmailController(client_id,$document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state)
    {

           function get_company_data_selected(id)
         {
            var companyData = {};
            companyData.table ='client'
            companyData.cond ={status:1,is_delete:1,company_id:sessionService.get('company_id'),client_id:id}
            
            $http.post('api/public/common/GetTableRecords',companyData).success(function(result) {
                
                if(result.data.success == '1') 
                {
                    $scope.allCompany =result.data.records;
                } 
                else
                {
                    $scope.allCompany=[];
                }
            });
        }

        get_company_data_selected(client_id)
    
    }
    
})();
