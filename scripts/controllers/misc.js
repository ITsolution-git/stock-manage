
app.controller('miscListCtrl', ['$scope','$http','$location','$state','$stateParams','fileUpload','deleteMessage','AuthService', function($scope,$http,$location,$state,$stateParams,fileUpload,deleteMessage,AuthService) {
  
AuthService.checksession();

 $http.get('api/public/common/getAllMiscData').success(function(result, status, headers, config) {

              $scope.miscData = result.data.records;
     
      });


var range = [];
for(var i=0;i<15;i++) {
  range.push(i);
}
$scope.range = range;


$scope.updateUser = function(value,id) {

              var combine_array_data = {};
              combine_array_data.value = value;
              combine_array_data.id = id;

               $http.post('api/public/admin/miscSave',combine_array_data).success(function(result, status, headers, config) {
                         
                          });
                          

  };


}]);

