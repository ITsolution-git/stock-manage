(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('AddDesignController', AddDesignController);

   
    function AddDesignController(event_id,$filter,$scope,$stateParams, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService,$log)
    {
        var vm = this;
          vm.title = 'Add New Design';

        
        if(event_id != 0) {

             var combine_array_id = {};
            combine_array_id.id = event_id;
            
            $http.post('api/public/order/designDetail',combine_array_id).success(function(result, status, headers, config) {
               
                if(result.data.success == '1') {
                    
                    
                    result.data.records[0].hands_date = new Date(result.data.records[0].hands_date);
                    result.data.records[0].shipping_date = new Date(result.data.records[0].shipping_date);
                    result.data.records[0].start_date = new Date(result.data.records[0].start_date);

                    $scope.design = result.data.records[0];

                }
                
            });
        } else {

            $scope.design = {};
            $scope.design.front_color_name = '';
            $scope.design.side_right_color_name = '';
            $scope.design.top_color_name = '';
            $scope.design.back_color_name = '';
            $scope.design.side_left_color_name = '';
            $scope.design.bottom_color_name = '';
        }
       
        

        $scope.saveDesign = function (designData) {
         
          
            if(designData.design_name == undefined || designData.design_name == '') {

                      var data = {"status": "error", "message": "Design Name should not be blank"}
                              notifyService.notify(data.status, data.message);
                              return false;
            }
            
            if(event_id != 0) {

                var order_data = {};
                order_data.table ='order_design'
                order_data.designData =designData
                order_data.cond ={id:event_id}
            
                $http.post('api/public/order/editDesign',order_data).success(function(result) {
                     $mdDialog.hide();
                      var data = {"status": "success", "message": "Design Updated Successfully."}
                     notifyService.notify(data.status, data.message);
                });


            } else {

                designData.order_id = $stateParams.id;     
                var combine_array_id = {};
                combine_array_id.designData = designData;

                $http.post('api/public/order/addDesign',combine_array_id).success(function(result) 
                {
                    $mdDialog.hide();
                    var data = {"status": "success", "message": "Design Added Successfully."}
                     notifyService.notify(data.status, data.message);
                });

            }
           
        };

        $scope.cancel = function () {
            $mdDialog.hide();
        };

        $scope.simulateQuery = false;
        $scope.isDisabled    = false;
        


        var companyData = {};
        companyData.cond ={is_delete :'1',status :'1'};
        companyData.table ='color';

        $http.post('api/public/common/allColor',companyData).success(function(result) {

                if(result.data.success == '1') 
                {
                    $scope.screen_allcolors =result.data.records;
                    $scope.querySearch   = querySearch;
                } 
                else
                {
                    $scope.screen_allcolors=[];
                }
        });

       

        function querySearch (query) {
           
            $scope.states = $scope.screen_allcolors;
           
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

       
        function createFilterFor(query) {
            var lowercaseQuery = angular.lowercase(query);
            // console.log(lowercaseQuery);
            return function filterFn(state) {
                return (state.name.indexOf(lowercaseQuery) === 0);
            };
        }
  vm.createDate1="",
        vm.createDate2="",
        vm.createDate3="",
        vm.monthSelectorOptions = {
            start: "year",
            depth: "year"
          };
          vm.getType = function(x) {
            return typeof x;
          };
          vm.isDate = function(x) {
            return x instanceof Date;
          };
        $scope.change_color = function(id,param){
           
             if(param == 'front_color_id')
                {
                $scope.design.front_color_id = angular.copy(id);
                }

             if(param == 'side_right_color_id')
                {
                $scope.design.side_right_color_id = angular.copy(id);
                }

             if(param == 'top_color_id')
                {
                $scope.design.top_color_id = angular.copy(id);
                }

             if(param == 'back_color_id')
                {
                $scope.design.back_color_id = angular.copy(id);
                }

             if(param == 'side_left_color_id')
                {
                $scope.design.side_left_color_id = angular.copy(id);
                }

             if(param == 'bottom_color_id')
                {
                $scope.design.bottom_color_id = angular.copy(id);
                }
            
        }
    }
})();