(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('PositionDialogController', PositionDialogController);
/** @ngInject */
    function PositionDialogController(order_id,quantity,$stateParams,$scope, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService)
    {

          // change display number to design Id for fetching the order data
          var design_data = {};
           design_data.cond ={company_id :sessionService.get('company_id'),display_number:$stateParams.id};
           design_data.table ='order_design';
          
          $http.post('api/public/common/GetTableRecords',design_data).success(function(result) {
              
              if(result.data.success == '1') 
              {
                  $scope.design_id = result.data.records[0].id;

              } 
          });


      
            $scope.order_design_position={};
            $scope.order_design_position.qnty = quantity;

            var misc_list_data = {};
            var condition_obj = {};
            condition_obj['company_id'] =  sessionService.get('company_id');
            misc_list_data.cond = angular.copy(condition_obj);

            $http.post('api/public/common/getAllMiscDataWithoutBlank',misc_list_data).success(function(result, status, headers, config) {
                      $scope.miscData = result.data.records;
            });

      
                 $scope.save = function (positionData) {
                    
                   if(positionData == undefined) {

                      var data = {"status": "error", "message": "Position and Quantity should not be blank"}
                              notifyService.notify(data.status, data.message);
                              return false;
                    } else if(positionData.qnty == undefined) {

                      var data = {"status": "error", "message": "Quantity should not be blank"}
                              notifyService.notify(data.status, data.message);
                              return false;
                    } else if(positionData.position_id == undefined) {

                      var data = {"status": "error", "message": "Position should not be blank"}
                              notifyService.notify(data.status, data.message);
                              return false;
                    }else if(positionData.placement_type == undefined) {

                      var data = {"status": "error", "message": "Placement Type should not be blank"}
                              notifyService.notify(data.status, data.message);
                              return false;
                    }

              var combine_array_id = {};
              var position_id = positionData.position_id
             
              combine_array_id.positionData = positionData;
              combine_array_id.design_id = $scope.design_id;
              combine_array_id.order_id = order_id;
              combine_array_id.position = $scope.miscData.position[position_id].value;

              
 
              $http.post('api/public/order/addPosition',combine_array_id).success(function(result) 
                {

                   if(result.data.success == '2') 

                        {
                       
                             var data = {"status": "error", "message": "This position already exists in this design."}
                             notifyService.notify(data.status, data.message);
                             return false;
                        } 


                    $mdDialog.hide();
                   
                });
        };

        $scope.cancel = function () {
            $mdDialog.hide();
        };
    }
})();