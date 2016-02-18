
app.controller('miscListCtrl', ['$scope','$rootScope','$http','$location','$state','$stateParams','fileUpload','AllConstant','AuthService','$filter', function($scope,$rootScope,$http,$location,$state,$stateParams,fileUpload,AllConstant,AuthService,$filter) {
  AuthService.AccessService('FM');

$("#ajax_loader").show();

 var company_id = $rootScope.company_profile.company_id;

 var misc_list_data = {};
 var condition_obj = {};
 condition_obj['company_id'] =  company_id;
 misc_list_data.cond = angular.copy(condition_obj);

 $http.post('api/public/common/getAllMiscData',misc_list_data).success(function(result, status, headers, config) {
             
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

