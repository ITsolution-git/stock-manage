(function ()
{
    'use strict';

    angular
        .module('app.settings')
        .controller('AddLaborController', AddLaborController);

   
    function AddLaborController(labor_id,$filter,$scope,$stateParams, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService,$log)
    {

          var order_data = {};
           order_data.cond ={status :1};
           order_data.table ='days';
          
          $http.post('api/public/common/GetTableRecords',order_data).success(function(result) {
                  $scope.items =result.data.records;

          });
          
          var vm = this;
          vm.title = 'Add/Edit Shift';

        
        if(labor_id != 0) {

             var combine_array_id = {};
            combine_array_id.id = labor_id;
            
            $http.post('api/public/labor/laborDetail',combine_array_id).success(function(result, status, headers, config) {
               
                if(result.data.success == '1') {
                    $scope.labor = result.data.records[0];

                    $scope.selected = result.data.selectedDays;

                    
                }
                
            });
        } else {

            $scope.labor = {};
           
        }

      $scope.selected = [];
       
     
      $scope.toggle = function (item, list) {
        var idx = list.indexOf(item);
        if (idx > -1) {
          list.splice(idx, 1);
        }
        else {
          list.push(item);
        }
      };

      $scope.exists = function (item, list) {
        
        return list.indexOf(parseInt(item)) > -1;
      };


        $scope.savelabor = function (laborData,selected) {



            if(laborData.shift_name == undefined || laborData.shift_name == '') {

                      var data = {"status": "error", "message": "Shift Name should not be blank"}
                              notifyService.notify(data.status, data.message);
                              return false;
            }

             if(selected.length == 0) {

                      var data = {"status": "error", "message": "Select atleast 1 day"}
                              notifyService.notify(data.status, data.message);
                              return false;
            }
            laborData.days_array = selected;
            
            if(labor_id != 0) {

                var order_data = {};
                order_data.table ='labor'
                order_data.laborData =laborData
                order_data.cond ={id:labor_id}
            
                $http.post('api/public/labor/editLabor',order_data).success(function(result) {
                     $mdDialog.hide();
                      var data = {"status": "success", "message": "Shift Updated Successfully."}
                     notifyService.notify(data.status, data.message);
                });


            } else {

                laborData.company_id = sessionService.get('company_id'); 
                laborData.shift_type = 0; 
                var combine_array_id = {};
                combine_array_id.laborData = laborData;

                $http.post('api/public/labor/addLabor',combine_array_id).success(function(result) 
                {
                    $mdDialog.hide();
                    var data = {"status": "success", "message": "Shift Added Successfully."}
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

      

        



       
    }
})();