app.controller('PurchaseListCtrl', ['$scope',  '$http','$state','$stateParams', 'AuthService',function($scope,$http,$state,$stateParams,AuthService) {
                          AuthService.AccessService('BC');
                          $scope.CurrentController=$state.current.controller;
                          $type = $stateParams.id;
                          $http.get('api/public/purchase/ListPurchase/'+$type ).success(function(Listdata) 
                          		  {
                                          $scope.ListPurchase = Listdata.data;
                                  });
}]);
app.controller('PurchasePOCtrl', ['$scope','$sce',  '$http','$modal','$state','$stateParams','$filter', 'AuthService',function($scope,$sce,$http,$modal,$state,$stateParams,$filter,AuthService) {
                           AuthService.AccessService('BC');
                           var modalInstance='';
                           //$scope.order_id = $stateParams.id;
                           $scope.po_id = $stateParams.id;
                           if($scope.po_id=='' || $scope.po_id==0)
                           {
                           		$state.go('purchase.list',{"id":1});
                           		return false;
                           }
                           GetPodata($scope.po_id);
                           function GetPodata(po_id)
                           {
                           		$http.get('api/public/purchase/GetPodata/'+po_id ).success(function(PoData) 
                          		  {
                          		  		  if(PoData.data.success==0)
                          		  		  {
                          		  		  	$state.go('purchase.list',{"id":1});
                           					return false;
                          		  		  }
                                          $scope.ArrPo = PoData.data.records.po[0];
                                          $scope.ArrPoLine = PoData.data.records.poline;
                                          $scope.ArrUnassign = PoData.data.records.unassign_order;
                                          $scope.ordered = PoData.data.records.order_total[0];
                                          $scope.received = PoData.data.records.received_total[0].received;
                                          $scope.received_line = PoData.data.records.received_line;
                                          $scope.currentPOUrl = $sce.trustAsResourceUrl(PoData.data.records.po[0].url);
                                          //$scope.po_id = PoData.data.records.po_id;
                                          getNotesDetail(po_id);
                                        // console.log($scope.ordered);
                                  });
                       		}

                       	  function short_over(poline_id)
                          {
                          		$http.get('api/public/purchase/short_over/'+poline_id).success(function(result) {

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
                                //getNotesDetail(getclient_id);
                              });
                              $scope.AllNotesData.splice(index,1);
                          }

                           $scope.ChangeOrderStatus = function(poline_id,value,purchase_id ){
                           //	console.log(value);
                            	  $http.get('api/public/purchase/ChangeOrderStatus/'+poline_id+'/'+value+'/'+purchase_id ).success(function(PoData) 
                          		  {
                                       GetPodata($scope.po_id ); 
                                  });
                          }
                          $scope.EditOrderLine = function(Poline_data){
                          			
                            	  $http.post('api/public/purchase/EditOrderLine',Poline_data ).success(function(PoData) 
                          		  {
                                       GetPodata($scope.po_id ); 
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
                                  });
                          }
                          $scope.Receive_order = function(data){

                          		 $http.post('api/public/purchase/Receive_order',data ).success(function() 
                          		  {
                                       GetPodata($scope.po_id ); 
                                  });
                          }

                          $scope.RemoveReceiveLine = function(id,poline_id){
                          	  var RecLine = {};
	                          RecLine.cond = {id:id};
	                          RecLine.table = 'purchase_received';
                              $http.post('api/public/common/DeleteTableRecords',RecLine).success(function(Listdata) {
                              		short_over(poline_id );
                               		GetPodata($scope.po_id ); 
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
	                              Receive_data.table ='purchase_order'
	                              Receive_data.data ={vendor_charge:$event.target.value}
	                              Receive_data.cond ={ po_id :$scope.po_id}
	                              $http.post('api/public/common/UpdateTableRecords',Receive_data).success(function(result) {
	                              		GetPodata($scope.po_id);
                                  		//getNotesDetail(po_id);
                                });
                          }
                          
}]);
app.controller('PurchaseSGCtrl', ['$scope',  '$http','$state','$stateParams', 'AuthService',function($scope,$http,$state,$stateParams,AuthService) {
                          AuthService.AccessService('BC');

                           $scope.order_id = $stateParams.id;

}]);
app.controller('PurchaseCPCtrl', ['$scope',  '$http','$state','$stateParams', 'AuthService',function($scope,$http,$state,$stateParams,AuthService) {
                          AuthService.AccessService('BC');
                           $scope.order_id = $stateParams.id;

}]);
app.controller('PurchaseCECtrl', ['$scope',  '$http','$state','$stateParams', 'AuthService',function($scope,$http,$state,$stateParams,AuthService) {
                          AuthService.AccessService('BC');
                           $scope.order_id = $stateParams.id;

}]);