(function () {
    'use strict';

    angular
            .module('app.customProduct')
            .controller('customProductController', customProductController);

    /** @ngInject */
    function customProductController(customProductData, $q, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$scope,$http,notifyService,sessionService,$state) {
        var vm = this;
        
        vm.searchOrder;
        vm.rangeFrom;
        vm.rangeTo;
        $scope.company_id = sessionService.get('company_id');
        

    
        $scope.init = {
          'count': 20,
          'page': 1,
          'sortBy': 'p.name',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };


        $scope.filterBy = {
          'vendor_id':''
        };

        $scope.filterBy.vendor_id = 0;
       

       $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
        };

    
        $scope.getReload = function () {
          $state.go($state.current,'', {reload: true, inherit: false});
        }

        $scope.getResource = function (params, paramsObj, search) {
            $scope.params = params;
            $scope.paramsObj = paramsObj;
 
            var orderData = {};
            $("#ajax_loader").show();

            orderData.cond ={company_id :sessionService.get('company_id'),params:$scope.params};
            //   orderData.cond ={company_id :0,params:$scope.params};

              return $http.post('api/public/product/getCustomProduct',orderData).success(function(response) {
                $("#ajax_loader").hide();
                var header = response.header;
                $scope.success = response.success;
                return {
                  'rows': response.rows,
                  'header': header,
                  'pagination': response.pagination,
                  'sortBy': response.sortBy,
                  'sortOrder': response.sortOrder
                }
              });
        }
        //Datatable
        vm.dtOptions = {
            dom: '<"top">rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };
        // Methods
        vm.openCustomProductDialog = openCustomProductDialog;
        vm.dtInstanceCB = dtInstanceCB;
        vm.searchTable = searchTable;

        // -> Filter menu
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
        }
        function searchTable() {
            var query = vm.searchQuery;
            vm.tableInstance.search(query).draw();
        }


        function openCustomProductDialog(ev, product_id)
        {

            $mdDialog                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                .show({
                controller: 'CustomProductDialogController',
                controllerAs: 'vm',
                templateUrl: 'app/main/customProduct/dialogs/customProduct/customProduct-dialog.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: true,
                locals: {
                    product_id: product_id,
                    event: ev
                },
                onRemoving : $scope.getReload
            });
        }

        
        $scope.submitForm = function () {
 
             var fileName = $("#file").val();
            

             if(fileName == ''){
                 var data = {"status": "error", "message": "Please upload CSV file."}
                              notifyService.notify(data.status, data.message);
                              return false;
             }
                if(fileName.lastIndexOf("csv")!=fileName.length-3) {

                     var data = {"status": "error", "message": "File must be in CSV format"}
                              notifyService.notify(data.status, data.message);
                              return false;
                } else {
                    
                    document.getElementById('uploadcsv').submit();
                }
                   

          
        }

    

    }
})();