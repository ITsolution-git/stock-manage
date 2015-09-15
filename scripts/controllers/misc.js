
app.controller('miscListCtrl', ['$scope','$http','$location','$state','$stateParams','fileUpload','deleteMessage', function($scope,$http,$location,$state,$stateParams,fileUpload,deleteMessage) {
  

 $http.get('api/public/common/getAllMiscData').success(function(result, status, headers, config) {

              $scope.miscData = result.data.records;
     
      });


var range = [];
for(var i=0;i<15;i++) {
  range.push(i);
}
$scope.range = range;


}]);

