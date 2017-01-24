(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('PriceGridController', PriceGridController);
            

    /** @ngInject */
    function PriceGridController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,AllConstant,notifyService)
    {

        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='CA' || $scope.role_slug=='AM' || $scope.role_slug=='SM' || $scope.role_slug=='FM')
        {
            $scope.allow_access = 1; // OTHER ROLES CAN NOT ALLOW TO EDIT, CAN VIEW ONLY
        }
        else
        {
            $scope.allow_access = 0;  // THESE ROLES CAN ALLOW TO EDIT
        }


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

         $scope.submitPriceGridDownload = function () {
 
             document.getElementById('downloadPricecsv').submit();
        }

         $scope.submitPriceForm = function () {
 
             var fileName = $("#file").val();
            

             if(fileName == ''){
                 var data = {"status": "error", "message": "Please upload xls/xlsx file."}
                              notifyService.notify(data.status, data.message);
                              return false;
             }
             
            document.getElementById('uploadPricecsv').submit();
               
        }


         $scope.print_excel = function(id)
        {
            var target;
            var form = document.createElement("form");
            form.action = 'api/public/admin/downloadPriceGridExcel';
            form.method = 'post';
            form.target = target || "_blank";
            form.style.display = 'none';

            var invoice_id = document.createElement('input');
            invoice_id.name = 'price_id';
            invoice_id.setAttribute('value', id);
            form.appendChild(invoice_id);

            var type = document.createElement('input');
            type.name = 'type';
            type.setAttribute('value', 'xlsx');
            form.appendChild(type);

            var input_pdf = document.createElement('input');
            input_pdf.name = 'pdf_token';
            input_pdf.setAttribute('value', 'pdf_token');
            form.appendChild(input_pdf);


            document.body.appendChild(form);
            form.submit();
        }

    }
    
})();
