(function ()
{
    'use strict';

    angular
            .module('app.invoices')
            .controller('singleInvoiceController', singleInvoiceController);
    /** @ngInject */
    function singleInvoiceController($q,$mdDialog,$document,$mdSidenav,DTOptionsBuilder,DTColumnBuilder,$resource,$scope,$http,sessionService,notifyService,$stateParams) {
        var vm = this;
        vm.linktopay = linktopay;

        var invoice_data = {invoice_id:$stateParams.id,company_id :sessionService.get('company_id')};
        
        $http.get('api/public/invoice/getInvoiceDetail/'+$stateParams.id+'/'+sessionService.get('company_id')+'/0').success(function(result) {
            $scope.allData = result.data.allData;
            $scope.brand_coordinator = sessionService.get('role_title');
        });

        $http.get('api/public/invoice/getInvoiceHistory/'+$stateParams.id+'/'+sessionService.get('company_id')+'/0').success(function(result) {
            $scope.siData = result.data.allData;
        });

        // JS FOR MODAL LINK TO PAY
        function linktopay(ev, settings) {
            $mdDialog.show({
                controller: 'linktoPayController',
                controllerAs: 'vm',
                templateUrl: 'app/main/invoices/dialogs/linktopay/linktopay-dialog.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    event: ev
                }
            });
        }

        $scope.print_pdf = function()
        {
            var target;
            var form = document.createElement("form");
            form.action = 'api/public/invoice/createInvoicePdf';
            form.method = 'post';
            form.target = target || "_blank";
            form.style.display = 'none';

            var invoice_id = document.createElement('input');
            invoice_id.name = 'invoice_id';
            invoice_id.setAttribute('value', $stateParams.id);
            form.appendChild(invoice_id);

            var company_id = document.createElement('input');
            company_id.name = 'company_id';
            company_id.setAttribute('value', sessionService.get('company_id'));
            form.appendChild(company_id);

            document.body.appendChild(form);
            form.submit();
        }

        $scope.pay_cash = function(amount)
        {
            if(amount == undefined) {
              var data = {"status": "error", "message": "Amount should not be blank"}
                  notifyService.notify(data.status, data.message);
                  return false;
            }
            $mdDialog.show();

            var invoice_id = document.createElement('input');
            invoice_id.name = 'invoice_id';
            invoice_id.setAttribute('value', $stateParams.id);
            

            var company_id = document.createElement('input');
            company_id.name = 'company_id';
            company_id.setAttribute('value', sessionService.get('company_id'));

            var combine_array_id = {};
            combine_array_id.amount = amount.cashAmount;
            combine_array_id.invoice_id = invoice_id.value;

            $http.post('api/public/order/paymentInvoiceCash',combine_array_id).success(function(result) 
            {
                $mdDialog.hide();
                if(result != '0')
                {
                    $scope.allData.order_data[0].total_payments = result.data.amt.total_payments;
                    $scope.allData.order_data[0].balance_due = result.data.amt.balance_due;
                    notifyService.notify('success',"Payment added Successfully");
                }
                else
                {
                    notifyService.notify('error',"Payment not added");
                }
                return false;
                
            });

        }
    }
})();
