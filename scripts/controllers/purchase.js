app.controller('PurchaseListCtrl', ['$scope', '$rootScope', '$http','$state','$stateParams', 'AuthService',function($scope,$rootScope,$http,$state,$stateParams,AuthService) {
                          AuthService.AccessService('BC');

                          $scope.Maintype = $stateParams.id;
                          $("#ajax_loader").show();
                           var type = {};
                          $scope.CurrentController=$state.current.controller;
                          type.type = $stateParams.id;
                          type.company_id = $rootScope.company_profile.company_id;
                          $http.post('api/public/purchase/ListPurchase',type ).success(function(Listdata) 
                          		  {
                                    	$scope.ListPurchase = Listdata.data;
                                        $("#ajax_loader").hide();
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
                                          getNotesDetail(po_id);
                                          AJloader.hide();
                                        // console.log($scope.ordered);
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
                            var ArrNotes = {};
                            ArrNotes.table ='purchase_notes'
                            ArrNotes.cond ={po_id: $scope.po_id}
                            $http.post('api/public/common/GetTableRecords',ArrNotes).success(function(result) {
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
 						 	  var Note_data = {};
	                          Note_data.cond = {id:id};
	                          Note_data.table = 'purchase_notes';
                              $http.post('api/public/common/DeleteTableRecords',Note_data).success(function(Listdata) {
                                		var data = {"status": "error", "message": "1 Note removed"}
                                        notifyService.notify(data.status, data.message);
                              });
                              $scope.AllNotesData.splice(index,1);
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
                           		//AJloader.show();
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
                                          AJloader.hide();
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


}]);
app.controller('PurchaseCECtrl', ['$scope',  '$http','$state','$stateParams', 'AuthService',function($scope,$http,$state,$stateParams,AuthService) {
                          AuthService.AccessService('BC');
                           $scope.order_id = $stateParams.id;

}]);