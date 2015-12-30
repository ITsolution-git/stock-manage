
app.controller('colorListCtrl', ['$scope','$http','$location','$state','$stateParams','fileUpload','AllConstant','AuthService','$filter', function($scope,$http,$location,$state,$stateParams,fileUpload,AllConstant,AuthService,$filter) {
  
AuthService.checksession();


  $http.get('api/public/common/getAllColorData').success(function(result, status, headers, config) {
              $scope.colorData = result.data.records;

      });



$scope.updateColor = function(value,id,updatedcolumn) {


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


$scope.addColor = function(){
                            $scope.colorData.push({ name:''});
                          }


    $scope.removeColor = function(index,id){

  var permission = confirm("Are you sure want to delete this record ? Clicking Ok will delete record permanently.");
                                if (permission == true) {
  
  if(angular.isUndefined(id)){
     $scope.colorData.splice(index,1);
  } else {

 var position_data = {};
position_data.table ='color'
position_data.cond ={id:id}
$http.post('api/public/common/DeleteTableRecords',position_data).success(function(result) {
     

  });


     $scope.colorData.splice(index,1);
  }

}
   
}


  



}]);

