(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('CompanyDetailsController', CompanyDetailsController);
            

    /** @ngInject */
    function CompanyDetailsController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {
      $scope.company_id = sessionService.get('company_id');
       $scope.btns = [{
        label: "SCREEN PRINTING",
        state: false
    }, {
        label: "EMBROIDERING",
        state: true
    }, {
        label: "PACKAGING",
        state: false
    }, {
        label: "SHIPPING",
        state: false
    }, {
        label: "ART WORK",
        state: false
    }];

    $scope.toggle = function () {
        this.b.state = !this.b.state;
    };
   
    $scope.getCompanyInfo = function (){
        $("#ajax_loader").show();
        $http.get('api/public/admin/company/getCompanyInfo/'+$scope.company_id).success(function(result) 
        {   
            if(result.data.success=='1')
            {  
                $scope.company_data = result.data.data;
                //console.log($scope.copmany_data.screen_print);
            }
            else
            {
                notifyService.notify('error',result.data.message);
            }
            $("#ajax_loader").hide();
        });
    }

    $scope.UpdateTableField = function(field_name,field_value,table_name,cond_field,cond_value,extra,param)
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
        UpdateArray.date_field = extra;
       // console.log(UpdateArray); return false;
        $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
            if(result.data.success=='1')
            {
                notifyService.notify('success', result.data.message);
                $scope.getCompanyInfo();
            }
            else
            {
                notifyService.notify('error', result.data.message);
            }
        });
    }

    $scope.getCompanyInfo();

    }
    
})();
