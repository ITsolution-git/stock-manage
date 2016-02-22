app.controller('ArtListCtrl', ['$scope',  '$http','$state','$stateParams','$rootScope', 'AuthService','$filter',function($scope,$http,$state,$stateParams,$rootScope,AuthService,$filter) {
	                      $("#ajax_loader").show();
                          AuthService.AccessService('BC');
                          $scope.CurrentController=$state.current.controller;
                          $scope.art_id = $stateParams.art_id;
                          $scope.company_id = $rootScope.company_profile.company_id;
                          

                          $http.get('api/public/art/listing/'+$scope.company_id).success(function(RetArray) {
	                          	if(RetArray.data.success=='1')
                          		{
                          			$scope.arts = RetArray.data.records;
                          			 $("#ajax_loader").hide();

                                  var init;

                                  $scope.searchKeywords = '';
                                  $scope.filteredArts = [];
                                  $scope.row = '';
                                  $scope.select = function (page) {
                                    var end, start;
                                    start = (page - 1) * $scope.numPerPage;
                                    end = start + $scope.numPerPage;
                                    return $scope.currentPageArts = $scope.filteredArts.slice(start, end);
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
                                    $scope.filteredArts = $filter('filter')($scope.arts, $scope.searchKeywords);
                                    return $scope.onFilterChange();
                                  };
                                  $scope.order = function (rowName) {
                                    if ($scope.row === rowName) {
                                        return;
                                    }
                                    $scope.row = rowName;
                                    $scope.filteredArts = $filter('orderBy')($scope.arts, rowName);
                                    return $scope.onOrderChange();
                                  };
                                  $scope.numPerPageOpt = [10, 20, 50, 100];
                                  $scope.numPerPage = 10;
                                  $scope.currentPage = 1;
                                  $scope.currentPageArts = [];

                                  init = function () {
                                    $scope.search();

                                    return $scope.select($scope.currentPage);
                                  };
                                  return init();
                              	}
                            });

                          	$http.get('api/public/art/ScreenListing/'+$scope.company_id).success(function(RetArray) {

                          			$("#ajax_loader").hide();
                          			$scope.screen_listing = RetArray.data;

                              	if(RetArray.data.success=='2')
                          		{
                          			$("#ajax_loader").hide();
                          			var data = {"status": "success", "message": RetArray.data.message}
                                    notifyService.notify(data.status, data.message);
                                    window.location.reload();
                          		}
                            });


}]);
app.controller('ArtJobCtrl', ['$scope',  '$http','$state','$stateParams','$rootScope', 'AuthService','notifyService','$modal','$q',function($scope,$http,$state,$stateParams,$rootScope,AuthService,notifyService,$modal,$q) {
						  $("#ajax_loader").hide();
                          AuthService.AccessService('BC');
                          $scope.CurrentController=$state.current.controller;
                          $scope.company_id = $rootScope.company_profile.company_id;
                          $scope.art_id = $stateParams.id;
                          

                          Get_artDetail();
                          function Get_artDetail()
                          {
                          	
                          	$http.get('api/public/art/Art_detail/'+$scope.art_id+'/'+$scope.company_id).success(function(RetArray) {

                          			$("#ajax_loader").hide();
                          			$scope.art_position = RetArray.data.records.art_position;
                          			$scope.art_orderline = RetArray.data.records.art_orderline;
                          			$scope.artjobscreen_list = RetArray.data.records.artjobscreen_list;
                          			$scope.graphic_size = RetArray.data.records.graphic_size;
                          			$scope.artjobgroup_list = RetArray.data.records.artjobgroup_list;
                          			$scope.art_worklist = RetArray.data.records.art_worklist;
                          			$scope.wp_position = RetArray.data.records.wp_position;
                          			$scope.art_approval = RetArray.data.records.art_approval;
                          			$scope.mokup_display_image = $scope.art_position[0].mokup_display_image;

                              	if(RetArray.data.success=='2')
                          		{
                          			$("#ajax_loader").hide();
                          			var data = {"status": "success", "message": RetArray.data.message}
                                    notifyService.notify(data.status, data.message);
                                    window.location.reload();
                          		}
                            });
						}



                           $scope.getNumber = function(num) {
							    return new Array(num);   
							}

						  $scope.UpdateField_field = function($event,id,table,fun_redirect){
                          		  var Receive_data = {};
                          		  Receive_data.table =table;
                          		  $scope.name_filed = $event.target.name;
                          		  var obj = {};
                          		  obj[$scope.name_filed] =  $event.target.value;
                          		  Receive_data.data = angular.copy(obj);
                          		  
	                              Receive_data.cond ={ id :id}
	                              $http.post('api/public/common/UpdateTableRecords',Receive_data).success(function(result) {
	                              		var data = {"status": "success", "message": "Data Updated successfully"}
                                        notifyService.notify(data.status, data.message); 
                                        //alert(fun_redirect);
                                        if(fun_redirect =='get_groupdata')
                                        {
                                        	$scope.get_groupdata();
                                        }
                                });
                          }
                          
                           $scope.UpdateField_clientnote = function($event,id,table){
                          		  var Receive_data = {};
                          		  Receive_data.table =table;
                          		  $scope.name_filed = $event.target.name;
                          		  var obj = {};
                          		  obj[$scope.name_filed] =  $event.target.value;
                          		  Receive_data.data = angular.copy(obj);
                          		  
	                              Receive_data.cond ={ art_id :id}
	                              $http.post('api/public/common/UpdateTableRecords',Receive_data).success(function(result) {
	                              		var data = {"status": "success", "message": "Data Updated successfully"}
                                        notifyService.notify(data.status, data.message); 
                                });
                          }


                          $scope.get_groupdata = function(){
                          	$http.get('api/public/art/artjobgroup_list/'+$scope.art_id+'/'+$scope.company_id).success(function(RetArray) {
                          		$scope.artjobgroup_list = RetArray.data.records;
                          	});
                          }
                          $scope.Asign_group_order = function(id){
                          		  var Receive_data = {};
                          		  Receive_data.table ='purchase_detail';
                          		  $scope.name_filed = 'art_group';
                          		  var obj = {};
                          		  obj[$scope.name_filed] = $('#art_group_'+id).val();;
                          		  Receive_data.data = angular.copy(obj);
                          		  
	                              Receive_data.cond ={ orderline_id :id}
	                              $http.post('api/public/common/UpdateTableRecords',Receive_data).success(function(result) {
	                              		var data = {"status": "success", "message": "Data Updated successfully"}
                                        notifyService.notify(data.status, data.message); 
                                         Get_artDetail();
                                });
                          }


                           $scope.change_color = function(name,value,id,table){
                           	//console.log(value); return false;
                          		  var Receive_data = {};
                          		  Receive_data.table =table;
                          		  $scope.name_filed = name;
                          		  var obj = {};
                          		  obj[$scope.name_filed] =  value;
                          		  Receive_data.data = angular.copy(obj);
                          		  
	                              Receive_data.cond ={ id :id}
	                              $http.post('api/public/common/UpdateTableRecords',Receive_data).success(function(result) {
	                              		var data = {"status": "success", "message": "Data Updated successfully"}
                                        notifyService.notify(data.status, data.message); 
                                });
                          }




                           $scope.create_artworkproof = function(orderline_id){
                           	
                           	$http.get('api/public/art/Insert_artworkproof/'+orderline_id).success(function(RetArray) {
                           		$scope.artworkproof_popup(RetArray.data.records);
                     	    });

                           }
                          $scope.artworkproof_popup = function(wp_id){

                          	$http.get('api/public/art/artworkproof_data/'+wp_id+'/'+$scope.company_id).success(function(RetArray) {
	                          	if(RetArray.data.success=='1')
                          		{
                          			$scope.workproof = RetArray.data.records.art_workproof[0];
                          			$scope.get_artworkproof_placement = RetArray.data.records.get_artworkproof_placement;
                          			$scope.Artworkproof_data();
		                            
		                        }
		                        else
		                        {
		                        	$("#ajax_loader").hide();
                          			var data = {"status": "info", "message": RetArray.data.message}
                                    notifyService.notify(data.status, data.message);
		                        }
                     	    });
                        }
                        $scope.Artworkproof_data= function () {
			                            var modalInstanceEdit = $modal.open({
			                              templateUrl: 'views/front/art/artjob/artwork_proof.html',
			                              scope : $scope,
			                              size : 'md'

			                             // controller:'ArtJobCtrl'
			                            });
			                            modalInstanceEdit.result.then(function (selectedItem) {
			                              $scope.selected = selectedItem;
			                            }, function () {
			                            });
			                            $scope.ClosePopup = function (cancel)
			                            {
			                               modalInstanceEdit.dismiss('cancel');
			                            };
			                            $scope.SaveArtWorkProof=function(Receive_data)
			                            {
			                            	Receive_data.art_id = $scope.art_id;
			                            	$http.post('api/public/art/SaveArtWorkProof',Receive_data).success(function(result) 
			                            	{
			                            		
					                        	var data = {"status": "success", "message": result.data.message}
				                                notifyService.notify(data.status, data.message); 
				                                $scope.ClosePopup('close');
				                                Get_artDetail();
				                                //$scope.artworkproof_popup(Receive_data.wp_id);
				                            });
			                            }
		                            }

		                $scope.color_popup = function(screen_id){
		                		$("#ajax_loader").show();
		                		$http.get('api/public/art/screen_colorpopup/'+screen_id+'/'+$scope.company_id).success(function(RetArray) {
		                			$scope.screen_colorpopup = RetArray.data.records.screen_colorpopup;
		                			$scope.screen_allcolors = RetArray.data.records.allcolors;


                      				$scope.simulateQuery = false;
								    $scope.isDisabled    = false;
								    // list of `state` value/display objects
								    $scope.states        = loadAll();
								    //console.log( $scope.states )
								    $scope.querySearch   = querySearch;



                          			$scope.color_popup_open();
                          		  });

                        }


		                $scope.color_popup_open = function(){
		                	var modalInstanceEdit = $modal.open({
			                              templateUrl: 'views/front/art/artjob/add_color.html',
			                              scope : $scope,
			                              size : 'md',
			                              windowClass: 'addColorModal'
			                             // controller:'ArtJobCtrl'
			                            });
		                				$("#ajax_loader").hide();
			                            modalInstanceEdit.result.then(function (selectedItem) {
			                              $scope.selected = selectedItem;
			                            }, function () {
			                            });
			                            $scope.CloseColorPopup = function (cancel)
			                            {
			                            	deferred = $q.defer();
									        setTimeout(function () { modalInstanceEdit.dismiss('cancel'); }, Math.random() * 500, false);
									        return deferred.promise;

			                               
			                            };
			                            $scope.add_color_line = function (screen_id){
				                        		var colors_insert = {};
				                                colors_insert.data = {screen_id :screen_id }
				                                colors_insert.table ='artjob_screencolors'
				                                $http.post('api/public/common/InsertRecords',colors_insert).success(function(result) {
				                                	 $scope.CloseColorPopup();
				                                	$scope.color_popup(screen_id);
				                                });
				                        }
				                        $scope.add_color_logo = function (image_array,field,table,image_name,image_path,cond,value)
				                        {
				                         	$scope.SaveImage(image_array,field,table,image_name,image_path,cond,value);
				                         	$scope.CloseColorPopup();
				                         	//setTimeout($scope.color_popup(value), 500);
				                     	}
		                	
		                }
		                $scope.create_group = function(table) {

		                	 var Address_data = {};
                                Address_data.data = {art_id:$scope.art_id};
                                Address_data.table =table
                                
                                $http.post('api/public/common/InsertRecords',Address_data).success(function(result) {
                                    if(result.data.success == '1') 
                                    {
                                       Get_artDetail();
                                    }
                                    else
                                    {
                                        $("#ajax_loader").hide();
	                          			var data = {"status": "error", "message": "Something wrong please try again."}
	                                    notifyService.notify(data.status, data.message);
                                    }
                                });
						}
		                $scope.create_screen = function() {

		                	 var Address_data = {};
                                Address_data.data = {art_id:$scope.art_id};
                                
                                $http.post('api/public/art/create_screen',Address_data).success(function(result) {
                                    if(result.data.success == '1') 
                                    {
                                       Get_artDetail();
                                    }
                                    else
                                    {
                                        $("#ajax_loader").hide();
	                          			var data = {"status": "error", "message": result.data.message}
	                                    notifyService.notify(data.status, data.message);
                                    }
                                });
							}
  						$scope.UpdateField_orderscreen = function(data,id,table) {
								var Receive_data = {};
                          		  Receive_data.table =table;

                          		  Receive_data.data = data;
                          		  
	                              Receive_data.cond ={ id :id}
	                              $http.post('api/public/art/update_orderScreen',Receive_data).success(function(result) {
	                              		var data = {"status": "success", "message": "Data Updated successfully"}
                                        notifyService.notify(data.status, data.message); 
                                });
						}
						$scope.remove_data = function (id,table){
							 	var permission = confirm("Are you sure to delete this record ?");
                                if (permission == true) {
	                        		var delete_data = {};
	                                delete_data.cond = {id :id }
	                                delete_data.table =table

	                                $http.post('api/public/common/DeleteTableRecords',delete_data).success(function(result) {
	                                	jQuery("#"+id).remove();
	                                	var data = {"status": "success", "message": "Record Deleted successfully"}
	                                    notifyService.notify(data.status, data.message); 
	                                });
                            	}
                        }
                        $scope.remove_screen = function (id){
							 	var permission = confirm("Are you sure to delete this record ?");
                                if (permission == true) {
                                	var delete_data = {};
	                                delete_data.cond = {id :id }

	                                $http.post('api/public/art/DeleteScreenRecord',delete_data).success(function(result) {
	                                	jQuery("#"+id).remove();
	                                	var data = {"status": "success", "message": "Record Deleted successfully"}
	                                    notifyService.notify(data.status, data.message); 
	                                });
                            	}
                        }
                        
                      


					      function querySearch (query) {
						      var results = query ? $scope.states.filter( createFilterFor(query) ) : $scope.states,
						          deferred;

						      if ($scope.simulateQuery) {
						        deferred = $q.defer();
						        $timeout(function () { deferred.resolve( results ); }, Math.random() * 1000, false);
						        return deferred.promise;
						      } else {
						        return results;
						      }
						    }

						    /**
						     * Build `states` list of key/value pairs
						     */
						    function loadAll() {
						      var allStates = $scope.screen_allcolors;
						      return allStates;
						    }
						     function createFilterFor(query) {
							      var lowercaseQuery = angular.lowercase(query);
							     // console.log(lowercaseQuery);
							      return function filterFn(state) {
							        return (state.name.indexOf(lowercaseQuery) === 0);
							      };
							    }
                      
                       $scope.SaveImage = function (image_array,field,table,image_name,image_path,cond,value){
                                	
                                	var Image_data = {};
	                                Image_data.image_array = image_array;
	                                Image_data.field = field;
	                                Image_data.table = table;
	                                Image_data.image_name = image_name;
	                                Image_data.image_path = image_path;
	                                Image_data.cond = cond;
	                                Image_data.value = value;

	                                $http.post('api/public/common/SaveImage',Image_data).success(function(result) {
	                                	
	                                	var data = {"status": "success", "message": "Image Uploaded Successfully"}
	                                    notifyService.notify(data.status, data.message); 
	                                     $scope.art_worklist_listing();
	                                });
                                }
					 
					 $scope.art_worklist_listing = function (){
							 $http.get('api/public/art/art_worklist_listing/'+$scope.art_id+'/'+$scope.company_id).success(function(RetArray) {							
                    		 $scope.art_worklist = RetArray.data.art_worklist;
                    		 $scope.art_position = RetArray.data.art_position;
                    		 $scope.mokup_display_image = $scope.art_position[0].mokup_display_image;
                    		  });           
                     }
                      $scope.swipe_image = function (image)
                          {
                          	$scope.art_work_image = image;
                          	var modalInstanceEdit = $modal.open({
			                              templateUrl: 'views/front/art/artjob/art_image.html',
			                              scope : $scope,
			                              size : 'md',
			                             // controller:'ArtJobCtrl'
			                            });
			                            modalInstanceEdit.result.then(function (selectedItem) {
			                              $scope.selected = selectedItem;
			                            }, function () {
			                            });
                          	
                          }


                    $scope.UpdateDate=function($event,table,cond,value)
                    {
                      var Array_data = {};
                      Array_data.table =table;
                      Array_data.field =$event.target.name;
                      Array_data.date = $event.target.value
                      Array_data.cond =cond
                      Array_data.value =value;

                      $http.post('api/public/common/updatedate',Array_data).success(function(result) {
                          		 var data = {"status": "success", "message": "Data Updated successfully"}
                                 notifyService.notify(data.status, data.message); 
                        });
                    }

}]);

