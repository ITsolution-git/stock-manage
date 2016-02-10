
app.controller('colorListCtrl', ['$scope','$http','notifyService','$location','$state','$stateParams','$modal','fileUpload','AllConstant','AuthService','$filter', function($scope,$http,notifyService,$location,$state,$stateParams,$modal,fileUpload,AllConstant,AuthService,$filter) {
  
AuthService.checksession();
 get_all_color();
function get_all_color() {

  $http.get('api/public/common/getAllColorData').success(function(result, status, headers, config) {
              $scope.colors = result.data.records;

              var init;

              $scope.searchKeywords = '';
              $scope.filteredColors = [];
              $scope.row = '';
              $scope.select = function (page) {
                  var end, start;
                  start = (page - 1) * $scope.numPerPage;
                  end = start + $scope.numPerPage;
                  return $scope.currentPageColors = $scope.filteredColors.slice(start, end);
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
                  $scope.filteredColors = $filter('filter')($scope.colors, $scope.searchKeywords);
                  return $scope.onFilterChange();
              };
              $scope.order = function (rowName) {
                  if ($scope.row === rowName) {
                      return;
                  }
                  $scope.row = rowName;
                  $scope.filteredColors = $filter('orderBy')($scope.colors, rowName);
                  return $scope.onOrderChange();
              };
              $scope.numPerPageOpt = [10, 20, 50, 100];
              $scope.numPerPage = 10;
              $scope.currentPage = 1;
              $scope.currentPageColors = [];

              init = function () {
                  $scope.search();

                  return $scope.select($scope.currentPage);
              };
              return init();

      });

}

$scope.updateColors = function(value,id,updatedcolumn) {


if(angular.isUndefined(id)){

 

   var combine_array_data = {};
              combine_array_data.updatedcolumn = value;
              combine_array_data.columnname = updatedcolumn;
            
               $http.post('api/public/admin/colorInsert',combine_array_data).success(function(result, status, headers, config) {
                        
                          });
} else {

    var combine_array_data = {};
              combine_array_data.updatedcolumn = value;
              combine_array_data.columnname = updatedcolumn;
              combine_array_data.id = id;
 
             
               $http.post('api/public/admin/colorSave',combine_array_data).success(function(result, status, headers, config) {
                         
                          });

}
            
       
                         $state.go('setting.color','',{reload:true});
                                return false;                   

  };



    $scope.removeColors = function(index,id){

  var permission = confirm("Are you sure want to delete this record ? Clicking Ok will delete record permanently.");
                                if (permission == true) {
  
  if(angular.isUndefined(id)){
     $scope.colors.splice(index,1);
  } else {

 var position_data = {};
position_data.table ='color'
position_data.cond ={id:id}
$http.post('api/public/common/DeleteTableRecords',position_data).success(function(result) {
     

  });
     get_all_color();
  }

}
   
}


$scope.openColorPopup = function (page) {

       
        var modalInstance = $modal.open({
                                templateUrl: 'views/setting/'+page,
                                scope : $scope,
                            });

        modalInstance.result.then(function (selectedItem) {
            $scope.selected = selectedItem;
        }, function () {
            //$log.info('Modal dismissed at: ' + new Date());
        });

        $scope.closePopup = function (cancel)
        {
            modalInstance.dismiss('cancel');
        };
        
        $scope.saveColor=function(colorName)
        {
          
        
            if(colorName == undefined) {

              var data = {"status": "error", "message": "Color should not be blank"}
                      notifyService.notify(data.status, data.message);
                      return false;
            }


            var combine_array_data = {};
              combine_array_data.updatedcolumn = colorName;
              combine_array_data.columnname = "name";
             
               $http.post('api/public/admin/colorInsert',combine_array_data).success(function(result, status, headers, config) {
                        
                          });
            modalInstance.dismiss('cancel');
              get_all_color();
        };
    };
  



}]);

