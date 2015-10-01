
app.controller('orderListCtrl', ['$scope','$http','$location','$state','$modal','AuthService','$log','AllConstant', function($scope,$http,$location,$state,$modal,AuthService,$log,AllConstant) {
                          
      $http.get('api/public/order/listOrder').success(function(Listdata) {
                      $scope.listOrder = Listdata.data;
              });

      $scope.deleteorder = function (order_id) {

                                var permission = confirm(AllConstant.deleteMessage);
                                if (permission == true) {
                                $http.post('api/public/order/deleteOrder',order_id).success(function(result, status, headers, config) {
                                              
                                              if(result.data.success=='1')
                                              {
                                                $state.go('order.list');
                                                $("#order_"+order_id).remove();
                                                return false;
                                              }  
                                         });
                                      }
      } // DELETE ORDER FINISH

$scope.items = ['item1', 'item2', 'item3'];

var companyData = {};
      companyData.table ='client'
      companyData.cond ={status:1,is_delete:1}
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

$scope.openpopup = function () {

  var modalInstance = $modal.open({
      animation: $scope.animationsEnabled,
      templateUrl: 'views/front/order/add.html',
      scope: $scope,
      size: 'sm'
      
    });

    modalInstance.result.then(function (selectedItem) {
      $scope.selected = selectedItem;
    }, function () {
      $log.info('Modal dismissed at: ' + new Date());
    });

 

  $scope.ok = function (orderData) {
   

    /*$http.post('api/public/order/orderAdd',data).success(function(result, status, headers, config) {
        
                                   $state.go('order.list');
                                    return false;
                         
                          });*/

 var order_data = {};
  order_data.data = orderData;
 // Address_data.data.client_id = $stateParams.id;
  order_data.table ='order'


  $http.post('api/public/common/InsertRecords',order_data).success(function(result) {
      if(result.data.success == '1') 
      {
       
           modalInstance.close($scope);
          $state.go('order.edit',{id: result.data.id,client_id:order_data.data.client_id});
                                return false;
      }
      else
      {
          console.log(result.data.message);
      }
  });



   // modalInstance.close($scope.selected.item);
  };

  $scope.cancel = function () {
    modalInstance.dismiss('cancel');
  };
};

}]);


app.controller('orderEditCtrl', ['$scope','$http','$location','$state','$stateParams','$modal','AuthService','$log','AllConstant', function($scope,$http,$location,$state,$stateParams,$modal,AuthService,$log,AllConstant) {
                          
     


if($stateParams.id && $stateParams.client_id) {

                          var combine_array_id = {};
                          combine_array_id.id = $stateParams.id;
                          combine_array_id.client_id = $stateParams.client_id;

                        
                           $http.post('api/public/order/orderDetail',combine_array_id).success(function(result, status, headers, config) {
        
                            if(result.data.success == '1') {
                                       
                                     $scope.order = result.data.records[0];
                                     $scope.client_data = result.data.client_data;
                                     $scope.client_main_data = result.data.client_main_data;
                                    

                                    
                             }  else {


                              $state.go('app.dashboard');
                             }
                         
                          });

                         }


      var companyData = {};
      companyData.table ='client'
      companyData.cond ={status:1,is_delete:1}
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


      var priceGridData = {};
      priceGridData.table ='price_grid'
      priceGridData.cond ={status:1,is_delete:1}
      $http.post('api/public/common/GetTableRecords',priceGridData).success(function(result) {
          if(result.data.success == '1') 
          {
              $scope.allGrid =result.data.records;
          } 
          else
          {
              $scope.allGrid=[];
          }
        });

          

      var miscData = {};
      miscData.table ='misc_type'
      miscData.cond ={status:1,is_delete:1,type:'approval'}
      $http.post('api/public/common/GetTableRecords',miscData).success(function(result) {
          if(result.data.success == '1') 
          {
              $scope.allMisc =result.data.records;
          } 
          else
          {
              $scope.allMisc=[];
          }
        });


      $http.get('api/public/common/getStaffList').success(function(result, status, headers, config) 
                          {
                              if(result.data.success == '1') 
                              {
                                  $scope.staffList =result.data.records;
                              } 
                              
                          });

      $http.get('api/public/common/getBrandCo').success(function(result, status, headers, config) 
                          {
                              if(result.data.success == '1') 
                              {
                                  $scope.brandCoList =result.data.records;
                              } 
                              
                          });


       $scope.saveOrderDetails=function(postArray,id)
                          {
                           
                            var order_data = {};
                            order_data.table ='order'
                            order_data.data =postArray
                            order_data.cond ={id:id}
                            $http.post('api/public/common/UpdateTableRecords',order_data).success(function(result) {
                                 $state.go('order.list');
                              });
                          }



}]);


app.controller('orderAddCtrl', ['$scope','$http','$location','$state','$modal','AuthService','$log','AllConstant', function($scope,$http,$location,$state,$modal,AuthService,$log,AllConstant) {
            
      var companyData = {};
      companyData.table ='client'
      companyData.cond ={status:1,is_delete:1}
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

}]);






