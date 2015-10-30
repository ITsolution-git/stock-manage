app.controller('finishingListCtrl', ['$scope','$http','$location','$state','$modal','AuthService','$log','AllConstant', function($scope,$http,$location,$state,$modal,AuthService,$log,AllConstant) {
                          
    $http.get('api/public/finishing/listFinishing').success(function(Listdata) {
        $scope.listFinishing = Listdata.data;
    });

    $scope.finishing = {date: null};

    $scope.deleteorder = function (order_id)
    {
        var permission = confirm(AllConstant.deleteMessage);
        if (permission == true)
        {
            $http.post('api/public/finishing/deleteFinishing',order_id).success(function(result, status, headers, config)
            {
                      if(result.data.success=='1')
                      {
                        $state.go('finishing.list');
                        $("#order_"+order_id).remove();
                        return false;
                      }  
            });
        }
    } // DELETE ORDER FINISH

    $scope.getList = function ()
    {
        if($scope.finishing.date != null)
        {
            var date = get_formated_date($scope.finishing.date);
            var data = {order_date:date};
        }
        else
        {
            var data = {};
        }

        $http.post('api/public/finishing/listFinishing',data).success(function(result)
        {
            $scope.listFinishing = result.data;
        });
    }

    var companyData = {};
    companyData.table ='client'
    companyData.cond ={status:1,is_delete:1}
    $http.post('api/public/common/GetTableRecords',companyData).success(function(result)
    {
          if(result.data.success == '1') 
          {
              $scope.allCompany =result.data.records;
          } 
          else
          {
              $scope.allCompany=[];
          }
    });

    $scope.updateFinishing = function (table,field,id)
    {
        var value = $("#category_name_"+id).val();
        
        var finishing = {value:value,table:table,field:field,id:id};
        $http.post('api/public/finishing/updateFinishing',finishing).success(function(result)
        {
              if(result.data.success == '1') 
              {
                  $scope.allCompany =result.data.records;
              }
              else
              {
                  $scope.allCompany=[];
              }
        });
    }
}]);