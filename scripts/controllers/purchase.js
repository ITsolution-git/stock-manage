app.controller('PurchaseListCtrl', ['$scope', '$rootScope', '$http','$state','$stateParams', 'AuthService','$filter',function($scope,$rootScope,$http,$state,$stateParams,AuthService,$filter) {
                          AuthService.AccessService('BC');
                         // console.log($rootScope.company_profile.company_id);
                          $scope.Maintype = $stateParams.id;
                          $("#ajax_loader").show();
                           var type = {};
                          $scope.CurrentController=$state.current.controller;
                          type.type = $stateParams.id;
                          $scope.Maintype = $scope.Maintype.toLowerCase();
                          type.company_id = $rootScope.company_profile.company_id;
                          $http.post('api/public/purchase/ListPurchase',type ).success(function(Listdata) 
                          		  {
                                    	$scope.listPurchase = Listdata.data.records;
                                        $("#ajax_loader").hide();

                                      var init;

                                      $scope.searchKeywords = '';
                                      $scope.filteredListPurchase = [];
                                      $scope.row = '';
                                      $scope.select = function (page) {
                                        var end, start;
                                        start = (page - 1) * $scope.numPerPage;
                                        end = start + $scope.numPerPage;
                                        return $scope.currentPageListPurchase = $scope.filteredListPurchase.slice(start, end);
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
                                        $scope.filteredListPurchase = $filter('filter')($scope.listPurchase, $scope.searchKeywords);
                                        return $scope.onFilterChange();
                                      };
                                      $scope.order = function (rowName) {
                                        if ($scope.row === rowName) {
                                            return;
                                        }
                                        $scope.row = rowName;
                                        $scope.filteredListPurchase = $filter('orderBy')($scope.listPurchase, rowName);
                                        return $scope.onOrderChange();
                                      };
                                      $scope.numPerPageOpt = [10, 20, 50, 100];
                                      $scope.numPerPage = 10;
                                      $scope.currentPage = 1;
                                      $scope.currentPageListPurchase = [];

                                      init = function () {
                                        $scope.search();

                                        return $scope.select($scope.currentPage);
                                      };
                                      return init();


                                  });
}]);
app.controller('PurchasePOCtrl', ['$scope','$rootScope','$sce',  '$http','$modal','$state','$stateParams','$filter','notifyService', 'AuthService',function($scope,$rootScope,$sce,$http,$modal,$state,$stateParams,$filter,notifyService,AuthService) {
                           AuthService.AccessService('BC');
                           $scope.company_id = $rootScope.company_profile.company_id;
                           var modalInstance='';
                           var AJloader = $("#ajax_loader");
                           $scope.po_id = $stateParams.id;
                           if($scope.po_id=='' || $scope.po_id==0)
                           {
                           		$state.go('purchase.list',{"id":1});
                           		return false;
                           }
							$scope.getNumber = function(num) {
							    return new Array(num);   
							}

                           
                           GetPodata($scope.po_id);
                           function GetPodata(po_id)
                           {
                           		AJloader.show();
                           		$http.get('api/public/purchase/GetPodata/'+po_id+'/'+$scope.company_id ).success(function(PoData) 
                          		  {
                          		  		  if(PoData.data.success==0)
                          		  		  {
                          		  		  	$state.go('purchase.list',{"id":$scope.po_id});
                           					return false;
                          		  		  }
                                          $scope.ArrPo = PoData.data.records.po[0];
                                          $scope.ArrPoLine = PoData.data.records.poline;
                                          $scope.ArrUnassign = PoData.data.records.unassign_order;
                                          $scope.ordered = PoData.data.records.order_total[0];
                                          $scope.received = PoData.data.records.received_total[0].received;
                                          $scope.received_line = PoData.data.records.received_line;
                                          $scope.currentPOUrl = $sce.trustAsResourceUrl(PoData.data.records.po[0].url);
                                          $scope.order_id = PoData.data.records.order_id;
                                          $scope.list_vendors = PoData.data.records.list_vendors
                                          getNotesDetail(po_id);
                                          get_contacts_vendors($scope.ArrPo.vendor_id);
                                          AJloader.hide();
                                        // console.log($scope.ordered);
                                  });
                       		}
                       		
                          function get_contacts_vendors(vendor_id)
                          {
                              var ArrNotes = {};
                              ArrNotes.table ='vendor_contacts'
                              ArrNotes.cond ={vendor_id: vendor_id}
                              $http.post('api/public/common/GetTableRecords',ArrNotes).success(function(result) {
                                     $scope.AllContacts =result.data.records;
                               });
                          }
                       	  function short_over(poline_id)
                          {
                          		$http.get('api/public/purchase/short_over/'+poline_id).success(function(result) {
                          				var data = {"status": "success", "message": "Data Updated successfully"}
                    				    notifyService.notify(data.status, data.message);
	                             });
                          }
                          function getNotesDetail()
                          {
                            $http.post('api/public/purchase/getPurchaseNote/'+$scope.po_id).success(function(result) {
                                if(result.data.success == '1') 
                                {
                                    $scope.AllNotesData =result.data.records;
                                } 
                              });
                          }
                       	  $scope.items = 'item1';
                          $scope.addNotes = function () {
	                            $scope.edit='add';
	                            var modalInstance = $modal.open({
	                              templateUrl: 'views/front/purchase/note.html',
	                              scope : $scope,
	                              
	                            });
	                             modalInstance.result.then(function (selectedItem) {
	                                 $scope.selected = selectedItem;
	                             }, function () {
	                              //$log.info('Modal dismissed at: ' + new Date());
	                             });
	                              $scope.ClosePopup = function (cancel)
	                             {
	                               	modalInstance.dismiss('cancel');
	                             };
	                             $scope.Savenotes=function(notes)
	                            {
	                                var Note_data = {};
	                                Note_data.data = notes;
	                                Note_data.data.po_id = $scope.po_id;
	                                Note_data.table = 'purchase_notes';
	                                Note_data.data.note_date = $filter('date')(new Date(), 'yyyy-MM-dd');;

	                                $http.post('api/public/common/InsertRecords',Note_data).success(function(Listdata) {
	                                      getNotesDetail($scope.po_id);
	                                });
	                                modalInstance.dismiss('cancel');
	                            };
                          }
                         
 						 $scope.removeNotes = function(index,id){
                            var permission = confirm("Are you sure to delete this record ?");
                            if (permission == true) {
   						 	              var Note_data = {};
  	                          Note_data.cond = {id:id};
  	                          Note_data.table = 'purchase_notes';
                                $http.post('api/public/common/DeleteTableRecords',Note_data).success(function(Listdata) {
                                  		var data = {"status": "error", "message": "1 Note removed"}
                                          notifyService.notify(data.status, data.message);
                                });
                                $scope.AllNotesData.splice(index,1);
                            }
                          }

                           $scope.ChangeOrderStatus = function(poline_id,value,purchase_id ){
                           //	console.log(value);
                            	  $http.get('api/public/purchase/ChangeOrderStatus/'+poline_id+'/'+value+'/'+purchase_id ).success(function(PoData) 
                          		  {
                                       GetPodata($scope.po_id );
                                       var data = {"status": "success", "message": "Orderline status change."}
                    				   notifyService.notify(data.status, data.message); 
                                  });
                          }
                          $scope.EditOrderLine = function(Poline_data){
                          			
                            	  $http.post('api/public/purchase/EditOrderLine',Poline_data ).success(function(PoData) 
                          		  {
                                       GetPodata($scope.po_id );
                                       var data = {"status": "success", "message": "Data Updated successfully"}
                    				   notifyService.notify(data.status, data.message); 
                                  });
                          }
                          $scope.shipttoblock = function($event,id){

                          		 var Arrshift = {};
	                              
	                              Arrshift.data = $event.target.value;
	                              Arrshift.po_id = $scope.po_id;
								//console.log(Arrshift); return false;

                          		$http.post('api/public/purchase/Update_shiftlock',Arrshift ).success(function() 
                          		  {
                                       GetPodata($scope.po_id );
                                       var data = {"status": "success", "message": "Data Updated successfully"}
                    				   notifyService.notify(data.status, data.message);  
                                  });
                          }
                          $scope.Receive_order = function(data){

                          		 $http.post('api/public/purchase/Receive_order',data ).success(function() 
                          		  {
                                       GetPodata($scope.po_id ); 
                                       var data = {"status": "success", "message": "One OrderLine Received"}
                                       notifyService.notify(data.status, data.message);  
                                  });
                          }

                          $scope.RemoveReceiveLine = function(id,poline_id){
                          	  var RecLine = {};
	                          RecLine.cond = {id:id};
	                          RecLine.table = 'purchase_received';
                              $http.post('api/public/common/DeleteTableRecords',RecLine).success(function(Listdata) {
                              		short_over(poline_id );
                               		GetPodata($scope.po_id );
                               		var data = {"status": "error", "message": "Data removed"}
                                       notifyService.notify(data.status, data.message);   
                              });
                          }

                          $scope.updateReceiveData = function($event,id,poline_id){
                          		  var Receive_data = {};
	                              Receive_data.table ='purchase_received'
	                              Receive_data.data ={qnty_received:$event.target.value}
	                              Receive_data.cond ={id:id}
	                              $http.post('api/public/common/UpdateTableRecords',Receive_data).success(function(result) {
	                              		short_over(poline_id ); 
                                  		GetPodata($scope.po_id ); 
                                  		var data = {"status": "success", "message": "One OrderLine Received"}
                                        notifyService.notify(data.status, data.message);  
                                });
                          }
                           $scope.Updatenotes = function($event,note_id){
                          		  var Receive_data = {};
	                              Receive_data.table ='purchase_notes'
	                              Receive_data.data ={note:$event.target.value}
	                              Receive_data.cond ={id:note_id}
	                              $http.post('api/public/common/UpdateTableRecords',Receive_data).success(function(result) {

                                  		getNotesDetail($scope.po_id);
                                });
                          }
                          $scope.vendorshipcharte = function($event){
                          		  var Receive_data = {};
	                              Receive_data.table ='purchase_order';
	                              Receive_data.data ={vendor_charge:$event.target.value}
	                              Receive_data.cond ={ po_id :$scope.po_id}
	                              $http.post('api/public/common/UpdateTableRecords',Receive_data).success(function(result) {
	                              		GetPodata($scope.po_id);
                                  		//getNotesDetail(po_id);
                                });
                          }
                          $scope.UpdateField_detail = function($event,id){
                          		  var Receive_data = {};
                          		  Receive_data.table ='purchase_detail';
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
                          $scope.UpdateField_order = function($event){
                          		  var Receive_data = {};
                          		 // Receive_data.data = [];
                              // / console.log($event.target.name);
                                if($event.target.name=='vendor_id')
                                {
                                  get_contacts_vendors($event.target.value);
                                  $("#vendor_contact_id").focus();
                                }

                          		  $scope.name_filed = $event.target.name;
                          		  var obj = {};
                          		  obj[$scope.name_filed] =  $event.target.value;
                          		  Receive_data.table ='purchase_order';
	                              Receive_data.data = angular.copy(obj);
	                              Receive_data.cond ={ po_id :$scope.po_id}
	                              //console.log(Receive_data);
	                              $http.post('api/public/common/UpdateTableRecords',Receive_data).success(function(result) {
	                              		var data = {"status": "success", "message": "Data Updated successfully"}
                                        notifyService.notify(data.status, data.message);
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
app.controller('PurchaseSGCtrl', ['$scope',  '$http','$state','$stateParams', 'AuthService',function($scope,$http,$state,$stateParams,AuthService) {
                          AuthService.AccessService('BC');

                           $scope.order_id = $stateParams.id;

}]);
app.controller('PurchaseCPCtrl', ['$scope','$sce','$rootScope',  '$http','$modal','$state','$stateParams','$filter','notifyService', 'AuthService',function($scope,$sce, 
	$rootScope ,$http,$modal,$state,$stateParams,$filter,notifyService,AuthService) {

						   AuthService.AccessService('BC');
						   $scope.company_id = $rootScope.company_profile.company_id;
                           var modalInstance='';
                           var AJloader = $("#ajax_loader");
                           $scope.po_id = $stateParams.id;
                           $scope.highlight_placement ='0';
                           if($scope.po_id=='' || $scope.po_id==0)
                           {
                           		$state.go('purchase.list',{"id":1});
                           		return false;
                           }
                           $scope.getNumber = function(num) {
							    return new Array(num);   
							}

                          GetScreenData($scope.po_id);
						  function GetScreenData(po_id)
                           {
                           		AJloader.show();
                           		$http.get('api/public/purchase/GetScreendata/'+po_id+'/'+$scope.company_id ).success(function(PoData) 
                          		  {
                          		  		  if(PoData.data.success==0)
                          		  		  {
                          		  		  	  $state.go('purchase.list',{"id":'cp'});
                           					      return false;
                          		  		  }
                                    
                                          $scope.ArrPo = PoData.data.records.screen_data[0];
                                          $scope.ArrPoLine = PoData.data.records.screen_line;
                                          $scope.ordered = PoData.data.records.order_total[0];
                                          $scope.order_id = PoData.data.records.order_id;
                                          $scope.list_vendors = PoData.data.records.list_vendors;
                                          $scope.order_line_data = PoData.data.records.order_line_data_new;
                                          $scope.placements = PoData.data.records.placements;

                                          //console.log($scope.list_vendors);
                                          get_contacts_vendors($scope.ArrPo.vendor_id);
                                          AJloader.hide();
                                  });
                       		}

                           function get_contacts_vendors(vendor_id)
                          {
                              var ArrNotes = {};
                              ArrNotes.table ='vendor_contacts'
                              ArrNotes.cond ={vendor_id: vendor_id}
                              $http.post('api/public/common/GetTableRecords',ArrNotes).success(function(result) {
                                     $scope.AllContacts =result.data.records;
                               });
                          }

						  $scope.UpdateField_detail = function($event,id){
                          		  var Receive_data = {};
                          		  Receive_data.table ='purchase_order_line';
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
                          $scope.UpdateField_order = function($event){
                          		  var Receive_data = {};
                          		 // Receive_data.data = [];
                               if($event.target.name=='vendor_id')
                                {
                                  get_contacts_vendors($event.target.value);
                                  $("#vendor_contact_id").focus();
                                }
                          		  $scope.name_filed = $event.target.name;
                          		  var obj = {};
                          		  obj[$scope.name_filed] =  $event.target.value;
                          		  Receive_data.table ='purchase_order';
	                              Receive_data.data = angular.copy(obj);
	                              Receive_data.cond ={ po_id :$scope.po_id}
	                              //console.log(Receive_data);
	                              $http.post('api/public/common/UpdateTableRecords',Receive_data).success(function(result) {
	                              		var data = {"status": "success", "message": "Data Updated successfully"}
                                        notifyService.notify(data.status, data.message);
                                });
                          }
                       $scope.EditScreenLine = function(Poline_data){
                          			
                            	  $http.post('api/public/purchase/EditScreenLine',Poline_data ).success(function(PoData) 
                          		  {
                                       GetScreenData($scope.po_id );
                                       var data = {"status": "success", "message": "Data Updated successfully"}
                    				           notifyService.notify(data.status, data.message); 
                                  });
                          }
                        $scope.vendorshipcharge = function($event){
                          		  var Receive_data = {};
	                              Receive_data.table ='purchase_order';
	                              Receive_data.data ={vendor_charge:$event.target.value}
	                              Receive_data.cond ={ po_id :$scope.po_id}
	                              $http.post('api/public/common/UpdateTableRecords',Receive_data).success(function(result) {
	                              		GetScreenData($scope.po_id);
                                  		//getNotesDetail(po_id);
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
                        $scope.placement_artwork=function(art_id,placemet_id,key)
                        {
                           $http.get('api/public/art/art_worklist_listing/'+art_id+'/'+$scope.company_id).success(function(RetArray) {             
                               $scope.art_worklist = RetArray.data.art_worklist;
                               $scope.placemet_id = placemet_id; 
                              // console.log($scope.placemet_id ) ; 
                               $scope.art_popup(key); 

                            });     
                        }
                         $scope.art_popup=function(key)
                        {
                           var modalInstanceEdit = $modal.open({
                                    templateUrl: 'views/front/purchase/art_popup.html',
                                    scope : $scope,
                                    size : 'md',
                                    windowClass: 'addColorModal'
                                   // controller:'ArtJobCtrl'
                                  });
                           
                                  modalInstanceEdit.result.then(function (selectedItem) {
                                    $scope.selected = selectedItem;
                                  }, function () {
                                     $("#ajax_loader").hide();
                                  });  
                               $scope.ClosePopup = function (cancel)
                               {
                                  modalInstanceEdit.dismiss('cancel');
                               };
                                $scope.save_art_placement = function(name,value,id,table){
                                //console.log(value); return false;
                                var Receive_data = {};
                                Receive_data.table =table;
                                $scope.name_filed = name;
                                var obj = {};
                                obj[$scope.name_filed] =  value;
                                Receive_data.data = angular.copy(obj);
                                
                                Receive_data.cond ={ id :id}
                                $http.post('api/public/common/UpdateTableRecords',Receive_data).success(function(result) {
                                    var data = {"status": "success", "message": result.data.message}
                                        notifyService.notify(data.status, data.message); 
                                        GetScreenData($scope.po_id);
                                        $scope.ClosePopup();
                                        console.log(key);
                                        $scope.highlight_placement =key;

                                 });
                                }

                        }

                         $scope.save_art_placement_data = function($event,id,table){
                                //console.log(value); return false;
                                var Receive_data = {};
                                Receive_data.table =table;
                                $scope.name_filed = $event.target.name;
                                var obj = {};
                                obj[$scope.name_filed] =  $event.target.value;
                                Receive_data.data = angular.copy(obj);
                                
                                Receive_data.cond ={ id :id}
                                $http.post('api/public/common/UpdateTableRecords',Receive_data).success(function(result) {
                                    var data = {"status": "success", "message": result.data.message}
                                        notifyService.notify(data.status, data.message); 
                                 });
                                }


                        


}]);
app.controller('PurchaseCECtrl', ['$scope',  '$http','$state','$stateParams', 'AuthService',function($scope,$http,$state,$stateParams,AuthService) {
                          AuthService.AccessService('BC');
                           $scope.order_id = $stateParams.id;

}]);