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
            $http.post('api/public/finishing/deleteOrder',order_id).success(function(result, status, headers, config)
            {
                      if(result.data.success=='1')
                      {
                        $state.go('order.list');
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

    $scope.openpopup = function ()
    {
        var modalInstance = $modal.open({
            animation: $scope.animationsEnabled,
            templateUrl: 'views/front/finishing/add.html',
            scope: $scope,
            size: 'sm'
        });

        modalInstance.result.then(function (selectedItem)
        {
            $scope.selected = selectedItem;
        }, function () {
            $log.info('Modal dismissed at: ' + new Date());
        });

        $scope.ok = function (orderData)
        {
            /*$http.post('api/public/finishing/orderAdd',data).success(function(result, status, headers, config)
            {
                $state.go('order.list');
                return false;
            });*/

            var order_data = {};
            order_data.data = orderData;
            // Address_data.data.client_id = $stateParams.id;
            order_data.table ='orders'

            $http.post('api/public/common/InsertRecords',order_data).success(function(result)
            {
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