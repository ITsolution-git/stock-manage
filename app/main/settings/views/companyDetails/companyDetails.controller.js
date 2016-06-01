(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('CompanyDetailsController', CompanyDetailsController);
            

    /** @ngInject */
    function CompanyDetailsController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state)
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
        $http.get('api/public/admin/company/getCompanyInfo'+$scope.company_id).success(function(result) 
        {   
            if(result.data.success=='1')
            {  
                var params = {};
                params = { contact_arr: result.data.records[0],states_all:$scope.states_all,AddrTypeData:$scope.AddrTypeData};                     
                open_popup(ev,params,'CompanyInfo',popup_page); // OPEN POPUP FOR CONTACT
            }
        });
    }


    }
    
})();
