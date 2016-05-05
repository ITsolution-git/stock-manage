(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('openEmailController', openEmailController);
            

    /** @ngInject */
    function openEmailController(client_id,order_id,$document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService)
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


         $scope.sendMail = function (email) {

             var data = {"status": "error", "message": "In progress"}
                          notifyService.notify(data.status, data.message);
                          return false;

        
              if(email == '') {

                  var data = {"status": "error", "message": "Email should not be blank"}
                          notifyService.notify(data.status, data.message);
                          return false;
                } 

            
                  var combine_array = {};
                 
                  combine_array.email = email;
                  combine_array.order_id = order_id;
                  combine_array.company_id = sessionService.get('company_id');
                 
                $http.post('api/public/order/sendEmail',combine_array).success(function(result) 
                {
                    if(result.data.success == '1') 
                    {
                        
                    } else {

                        var data = {"status": "error", "message": "Please print the pdf before send an email."}
                          notifyService.notify(data.status, data.message);
                          
                    }
                    $mdDialog.hide();
                    
                });

        };

        $scope.cancel = function () {
            $mdDialog.hide();
        };
    
    }
    
})();
