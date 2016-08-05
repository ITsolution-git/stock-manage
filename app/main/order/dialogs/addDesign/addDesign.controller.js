(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('AddDesignController', AddDesignController);

   
    function AddDesignController(event_id,$filter,$scope,$stateParams, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService,$log)
    {

          
          $scope.updateStartDate = function(){
            $scope.minDate = new Date($scope.design.start_date);
            $scope.minShipDate = new Date($scope.design.start_date);
          }

          $scope.updateshipDate = function(){
            $scope.minShipDate = new Date($scope.design.shipping_date);
          }

          
          var vm = this;
          vm.title = 'Add/Edit Design';

        
        if(event_id != 0) {

             var combine_array_id = {};
            combine_array_id.id = event_id;
            
            $http.post('api/public/order/designDetail',combine_array_id).success(function(result, status, headers, config) {
               
                if(result.data.success == '1') {
                    $scope.design = result.data.records[0];
                    $scope.minDate = new Date($scope.design.start_date);
                     $scope.minShipDate = new Date($scope.design.shipping_date);
                }
                
            });
        } else {

            $scope.design = {};
           
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
       
    }
})();