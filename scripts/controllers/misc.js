
app.controller('miscListCtrl', ['$scope','$http','$location','$state','$stateParams','fileUpload','AllConstant','AuthService', function($scope,$http,$location,$state,$stateParams,fileUpload,AllConstant,AuthService) {
  
AuthService.checksession();
$("#ajax_loader").show();
 $http.get('api/public/common/getAllMiscData').success(function(result, status, headers, config) {

              
              $scope.miscData = result.data.records;
              $scope.pagination = AllConstant.pagination;
              $("#ajax_loader").hide();
    
      });


var range = [];
for(var i=0;i<17;i++) {
  range.push(i);
}
$scope.range = range;


$('.footable-page a').filter('[data-page="0"]').trigger('click');

$scope.updateUser = function(value,id) {

              var combine_array_data = {};
              combine_array_data.value = value;
              combine_array_data.id = id;

               $http.post('api/public/admin/miscSave',combine_array_data).success(function(result, status, headers, config) {
                         
                          });
                          

  };


}]);

