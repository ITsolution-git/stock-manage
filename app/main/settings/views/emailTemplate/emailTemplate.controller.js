(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('EmailTemplateController', EmailTemplateController);
            

    /** @ngInject */
    function EmailTemplateController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,AllConstant,notifyService)
    {

        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='CA' || $scope.role_slug=='AM')
        {
            $scope.allow_access = 1; // OTHER ROLES CAN NOT ALLOW TO EDIT, CAN VIEW ONLY
        }
        else
        {
            $scope.allow_access = 0;  // THESE ROLES CAN ALLOW TO EDIT
        }

        $scope.company_id = sessionService.get('company_id');
     

        $scope.templateList = function(){

               var allData = {};
                allData.table ='email_template';
                allData.sort ='id';
                allData.sortcond ='desc';
                allData.cond ={company_id:sessionService.get('company_id')}

                $http.post('api/public/common/GetTableRecords',allData).success(function(result)
                {   
                    if(result.data.success=='1')
                    {   
                        $scope.allTemplate = result.data.records;
                        
                    } else {
                        $scope.allTemplate = {};
                       
                    }     
                        
                });
        }

        $scope.templateList();

        

    }
    
})();
