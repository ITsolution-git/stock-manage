(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('AddDesignController', AddDesignController);

   
    function AddDesignController($filter,$scope,$stateParams, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService,$log)
    {

        


         $scope.saveDesign = function (designData) {
          console.log(designData);return false;
            designData.order_id = $stateParams.id;
         
             
                  var combine_array_id = {};
                 
                  combine_array_id.designData = designData;
                  

                  $http.post('api/public/order/addDesign',combine_array_id).success(function(result) 
                    {
                        $mdDialog.hide();
                    
                    });
        };

        $scope.cancel = function () {
            $mdDialog.hide();
        };



$scope.simulateQuery = false;
$scope.isDisabled    = false;
// list of `state` value/display objects

//console.log( $scope.states )
$scope.querySearch   = querySearch;


var companyData = {};
            companyData.cond ={is_delete :'1',status :'1'};
            companyData.table ='color';

                $http.post('api/public/common/allColor',companyData).success(function(result) {
        
                        if(result.data.success == '1') 
                        {
                            $scope.screen_allcolors =result.data.records;
                        } 
                        else
                        {
                            $scope.screen_allcolors=[];
                        }
                });


function querySearch (query) {
                              $scope.states        = loadAll();

                              var results = query ? $scope.states.filter( createFilterFor(query) ) : $scope.states,
                                  deferred;

                              if ($scope.simulateQuery) {
                                deferred = $q.defer();
                                $timeout(function () { deferred.resolve( results ); }, Math.random() * 1000, false);
                                return deferred.promise;
                              } else {
                                return results;
                              }
                            }

                            /**
                             * Build `states` list of key/value pairs
                             */
                            function loadAll() {
                              var allStates = $scope.screen_allcolors;
                              console.log(allStates);
                              return allStates;
                            }
                             function createFilterFor(query) {
                                  var lowercaseQuery = angular.lowercase(query);
                                 // console.log(lowercaseQuery);
                                  return function filterFn(state) {
                                    return (state.name.indexOf(lowercaseQuery) === 0);
                                  };
                                }


                                $scope.change_color = function(id,param){
                                 if(param == 'front_color_id')
                                 {
                                    $scope.front_color_id = angular.copy(id);
                                 }
                               }

  





    }
})();