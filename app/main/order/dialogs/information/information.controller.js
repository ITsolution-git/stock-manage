(function ()
{
    'use strict';

    angular
        .module('app.order')
        .controller('InformationController', InformationController);

    /** @ngInject */
    function InformationController(order_id,client_id,$filter,$scope,$stateParams, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,$resource,$http,notifyService,$state,sessionService,$log)
    {

        $scope.client_id = client_id;
        $scope.order_id = order_id;
        
        function myCustomPropertyForTheObjectCompany()
        {
            $scope.addressModel = [];
        }


        $scope.orderDetailInfo = function(order_id){

            var combine_array_id = {};
            combine_array_id.id = order_id;
            combine_array_id.company_id = sessionService.get('company_id');
            
            $http.post('api/public/order/orderDetailInfo',combine_array_id).success(function(result, status, headers, config) {
            
                if(result.data.success == '1') {
                   $scope.order_data = result.data.records[0];
                   $scope.order_items = result.data.order_item;
                   $scope.allGrid = result.data.price_grid;
                   $scope.staffList =result.data.staff;
                   $scope.brandCoList =result.data.brandCo;
                   $scope.contact_main =result.data.contact_main;
                  
                   //$scope.minDate = new Date(result.data.records[0].date_start);
                   //$scope.minShipDate = new Date(result.data.records[0].date_shipped);
                }
               
            });
          }

$scope.addresscustomTexts = {buttonDefaultText: 'Select Address'};


$scope.allOrderAddress = function (order_id) {

                  var addressData = {};
                  addressData.order_id = order_id;


                  $http.post('api/public/order/allOrderAddress',addressData).success(function(result)
                  {   
                      if(result.data.success=='1')
                      {   
                        $scope.addressModel = result.data.records;
                        $scope.addressModelOld = angular.copy(result.data.records);

                          
                      } else {
                          $scope.addressModel = [];
                           $scope.addressModelOld = [];
                         
                      }     
                          
                  });

};


          $scope.selectedItemChange = function (client_id,company_change) {


            
                  if(company_change == 0) {

                    if(client_id != $scope.client_id) {

                       $scope.addressChecksettings = {externalIdProp: myCustomPropertyForTheObjectCompany()}
                       

                          for (var i = 0; i < $scope.addressModel.length; i++) {              
                             $scope.addressModel[i].id = null;
                          }
                      } else {

                        $scope.allOrderAddress($scope.order_id);
                      }


                     


                  }
                  var clientData = {};
                  clientData.client_id =client_id;


                  $http.post('api/public/order/GetAllClientsAddress',clientData).success(function(result)
                  {   
                      if(result.data.success=='1')
                      {   
                        $scope.allAddressData = result.data.records;

                          
                      } else {
                          $scope.allAddressData = [];
                         
                      }     
                          
                  });

        };

        var companyData = {};
        companyData.company_id =sessionService.get('company_id');
        companyData.table = 'client';
        companyData.cond = {'company_id':sessionService.get('company_id'),'is_delete':1};

        $http.post('api/public/common/GetTableRecords',companyData).success(function(result) {

                if(result.data.success == '1') 
                {
                    $scope.allCompany =result.data.records;
                } 
                else
                {
                    $scope.allCompany=[];
                }
        });

          var misc_list_data = {};
          var condition_obj = {};
          condition_obj['company_id'] =  sessionService.get('company_id');
          misc_list_data.cond = angular.copy(condition_obj);

          $http.post('api/public/common/getAllMiscDataWithoutBlank',misc_list_data).success(function(result, status, headers, config) {
                    $scope.miscData = result.data.records;
          });

    $scope.allOrderAddress(order_id);

     $scope.orderDetailInfo(order_id);

     $scope.selectedItemChange(client_id,1);
     

      $scope.cancel = function () {
            $mdDialog.hide();
        };

        $scope.saveOrderInfo = function (orderDataDetail) {
         
         
         var date_shipped;
         var date_start ;
         var in_hands_by;


        if(orderDataDetail.date_shipped != '') {
            date_shipped = new Date(orderDataDetail.date_shipped);
        }

        if(orderDataDetail.date_start != '') {
            date_start = new Date(orderDataDetail.date_start);
        }

        if(orderDataDetail.in_hands_by != '') {
            in_hands_by = new Date(orderDataDetail.in_hands_by);
        }
          
      
              
        if(date_shipped < date_start) {
           var data = {"status": "error", "message": "Shipped Date must be greater then Start Date."}
            notifyService.notify(data.status, data.message);
            return false;
        }

        if(in_hands_by < date_shipped) {
           var data = {"status": "error", "message": "Hands Date must be greater then Shipped Date."}
            notifyService.notify(data.status, data.message);
            return false;
        }

        if(in_hands_by < date_start) {
           var data = {"status": "error", "message": "Hands Date must be greater then Start Date."}
            notifyService.notify(data.status, data.message);
            return false;
        }


        
                var order_data = {};
                order_data.table ='orders'
                order_data.orderDataDetail =orderDataDetail
                order_data.addressModel =$scope.addressModel
                order_data.addressModelOld = $scope.addressModelOld;
                order_data.cond ={id:order_id}
            
                $http.post('api/public/order/editOrder',order_data).success(function(result) {
                     $mdDialog.hide();
                     var data = {"status": "success", "message": "Data Updated Successfully."}
                     notifyService.notify(data.status, data.message);
                });


            
           
        };
    }
})();