
app.controller('miscListCtrl', ['$scope','$http','$location','$state','$stateParams','fileUpload','deleteMessage', function($scope,$http,$location,$state,$stateParams,fileUpload,deleteMessage) {
  
 $http.get('api/public/admin/misc/value1').success(function(result, status, headers, config) {

                                  $scope.value1 = result.data.records;
                         
                          });

 $http.get('api/public/common/getAllMiscData').success(function(result, status, headers, config) {

              $scope.miscData = result.data.records;
     
      });


}]);

