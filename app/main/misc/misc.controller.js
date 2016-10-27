(function () {
    'use strict';

    angular
            .module('app.misc')
            .controller('miscController', miscController);
    /** @ngInject */
    function miscController($q, $mdDialog, $document, $mdSidenav, DTOptionsBuilder, DTColumnBuilder,sessionService,$http,$scope) {
        var vm = this;
        
        $("#ajax_loader").show();

 var company_id = sessionService.get('company_id');

 var misc_list_data = {};
 var condition_obj = {};
 condition_obj['company_id'] =  company_id;
 misc_list_data.cond = angular.copy(condition_obj);



$scope.misc_all_data = [{"id": "approved","name":"Approval / Order Sequence"}, 
                  {"id": "art_ink","name":"Art Ink"},
                  {"id": "boxing_type","name":"Boxing Type"},
                  {"id": "charge_apply","name":"Charge Apply"},
                  {"id": "color_group","name":"Color Group"},
                  {"id": "dtg","name":"Direct to Garment"},
                  {"id": "dtgs","name":"Dir. to Garment Sz."},
                  {"id": "disposition","name":"Disposition"},
                  {"id": "graphic_size","name":"Graphic Size"},
                  {"id": "level","name":"Level"},
                  {"id": "yesno","name":"Yes/No"},
                  {"id": "po_status","name":"PO Status"},
                  {"id": "address_type","name":"Address Type"},
                  {"id": "placement_type","name":"Placement Type"},
                  {"id": "position","name":"Position"},
                  {"id": "production","name":"Production"},
                  {"id": "production_sub_type","name":"Production Sub Type"},
                  {"id": "product_type","name":"Production Type"},
                  {"id": "size_group","name":"Size Group"},
                  {"id": "staff_type","name":"Staff Type"},
                  {"id": "status","name":"Status"},
                  {"id": "symbol_description","name":"Symbol Description"},
                  {"id": "terms","name":"Terms"},
                  {"id": "shipping_method","name":"Shipping Method"},
                  {"id": "time_off_type","name":"Time off Type"},
                  {"id": "po_type","name":"PO Type"},
                  {"id": "art_filter","name":"Art Filter"},
                  {"id": "purchasing_filter","name":"Purchasing Filter"},
                  {"id": "shipping_filter","name":"Shipping Filter"},
                  {"id": "production_filter","name":"Production Filter"},
                  {"id": "production_shipped","name":"Production Shipped"},
                  {"id": "production_paid","name":"Production Paid"},
                  {"id": "production_on_press","name":"Production On Press"},
                  {"id": "finishing","name":"Finishing"}];


 //console.log($scope.misc_all_data);return false;


$scope.selectedIndex = 'address_type'; // Whatever the default selected index is, use -1 for no selection

  $scope.itemClicked = function ($string) {
    $scope.selectedIndex = $string;
  };



 $http.post('api/public/common/getAllMiscData',misc_list_data).success(function(result, status, headers, config) {
             
              $scope.miscData = result.data.records;
             // $scope.pagination = AllConstant.pagination;
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
    }
})();
