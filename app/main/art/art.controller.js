(function () {
    'use strict';

    angular
            .module('app.art')
            .controller('ArtController', ArtController)
            .controller('ArtScreenController', ArtScreenController);
    /** @ngInject */
    function ArtController($document, $window, $timeout,$mdSidenav, $mdDialog,DTOptionsBuilder, DTColumnBuilder, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter){
        var vm = this;
        vm.searchQuery = "";
         vm.resetFilter = resetFilter;
         vm.companyfilter = false;
         vm.searchOrder;

        // Data
        $scope.company_id = sessionService.get('company_id');
         
        // CHECK THIS MODULE ALLOW OR NOT FOR ROLES
        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='SU')
        {
            $scope.allow_access = 0; // OTHER ROLES CAN NOT ALLOW TO EDIT, CAN VIEW ONLY
        }
        else
        {
            $scope.allow_access = 1;  // THESE ROLES CAN ALLOW TO EDIT
        }



        /* TESTY PAGINATION */     
        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'ord.display_number',
          'sortOrder': 'dsc'
        };
        vm.companyCheckModal = [];
        // GET CLIENT TABLE CALL
        var company_list_data = {};
        var condition_obj = {};
        condition_obj['company_id'] =  sessionService.get('company_id');
        company_list_data.cond = angular.copy(condition_obj);
        
        $http.post('api/public/client/getClientFilterData',company_list_data).success(function(Listdata) {
            vm.companyCheckData = Listdata.data.records;
        });




        $scope.reloadCallback = function () { };

        $scope.filterBy = {
          'search': '',
          'name': '',
          'client': '',
          'function': 'art_list',
          'params_first':'orders'
        };
        $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
            //getResource();
        };
        
      $scope.getResource = function (params, paramsObj, search)
        {   
            $scope.params = params;
            $scope.params.company_id = $scope.company_id;
            $scope.paramsObj = paramsObj;

            var company_data = {};
            company_data.cond ={params:$scope.params};

            $("#ajax_loader").show();     
           return $http.post('api/public/common/getTestyRecords',company_data).success(function(result) 
            {
                $("#ajax_loader").hide();
                $scope.success  = result.success;
                if(result.success=='1')
                {
                    return {
                      'rows': result.rows,
                      'header': result.header,
                      'pagination': result.pagination,
                      'sortBy': result.sortBy,
                      'sortOrder': result.sortOrder
                    }
                }
                else
                {
                    notifyService.notify('error',result.message);
                }
            });
        }

      
        $scope.buttonSetting = {
            buttonDefaultText:'Select Options to Filter',
            scrollableHeight: '500px',
            scrollable: true
        };


       


        function resetFilter() {

/*            for (var i = 0; i < this.salesCheckData.length; i++) {
                this.salesCheckData[i].label = false;
            }
            for (var i = 0; i < this.companyCheckData.length; i++) {
                this.companyCheckData[i].label = false;
            }

*/            
            vm.companyChecksettings = 
                {   externalIdProp: myCustomPropertyForTheObject(),
                    
                   
                }
            this.searchOrder = true;

           
            
            function myCustomPropertyForTheObject(){
                vm.companyCheckModal = [];
            }

            for (var i = 0; i < vm.companyCheckModal.length; i++) {
                vm.companyCheckModal[i].id = null;

            }   
            vm.shipDate = vm.createDate = vm.rangeFrom = vm.rangeTo = null;
            this.searchOrder = true;

            $scope.filterOrders();
        }

        $scope.filterOrders = function(){
            
            var flag = true;
            $scope.filterBy.client = '';
            $scope.clientArray = [];
            $scope.filterBy.temp = '';

            angular.forEach(vm.companyCheckModal, function(company){
                    $scope.clientArray.push(company.id);
            })
            if($scope.clientArray.length > 0)
            {
                flag = false;
                $scope.filterBy.client = angular.copy($scope.clientArray);
            }

            if(flag == true)
            {
                $scope.filterBy.temp = angular.copy(1);
            }
        }


        //vm.screenset = ArtData.artData.data1;
        //Datatable
        vm.dtOptions = {
            dom: '<"top">rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };
        // Methods
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
        // -> Filter menu
        vm.toggle = true;
        vm.openRightMenu = function () {
            $mdSidenav('right').toggle();
        };
        vm.openRightMenu1 = function () {
            $mdSidenav('left').toggle();
        };


        var misc_list_data = {};
        var condition_obj = {};
        condition_obj['company_id'] =  sessionService.get('company_id');
        misc_list_data.cond = angular.copy(condition_obj);

        $http.post('api/public/common/getAllMiscDataWithoutBlank',misc_list_data).success(function(result, status, headers, config) {
                $scope.miscData = result.data.records;
        });

        $scope.updateOrderStatus = function(name,value,id)
        {
            var order_main_data = {};

            order_main_data.table ='orders';

            $scope.name_filed = name;
            var obj = {};
            obj[$scope.name_filed] =  value;
            order_main_data.data = angular.copy(obj);

            var condition_obj = {};
            condition_obj['id'] =  id;
            order_main_data.cond = angular.copy(condition_obj);

            $http.post('api/public/common/UpdateTableRecords',order_main_data).success(function(result) {

                var data = {"status": "success", "message": "Data Updated Successfully."}
                notifyService.notify(data.status, data.message);
            });
        }


    }
    function ArtScreenController($document, $window, $timeout,DTOptionsBuilder, DTColumnBuilder,$mdSidenav, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter){

        var vm = this;
        vm.searchQuery = "";
         vm.resetFilter = resetFilter;
         vm.companyfilter = false;
         vm.searchOrder;

         
        // Data
        $scope.company_id = sessionService.get('company_id');
        $scope.role_slug = sessionService.get('role_slug');
        /* TESTY PAGINATION */     
        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'po.id',
          'sortOrder': 'dsc'
        };
        vm.companyCheckModal_screen = [];
        vm.widthCheckModal = [];
        // GET CLIENT TABLE CALL
        var company_list_data = {};
        var condition_obj = {};
        condition_obj['company_id'] =  sessionService.get('company_id');
        company_list_data.cond = angular.copy(condition_obj);
        
        $http.post('api/public/client/getClientFilterData',company_list_data).success(function(Listdata) {
            $scope.ListClients = Listdata.data.records;
        });

        $http.get('api/public/art/getScreenSizes/'+sessionService.get('company_id')).success(function(Listdata) {
            $scope.screensizes = Listdata.data.records;
        });


        $scope.reloadCallback = function () { };

        $scope.filterBy = {
          'search': '',
          'name': '',
          'client':'',
          'width':'',
          'function': 'art_list_screen'
        };
        $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
            //getResource();
        };

        var misc_list_data = {};
        var condition_obj = {};
        condition_obj['company_id'] =  sessionService.get('company_id');
        misc_list_data.cond = angular.copy(condition_obj);

        $http.post('api/public/common/getAllMiscDataWithoutBlank',misc_list_data).success(function(result, status, headers, config) {
                $scope.miscData = result.data.records;
        });

        $scope.updateOrderStatus = function(name,value,id)
        {
            var order_main_data = {};

            order_main_data.table ='orders';

            $scope.name_filed = name;
            var obj = {};
            obj[$scope.name_filed] =  value;
            order_main_data.data = angular.copy(obj);

            var condition_obj = {};
            condition_obj['id'] =  id;
            order_main_data.cond = angular.copy(condition_obj);

            $http.post('api/public/common/UpdateTableRecords',order_main_data).success(function(result) {

                var data = {"status": "success", "message": "Data Updated Successfully."}
                notifyService.notify(data.status, data.message);
            });
        }
        
      $scope.getResource = function (params, paramsObj, search)
        {   
            $scope.params = params;
            $scope.params.company_id = $scope.company_id;
            $scope.paramsObj = paramsObj;

            var company_data = {};
            company_data.cond ={params:$scope.params};

            $("#ajax_loader").show();     
           return $http.post('api/public/common/getTestyRecords',company_data).success(function(result) 
            {
                $("#ajax_loader").hide();
                $scope.success  = result.success;
                if(result.success=='1')
                {
                    return {
                      'rows': result.rows,
                      'header': result.header,
                      'pagination': result.pagination,
                      'sortBy': result.sortBy,
                      'sortOrder': result.sortOrder
                    }
                }
                else
                {
                    notifyService.notify('error',result.message);
                }
            });
        }
        
      
        $scope.buttonSetting = {
            buttonDefaultText:'Select Options to Filter',
            scrollableHeight: '500px',
            scrollable: true
        };


        function resetFilter() {

            vm.companyChecksettings_screen = 
                {   
                    externalIdProp: myCustomPropertyForTheObject_screen(),
                }
            vm.widthChecksettings = 
                {   
                    externalIdProp: myCustomPropertyForWidth(),
                }
            this.searchOrder = true;
            
            function myCustomPropertyForTheObject_screen(){
                vm.companyCheckModal_screen = [];
            }
             function myCustomPropertyForWidth(){
                vm.widthCheckModal = [];
            }


            for (var i = 0; i < vm.companyCheckModal_screen.length; i++) {
                vm.companyCheckModal_screen[i].id = null;

            }   
            for (var i = 0; i < vm.widthCheckModal.length; i++) {
                vm.widthCheckModal[i].id = null;

            }  
            
            this.searchOrder = true;

            $scope.filterOrders();
        }

        $scope.filterOrders = function(){
            
            var flag = true;
            $scope.filterBy.client = '';
            $scope.filterBy.width = '';
            $scope.clientArray = [];
            $scope.widthArray = [];
            $scope.filterBy.temp = '';

            angular.forEach(vm.companyCheckModal_screen, function(company){
                    $scope.clientArray.push(company.id);
            })
            if($scope.clientArray.length > 0)
            {
                flag = false;
                $scope.filterBy.client = angular.copy($scope.clientArray);
            }
            angular.forEach(vm.widthCheckModal, function(width){
                    $scope.widthArray.push(width.id);
            })
            if($scope.widthArray.length > 0)
            {
                flag = false;
                $scope.filterBy.width = angular.copy($scope.widthArray);
            }

            if(flag == true)
            {
                $scope.filterBy.temp = angular.copy(1);
            }
        }

        //vm.screenset = ArtData.artData.data1;
        //Datatable
        vm.dtOptions = {
            dom: '<"top">rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };
        // Methods
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
        // -> Filter menu
        vm.toggle = true;
        vm.openRightMenu = function () {
            $mdSidenav('right').toggle();
        };
        vm.openRightMenu1 = function () {
            $mdSidenav('left').toggle();
            $('body').addClass('vinit');
            
        };
    }
})();
