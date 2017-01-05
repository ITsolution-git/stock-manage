(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('approvalsController', approvalsController)
            .controller('pendingController', pendingController)
            .controller('deniedController', deniedController);
            

    /** @ngInject */
    function approvalsController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter)
    {
        $scope.company_id = sessionService.get('company_id');
        var vm = this ;

        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'order.id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };


        $scope.filterBy = {
          'temp':'',
          'search': '',
          'seller': '',
          'client': '',
          'created_date': ''
        };
        
        $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
        };
        
        $scope.getResource = function (params, paramsObj, search) {
            
            $scope.params = params;
            $scope.paramsObj = paramsObj;
            $("#ajax_loader").show();
            var orderData = {};

            orderData.cond ={company_id :sessionService.get('company_id'),params:$scope.params};

            return $http.post('api/public/admin/getApprovedOrders',orderData).success(function(response)
            {
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

        $scope.getTab = function()
        {
            $scope.reloadCallback();
        }
    }
    function pendingController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter)
    {
        $scope.company_id = sessionService.get('company_id');
        var vm = this ;

        $scope.approveOrder = function(order_id,sns_shipping,order_sns_status)
        {
            if(order_sns_status == 'approve')
            {
                var combine_array_id = {};
                combine_array_id.id = order_id;
                combine_array_id.company_id = sessionService.get('company_id');
                combine_array_id.company_name = sessionService.get('company_name');
                combine_array_id.sns_shipping = sns_shipping;
                combine_array_id.user_id = sessionService.get('user_id');
                
                $("#ajax_loader").show();
               
                $http.post('api/public/order/snsOrder',combine_array_id).success(function(result) 
                {
                    $("#ajax_loader").hide();
                    if(result.data.success=='1')
                    {
                        notifyService.notify('success',result.data.message);
                        $mdDialog.hide();
                        $scope.reloadCallback();
                    }
                    else
                    {
                        notifyService.notify('error',result.data.message);
                        return false;
                    }
                });
            }
            else
            {
                var UpdateArray = {};
                UpdateArray.table ='orders';
                UpdateArray.data = {'order_sns_status':'Denied'};
                UpdateArray.cond = {id:order_id};

                $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) 
                {
                    if(result.data.success=='1')
                    {
                       $scope.reloadCallback();
                    }
                });
            }
        }

        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'order.id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };


        $scope.filterBy = {
          'temp':'',
          'search': '',
          'seller': '',
          'client': '',
          'created_date': ''
        };
        
        $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
        };
        
        $scope.getResource = function (params, paramsObj, search) {
            
            $scope.params = params;
            $scope.paramsObj = paramsObj;
            $("#ajax_loader").show();
            var orderData = {};

            orderData.cond ={company_id :sessionService.get('company_id'),params:$scope.params};

            return $http.post('api/public/admin/getPendingOrders',orderData).success(function(response)
            {
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

        $scope.getTab = function()
        {
            $scope.reloadCallback();
        }
    }
    function deniedController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter)
    {
        $scope.company_id = sessionService.get('company_id');
        var vm = this ;

        $scope.init = {
          'count': 10,
          'page': 1,
          'sortBy': 'order.id',
          'sortOrder': 'dsc'
        };

        $scope.reloadCallback = function () { };


        $scope.filterBy = {
          'temp':'',
          'search': '',
          'seller': '',
          'client': '',
          'created_date': ''
        };
        
        $scope.search = function ($event){
            $scope.filterBy.name = $event.target.value;
        };
        
        $scope.getResource = function (params, paramsObj, search) {
            
            $scope.params = params;
            $scope.paramsObj = paramsObj;
            $("#ajax_loader").show();
            var orderData = {};

            orderData.cond ={company_id :sessionService.get('company_id'),params:$scope.params};

            return $http.post('api/public/admin/getDeniedOrders',orderData).success(function(response)
            {
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

        $scope.getTab = function()
        {
            $scope.reloadCallback();
        }
    }
})();