(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('OrderDialogController', OrderDialogController);
/** @ngInject */
    function OrderDialogController($scope, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService)
    {
        var companyData = {};
            companyData.company_id =sessionService.get('company_id');

                $http.post('api/public/order/GetAllClientsLowerCase',companyData).success(function(result) {
       
                        if(result.data.success == '1') 
                        {
                            $scope.allCompany =result.data.records;

                             $scope.simulateQuery = false;
                              $scope.isDisabled    = false;
                              $scope.states        = loadAll();
                              $scope.querySearch   = querySearch;

                        } 
                        else
                        {
                            $scope.allCompany=[];
                        }
                });


                function querySearch (query) 
                                    {
                                        var results = query ? $scope.states.filter( createFilterFor(query) ) : $scope.states, deferred;
                                        if ($scope.simulateQuery) 
                                        {
                                            deferred = $q.defer();
                                            $timeout(function () { deferred.resolve( results ); }, Math.random() * 1000, false);
                                            return deferred.promise;
                                        } 
                                        else 
                                        {
                                            return results;
                                        }
                                    }
                                    function loadAll() 
                                    {
                                        var allStates = $scope.allCompany;
                                        return allStates;
                                    }
                                    function createFilterFor(query) 
                                    {
                                        var lowercaseQuery = angular.lowercase(query);
                                        return function filterFn(state) 
                                        {
                                            return (state.client_company.indexOf(lowercaseQuery) === 0);
                                        };
                                    }


                 $scope.save = function (orderData) {
                  
              
                   if(orderData == undefined) {

                      var data = {"status": "error", "message": "Company and Job Name should not be blank"}
                              notifyService.notify(data.status, data.message);
                              return false;
                    } else if(orderData.name == undefined) {

                      var data = {"status": "error", "message": "Name should not be blank"}
                              notifyService.notify(data.status, data.message);
                              return false;
                    } else if(orderData.client == null) {

                      var data = {"status": "error", "message": "Company should not be blank"}
                              notifyService.notify(data.status, data.message);
                              return false;
                    }

              var combine_array_id = {};
             
              combine_array_id.orderData = orderData;
              combine_array_id.company_id = sessionService.get('company_id');
              combine_array_id.login_id = sessionService.get('user_id');

              

              $http.post('api/public/order/addOrder',combine_array_id).success(function(result) 
                {
                    $mdDialog.hide();
                    $state.go('app.order.order-info',{id: result.data.id});
                    return false;
                    
                });
        };

        $scope.cancel = function () {
            $mdDialog.hide();
        };
    }
})();