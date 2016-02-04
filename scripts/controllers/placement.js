
app.controller('placementListCtrl', ['$scope','$http','$location','$state','$stateParams','AuthService','fileUpload','AllConstant','$filter', function($scope,$http,$location,$state,$stateParams,AuthService,fileUpload,AllConstant,$filter) {
   AuthService.AccessService('FM');


 $http.get('api/public/common/getAllMiscDataWithoutBlank').success(function(result, status, headers, config) {

           
              $scope.miscData = result.data.records;

             
      });


  $http.get('api/public/common/getAllPlacementData').success(function(result, status, headers, config) {
              $scope.placementData = result.data.records;
              $scope.pagination = AllConstant.pagination;
              
              for(var i=0; i < $scope.placementData.length; i++){
                $scope.placementData[i].user = {
                  group: $scope.placementData[i].misc_id,
                  groupName: $scope.placementData[i].position,
                };  

                $scope.$watch($scope.placementData[i].user.group, function(newVal, oldVal) {
                  if (newVal !== oldVal) {

                    var selected = $filter('filter')($scope.groups, {id: $scope.user.group});
                    $scope.user.groupName = selected.length ? selected[0].value : null;
                  }
                });
            }

            var init;

            $scope.searchKeywords = '';
            $scope.filteredPlacementData = [];
            $scope.row = '';
            $scope.select = function (page) {
                var end, start;
                start = (page - 1) * $scope.numPerPage;
                end = start + $scope.numPerPage;
                return $scope.currentPagePlacementData = $scope.filteredPlacementData.slice(start, end);
            };
            $scope.onFilterChange = function () {
                $scope.select(1);
                $scope.currentPage = 1;
                return $scope.row = '';
            };
            $scope.onNumPerPageChange = function () {
                $scope.select(1);
                return $scope.currentPage = 1;
            };
            $scope.onOrderChange = function () {
                $scope.select(1);
                return $scope.currentPage = 1;
            };
            $scope.search = function () {
                $scope.filteredPlacementData = $filter('filter')($scope.placementData, $scope.searchKeywords);
                return $scope.onFilterChange();
            };
            $scope.order = function (rowName) {
                if ($scope.row === rowName) {
                    return;
                }
                $scope.row = rowName;
                $scope.filteredPlacementData = $filter('orderBy')($scope.placementData, rowName);
                return $scope.onOrderChange();
            };
            $scope.numPerPageOpt = [10, 20, 50, 100];
            $scope.numPerPage = 10;
            $scope.currentPage = 1;
            $scope.currentPagePlacementData = [];

            init = function () {
                $scope.search();

                return $scope.select($scope.currentPage);
            };
            return init();
      });
  


  $scope.groups = [];

  $scope.loadGroups = function() {
    return $scope.groups.length ? null : $http.get('api/public/common/getMiscData').success(function(data) {
      
      $scope.groups = data;
      
    });
  };

  





$scope.updatePlacement = function(value,id,updatedcolumn) {


if(angular.isUndefined(id)){

   var combine_array_data = {};
              combine_array_data.updatedcolumn = value;
              combine_array_data.columnname = updatedcolumn;
             
               $http.post('api/public/admin/placementInsert',combine_array_data).success(function(result, status, headers, config) {
                        
                          });
} else {

    var combine_array_data = {};
              combine_array_data.updatedcolumn = value;
              combine_array_data.columnname = updatedcolumn;
              combine_array_data.id = id;

             
               $http.post('api/public/admin/placementSave',combine_array_data).success(function(result, status, headers, config) {
                         
                          });

}
            
       
                         $state.go('setting.placement','',{reload:true});
                                return false;                   

  };


  $scope.addPlacement = function(){
                            $scope.placementData.push({ misc_value:''});
                          }



  $scope.removePlacement = function(index,id){

  var permission = confirm("Are you sure want to delete this record ? Clicking Ok will delete record permanently.");
                                if (permission == true) {
  
  if(angular.isUndefined(id)){
     $scope.placementData.splice(index,1);
  } else {

 var position_data = {};
position_data.table ='placement'
position_data.cond ={id:id}
$http.post('api/public/common/DeleteTableRecords',position_data).success(function(result) {
     

  });


     $scope.placementData.splice(index,1);
  }

}
   
}


}]);

