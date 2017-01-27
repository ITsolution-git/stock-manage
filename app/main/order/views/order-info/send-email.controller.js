(function ()
{
    'use strict';

    angular
            .module('app.order')
            .controller('openEmailController', openEmailController);
            

    /** @ngInject */
    function openEmailController(client_id,order_id,display_number,paid,balance,approval,$document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService)
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
                    $scope.email =sessionService.get('email');
                    //$scope.subject = approval+': '+display_number+' from '+sessionService.get('company_name');
                    $scope.subject = 'Order #'+display_number+' from '+sessionService.get('company_name');
                } 
                else
                {
                    $scope.allCompany=[];
                }
            });
        }

        var combine_array = {};
        combine_array.table ='invoice';
        combine_array.cond ={order_id:order_id}
        
        $http.post('api/public/common/GetTableRecords',combine_array).success(function(result) {
            
            if(result.data.success == '1') 
            {
                $scope.invoice =result.data.records[0];
            } 
            else
            {
                $scope.invoice=[];
            }
        });

        get_company_data_selected(client_id)


        $scope.sendMail = function (email,mailMessage,subject) {
            


            if(email == '') {
                  var data = {"status": "error", "message": "Email should not be blank"}
                  notifyService.notify(data.status, data.message);
                  return false;
            } 

            if(subject == '') {
                  var data = {"status": "error", "message": "Subject should not be blank"}
                  notifyService.notify(data.status, data.message);
                  return false;
            } 

            var combine_array = {};

            combine_array.email = email;
            combine_array.order_id = order_id;
            combine_array.company_id = sessionService.get('company_id');
            combine_array.from_email = sessionService.get('email');
            combine_array.name = sessionService.get('name');
            combine_array.mailMessage = mailMessage;
            combine_array.invoice_id = $scope.invoice.id;
            combine_array.paid = paid;
            combine_array.balance = balance;
            combine_array.subject = subject;

            $("#ajax_loader").show();
             
            $http.post('api/public/order/sendEmail',combine_array).success(function(result) 
            {
                if(result.data.success == '1') 
                {
                    var data = {"status": "success", "message": result.data.message}
                    notifyService.notify(data.status, data.message);
                }
                else
                {
                    var data = {"status": "error", "message": "Please print the pdf before send an email."}
                    notifyService.notify(data.status, data.message);
                }
                $("#ajax_loader").hide();
                $mdDialog.hide();
            });
        };

        $scope.cancel = function () {
            $mdDialog.hide();
        };
    }
})();