app.controller('ArtScreenCtrl', ['$scope',  '$http','$state','$stateParams','$rootScope', 'AuthService','notifyService','$modal',function($scope,$http,$state,$stateParams,$rootScope,AuthService,notifyService,$modal) {

						  AuthService.AccessService('BC');
                          $scope.CurrentController=$state.current.controller;
                          $scope.company_id = $rootScope.company_profile.company_id;
                          $scope.screen_id = $stateParams.id;

                          screen_colorpopup();
                          function screen_colorpopup() {
                          $http.get('api/public/art/screen_colorpopup/'+$scope.screen_id+'/'+$scope.company_id).success(function(RetArray) {
		                			$scope.screen_detail = RetArray.data.records.screen_colorpopup;
		                			$scope.graphic_size_all=  RetArray.data.records.graphic_size;
		                			$scope.screen_arts  = RetArray.data.records.screen_arts;
		                			$scope.art_approval = RetArray.data.records.art_approval;
		                			$scope.screen_garments = RetArray.data.records.screen_garments;
                          		  });
                      	  }
                      	  $scope.UpdateField_field = function($event,id,table){
                          		  var Receive_data = {};
                          		  Receive_data.table =table;
                          		  $scope.name_filed = $event.target.name;
                          		  var obj = {};
                          		  obj[$scope.name_filed] =  $event.target.value;
                          		  Receive_data.data = angular.copy(obj);
                          		  
	                              Receive_data.cond ={ id :id}
	                              $http.post('api/public/common/UpdateTableRecords',Receive_data).success(function(result) {
	                              		var data = {"status": "success", "message": "Data Updated successfully"}
                                        notifyService.notify(data.status, data.message); 
                                });
                          }
                          $scope.swipe_image = function (image)
                          {
                          	$scope.art_work_image = image;
                          }
                          $scope.UpdateDate=function($event,table,cond,value)
		                    {
		                      var Array_data = {};
		                      Array_data.table =table;
		                      Array_data.field =$event.target.name;
		                      Array_data.date = $event.target.value
		                      Array_data.cond =cond
		                      Array_data.value =value;

		                      $http.post('api/public/common/updatedate',Array_data).success(function(result) {
		                            var data = {"status": "success", "message": "Data Updated successfully"}
                               		notifyService.notify(data.status, data.message); 
		                        });
		                    }


	}]);
