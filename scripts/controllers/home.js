app.controller('homeCtrl', ['$scope','$rootScope','$http','$location','$state','$filter','$modal','AuthService','$log','AllConstant', function($scope,$rootScope,$http,$location,$state,$filter,$modal,AuthService,$log,AllConstant) {


          $("#ajax_loader").show();

            var company_id = $rootScope.company_profile.company_id;
            var login_id = $scope.app.user_id;
   
                
      $scope.orderListAll = function($event,column_name){


        var order_list_data = {};
          var condition_obj = {};
          condition_obj['company_id'] =  company_id;
          order_list_data.cond = angular.copy(condition_obj);


       if($event) {
            
          $scope.name_filed = $event.target.name;
          var obj = {};
          obj[$scope.name_filed] =  $event.target.value;
          order_list_data.data = angular.copy(obj);
          
          } 

        $http.post('api/public/order/listOrder',order_list_data).success(function(Listdata) {
            $scope.listOrder = Listdata.data;
            $("#ajax_loader").hide();

        });

       } 

      $scope.orderListAll();
    
         $http.get('api/public/common/getAllMiscDataWithoutBlank').success(function(result, status, headers, config) {
                      $scope.miscData = result.data.records;
            });

    
        var companyData = {};
        companyData.table ='client'
        companyData.cond ={status:1,is_delete:1,company_id:company_id}
        
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


        $http.get('api/public/common/getStaffList').success(function(result, status, headers, config) {
              $scope.staffList = result.data.records;
        });


        $scope.new_data_fun = function(){
        
        var combine_array = {};
         combine_array.data = {};
          combine_array.cond = {};


        combine_array.data.f_approval = $scope.f_approval;
       combine_array.data.client_id = $scope.client_id;
       combine_array.data.sales_id = $scope.sales_id;
        combine_array.cond.company_id = company_id;



        $http.post('api/public/order/listOrder',combine_array).success(function(Listdata) {
            $scope.listOrder = Listdata.data;
            $("#ajax_loader").hide();

        });

       }
     

}]);
