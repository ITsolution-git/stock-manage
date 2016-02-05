app.controller('clientAddCtrl', ['$scope','$rootScope','$http','$location','$state','$modal','AuthService','$log', function($scope,$rootScope,$http,$location,$state,$modal,AuthService,$log) {
                          AuthService.AccessService('BC');
                          $scope.company_id = $rootScope.company_profile.company_id;
                          $scope.CurrentController=$state.current.controller;

                          $http.get('api/public/common/type/client').success(function(Listdata) {
                                  $scope.Typelist = Listdata.data
                            });
                         
                         $scope.CheckClient = function ($event) {
                              $("#client_namecheck").hide();
                              var params = {};
                              params.data = {value:$event.target.value,company_id:$scope.company_id};
                              var client_name = $event.target.value;
                              if(client_name.trim()!='')
                              {
                                $http.post('api/public/client/checkCompName',params).success(function(response) {
                                       if(response.data.success=='1')
                                       {
                                          if(response.data.result!='0')
                                          {
                                              $("#client_namecheck").show();
                                          }
                                       }
                                });
                              }
                           }

                          $scope.SaveClient = function () {
                            $scope.client.company_id = $scope.company_id;
                            var company_post = $scope.client;
                            if($scope.client.client_company!='')
                            {
                              $http.post('api/public/client/addclient',company_post).success(function(Listdata) {
                                      if(Listdata.data.success=='1')
                                       {
                                           $state.go('client.list');
                                           return false;
                                       }  
                              });
                            }
                            
                          }

}]);
app.controller('clientListCtrl', ['$scope','$rootScope','$http','$location','$state','$modal','AuthService','$log','$filter', function($scope,$rootScope,$http,$location,$state,$modal,AuthService,$log,$filter) {
                          AuthService.AccessService('BC');
                         $scope.company_id = $rootScope.company_profile.company_id;
                          $scope.CurrentController=$state.current.controller;
                          var delete_params = {};
                          $scope.deleteclient = function (comp_id) {
                                delete_params.id = comp_id;
                                var permission = confirm("Are you sure to delete this record ?");
                                if (permission == true) {
                                $http.post('api/public/client/DeleteClient',delete_params).success(function(result, status, headers, config) {
                                              
                                              if(result.data.success=='1')
                                              {
                                                $state.go('client.list');
                                                $("#comp_"+comp_id).remove();
                                                return false;
                                              }  
                                         });
                                      }
                                  } // DELETE COMPANY FINISH

                                  $("#ajax_loader").show();


                                   var company_list_data = {};
                                    var condition_obj = {};
                                    condition_obj['company_id'] =  $scope.company_id;
                                     company_list_data.cond = angular.copy(condition_obj);


                          $http.post('api/public/client/ListClient',company_list_data).success(function(Listdata) {
                                       if(Listdata.data.success=='1')
                                       {
                                          $scope.clients = Listdata.data.records;
                                          $("#ajax_loader").hide();

                                          var init;

                                          $scope.searchKeywords = '';
                                          $scope.filteredClients = [];
                                          $scope.row = '';
                                          $scope.select = function (page) {
                                            var end, start;
                                            start = (page - 1) * $scope.numPerPage;
                                            end = start + $scope.numPerPage;
                                            return $scope.currentPageClients = $scope.filteredClients.slice(start, end);
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
                                            $scope.filteredClients = $filter('filter')($scope.clients, $scope.searchKeywords);
                                            return $scope.onFilterChange();
                                          };
                                          $scope.order = function (rowName) {
                                            if ($scope.row === rowName) {
                                                return;
                                            }
                                            $scope.row = rowName;
                                            $scope.filteredClients = $filter('orderBy')($scope.clients, rowName);
                                            return $scope.onOrderChange();
                                          };
                                          $scope.numPerPageOpt = [10, 20, 50, 100];
                                          $scope.numPerPage = 10;
                                          $scope.currentPage = 1;
                                          $scope.currentPageClients = [];

                                          init = function () {
                                            $scope.search();

                                            return $scope.select($scope.currentPage);
                                          };
                                          return init();
                                       }
                                  });


}]);
app.controller('clientEditCtrl', ['$scope','$rootScope','$sce','$http','$location','$state','$modal','$stateParams','AuthService','$log','$filter', function($scope,$rootScope,$sce,$http,$location,$state,$modal,$stateParams,AuthService,$log,dateWithFormat,$filter) {

                           $("#ajax_loader").show();
                          $scope.company_id = $rootScope.company_profile.company_id;
                         // console.log(company_id);
                          var client_contacts=[];
                          var AddrTypeData={};
                          var PriceGrid={};
                          var Arrdisposition={};
                          var modalInstance='';
                          $scope.modalInstanceEdit  ='';

                          AuthService.AccessService('BC');
                          $scope.CurrentUserId =  $scope.app.user_id;

                          $scope.CurrentController=$state.current.controller;
                          var getclient_id = $stateParams.id;
                         // address_type = Common_Misc.GetMicType('art_type');

                        /*  $http.get('api/public/common/GetMicType/address_type').success(function(result, status, headers, config) 
                          {
                              if(result.data.success == '1') 
                              {
                                  $scope.AddrTypeData =result.data.records;
                              } 
                              
                          });

                          $http.get('api/public/common/GetMicType/disposition').success(function(result, status, headers, config) 
                          {
                              if(result.data.success == '1') 
                              {
                                  $scope.Arrdisposition =result.data.records;
                              } 
                              
                          });*/
                           $http.get('api/public/admin/price').success(function(result, status, headers, config) 
                          {
                              if(result.data.success == '1') 
                              {
                                  $scope.PriceGrid =result.data.records;
                              } 
                              
                          });

                          /*$http.get('api/public/common/getStaffList').success(function(result, status, headers, config) 
                          {
                              if(result.data.success == '1') 
                              {
                                 // $scope.StaffList =result.data.records;
                              } 
                              
                          });*/

                         /* $http.get('api/public/common/type/company').success(function(result, status, headers, config) 
                          {
                              if(result.data.success == '1') 
                              {
                                  $scope.ArrCleintType =result.data.records;
                              } 
                              
                          });  */
                           $scope.CheckClient = function ($event) {
                              $("#client_namecheck").hide();
                              var params = {};
                              params.data = {value:$event.target.value,company_id:$scope.company_id,client_id:$stateParams.id};
                              var client_name = $event.target.value;
                              if(client_name.trim()!='')
                              {
                                $http.post('api/public/client/checkCompName',params).success(function(response) {
                                       if(response.data.success=='1')
                                       {
                                          if(response.data.result!='0')
                                          {
                                              $("#client_namecheck").show();
                                          }
                                       }
                                });
                              }
                           }

                           getClientDetail(getclient_id );
                           function getClientDetail(getclient_id)
                           {
                                $("#ajax_loader").show();
                                $http.get('api/public/client/GetclientDetail/'+getclient_id).success(function(result) 
                                {
                                    if(result.data.success == '1') 
                                    {

                                        $scope.Response = result.data.records;
                                        $scope.mainaddress = $scope.Response.clientDetail.address;
                                        $scope.salesDetails =$scope.Response.clientDetail.sales;
                                        $scope.maincompcontact =$scope.Response.clientDetail.contact;
                                        $scope.main =$scope.Response.clientDetail.main;
                                        $scope.client_tax =$scope.Response.clientDetail.tax;
                                        $scope.pl_imp =$scope.Response.clientDetail.pl_imp;
                                        
                                        $scope.StaffList =$scope.Response.StaffList;
                                        $scope.ArrCleintType =$scope.Response.ArrCleintType;
                                      //  $scope.PriceGrid = $scope.Response.PriceGrid;
                                        $scope.allContacts = $scope.Response.allContacts;
                                        $scope.allclientnotes = $scope.Response.allclientnotes;
                                        $scope.Arrdisposition = $scope.Response.Arrdisposition;
                                        $scope.Client_orders = $scope.Response.Client_orders;

   
                                        $scope.currentProjectUrl = $sce.trustAsResourceUrl($scope.main.salesweb);

                                        $("#ajax_loader").hide();
                                    } 
                                    
                                });
                            }
                          

                         


    //****************  CONTACTS TAB CODE START  ****************                          
                          //getContacts($stateParams.id );
                          $scope.currentActivity = 1;
                          function getContacts(getclient_id)
                           {
                              $http.post('api/public/client/getContacts',getclient_id).success(function(Listdata) 
                              {
                                   if(Listdata.data.success=='1')
                                   {
                                      $scope.allContacts = Listdata.data.records
                                   }
                                   else
                                   {
                                      $scope.allContacts = [];
                                   }   
                              });
                           }

                          
                          client_contacts :[{ isUserAnswer: false}] ;
                          $scope.addContacts = function(){
                            $scope.allContacts.push({ first_name:'' ,last_name:'', location:'', phone:'', email:'',contact_main:''});
                          }

                          $scope.removeContacts = function(index){
                              $scope.allContacts.splice(index,1);
                          }
    //****************  CONTACTS TAB CODE START  ****************
    //****************  ADDRESS TAB CODE START  ****************
                         
                          
                           getAdress(getclient_id );
                           function getAdress(getclient_id)
                           {
                              //var permadd={};
                              $http.post('api/public/client/getAddress',getclient_id).success(function(Listdata) 
                              {
                                   if(Listdata.data.success=='1')
                                   {

                                      $scope.permadd=Listdata.data.records.address;
                                      $scope.alladdress = Listdata.data.records.result
                                   }
                                   else
                                   {
                                      $scope.alladdress = [];
                                   }   
                              });
                           }
                        
                          $scope.addAddress = function(){
                            $scope.alladdress.push({address:'', city:'' ,state:'', postal_code:'', type:''});
                          }

                          $scope.removeAddress = function(index){
                              $scope.alladdress.splice(index,1);
                          }

     // **************** ADDRESS TAB CODE END  ****************

     // **************** NOTES TAB CODE END  ****************
                          //getNotesDetail(getclient_id );
                           function getNotesDetail(getclient_id)
                           {
                                $http.get('api/public/client/GetNoteDetails/'+getclient_id).success(function(result, status, headers, config) 
                                {
                                    if(result.data.success == '1') 
                                    {
                                        $scope.allclientnotes =result.data.records;
                                    } 
                                    else
                                    {
                                        $scope.allclientnotes=[];
                                    }
                                    
                                });
                            }
                            function GetClientDetailById(id)
                            {
                               $http.get('api/public/client/GetClientDetailById/'+id).success(function(result) {
                                    
                                    if(result.data.success == '1') 
                                    {
                                      $scope.thisclientNote =result.data.records;

                                    }
                                    else
                                    {
                                      $scope.thisclientNote=[];
                                    }
                              });
                            }

                            $scope.removeclientnotes = function(index,id){
                              $http.get('api/public/client/DeleteCleintNotes/'+id).success(function(Listdata) {
                                //getNotesDetail(getclient_id);
                              });
                              $scope.allclientnotes.splice(index,1);
                          }
 

                           $scope.Editnotes=function(NoteDetails)
                          {
                              var Note_data = {};
                              //console.log(Note_data); return false;
                              Note_data.data = NoteDetails;
                              Note_data.note_id = NoteDetails;
                              $http.post('api/public/client/EditCleintNotes',Note_data).success(function(Listdata) {
                                    //getNotesDetail(getclient_id );
                              });
                          };
                          

  // **************** NOTES TAB CODE END  ****************
  // **************** DISTRIBUTION POPUP TAB CODE END  ****************
                          
                          getDistAdressDetail('');
                          function getDistAdressDetail()
                          {
                            var Address_data = {};
                            Address_data.table ='client_distaddress'
                            Address_data.cond ={status:1, client_id: $stateParams.id}
                            $http.post('api/public/common/GetTableRecords',Address_data).success(function(result) {
                                if(result.data.success == '1') 
                                {
                                    $scope.AllDistAddress =result.data.records;
                                } 
                                else
                                {
                                    $scope.AllDistAddress=[];
                                }
                              });
                          }
                          
                          function getDistAdressDetailbyId(id)
                          {
                            var Address_data = {};
                            Address_data.table ='client_distaddress'
                            Address_data.cond ={id:id}
                            $http.post('api/public/common/GetTableRecords',Address_data).success(function(result) {
                                if(result.data.success == '1') 
                                {
                                    $scope.dist_address =result.data.records[0];
                                } 
                               
                              });
                          }
                          
                          $scope.EditeDistAddress= function (id) {
                            getDistAdressDetailbyId(id);
                            $scope.edit='edit';
                            //console.log('edit');
                            var modalInstanceEdit = $modal.open({
                              templateUrl: 'views/front/client/address.html',
                              scope : $scope,
                              controller:'clientEditCtrl'

                            });

                            modalInstanceEdit.result.then(function (selectedItem) {
                              $scope.selected = selectedItem;
                            }, function () {
                              
                              //$log.info('Modal dismissed at: ' + new Date());
                            });
                            $scope.ClosePopup = function (cancel)
                            {
                               modalInstanceEdit.dismiss('cancel');
                            };
                            $scope.UpdateDistAdress=function(postArray,id)
                            {
                              var Address_data = {};
                              Address_data.table ='client_distaddress'
                              Address_data.data =postArray
                              Address_data.cond ={id:id}
                              $http.post('api/public/common/UpdateTableRecords',Address_data).success(function(result) {
                                  getDistAdressDetail();
                                  modalInstanceEdit.dismiss('cancel');
                                });
                            }
                          };
                       


 // **************** DISTRIBUTION POPUP TAB CODE END  ****************
 // **************** DOCUMENT POPUP TAB CODE END  ****************
                         // modalInstance.dismiss('cancel');
                          $scope.items = 'item1';
                          $scope.openPopup = function (page) {
                            $scope.edit='add';
                            var modalInstance = $modal.open({
                              templateUrl: 'views/front/client/'+page,
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
                            $scope.Savenotes=function(saveNoteDetails)
                            {
                                var Note_data = {};
                                Note_data.data = saveNoteDetails;
                                Note_data.data.client_id = $stateParams.id;
                                 Note_data.data.user_id = $scope.CurrentUserId
                                $http.post('api/public/client/SaveCleintNotes',Note_data).success(function(Listdata) {
                                      getNotesDetail(getclient_id );
                                });
                                modalInstance.dismiss('cancel');
                            };
                            $scope.SaveDistAddress=function (ArrDistAddress)
                            {
                                var Address_data = {};
                                Address_data.data = ArrDistAddress;
                                Address_data.data.client_id = $stateParams.id;
                                Address_data.table ='client_distaddress'

                                $http.post('api/public/common/InsertRecords',Address_data).success(function(result) {
                                    if(result.data.success == '1') 
                                    {
                                        getDistAdressDetail();
                                    }
                                    else
                                    {
                                        console.log(result.data.message);
                                    }
                                });
                                 modalInstance.dismiss('cancel');
                            }

                          };

                          $scope.EditPopup = function (id) {
                            GetClientDetailById(id);
                            $scope.edit='edit';
                            //console.log($scope);
                            var modalInstanceEdit = $modal.open({
                              templateUrl: 'views/front/client/note.html',
                              scope : $scope,
                            });

                            modalInstanceEdit.result.then(function (selectedItem) {
                              $scope.selected = selectedItem;
                            }, function () {
                              
                              //$log.info('Modal dismissed at: ' + new Date());
                            });

                             $scope.ClosePopup = function (cancel)
                            {
                               modalInstanceEdit.dismiss('cancel');
                            };
                             $scope.Updatenotes=function(UpdateNote)
                            {
                                var UpdateNote_data = {};
                                //console.log(Note_data); return false;
                                UpdateNote_data.data = UpdateNote;
                                $http.post('api/public/client/UpdateCleintNotes',UpdateNote_data).success(function(Listdata) {
                                      getNotesDetail(getclient_id );
                                });
                                modalInstanceEdit.dismiss('cancel');
                            };
                          };
                       

     // **************** DOCUMENT POPUP TAB CODE END  ****************

                          $scope.SaveClientAddress=function(arrAddress,permadd)
                          {
                             var address_data = {};
                             address_data.data = arrAddress;
                             address_data.permadd = permadd;
                             address_data.id = $stateParams.id;
                             $http.post('api/public/client/ClientAddress',address_data).success(function(Listdata) {
                                    getAdress(getclient_id );
                              });
                          };
                          $scope.SaveClientContact=function (arrContact)
                          {
                              var contact_data = {};
                              contact_data.data = arrContact;
                              contact_data.maincontact= arrContact.maincontact;
                              contact_data.id = $stateParams.id;
                              $http.post('api/public/client/ClientContacts',contact_data).success(function(Listdata) {
                                    getContacts(getclient_id );
                                    getClientDetail(getclient_id );
                              });
                            

                          };
                          $scope.SaveSalesDetails=function(salesDetails)
                          {
                              var sales_data = {};
                              sales_data.data = salesDetails;
                              sales_data.id = $stateParams.id;
                              $http.post('api/public/client/SaveSalesDetails',sales_data).success(function(Listdata) {
                                    getClientDetail(getclient_id );
                              });
                          };
                         $scope.SaveCleintDetails=function(ClientDetails)
                          {
                              var Cleint_data = {};
                              //console.log(ClientDetails); return false;
                              Cleint_data.data = ClientDetails;
                              Cleint_data.id = $stateParams.id;
                              $http.post('api/public/client/SaveCleintDetails',Cleint_data).success(function(Listdata) {
                                    getClientDetail(getclient_id);
                              });
                          };
                         $scope.SaveTaxDetails=function(TaxDetails)
                          {
                              var Tax_data = {};
                              //console.log(TaxDetails); return false;
                              Tax_data.data = TaxDetails;
                              Tax_data.id = $stateParams.id;
                              $http.post('api/public/client/SaveCleintTax',Tax_data).success(function(Listdata) {
                                    getClientDetail(getclient_id );
                              });
                          };
                          $scope.SavePlimpDetails=function(PlimpDetails)
                          {
                              var Plimp_data = {};
                              //console.log(TaxDetails); return false;
                              Plimp_data.data = PlimpDetails;
                              Plimp_data.id = $stateParams.id;
                              $http.post('api/public/client/SaveCleintPlimp',Plimp_data).success(function(Listdata) {
                                    //getClientDetail(getclient_id );
                              });
                          };


                          $scope.openTab = function(tab_name){
                           if(tab_name == 'document'){
                            get_document_list(getclient_id);

                           } 
                        }



                         function get_document_list(client_id)
                            {
                                $("#ajax_loader").show();
                                $http.get('api/public/client/getDocument/'+client_id).success(function(result, status, headers, config) 
                                {
                                    if(result.data.success == '1') 
                                    {
                                        $scope.alldocumentnotes =result.data.records;
                                    } 
                                    else
                                    {
                                        $scope.alldocumentnotes=[];
                                    }
                                    $("#ajax_loader").hide();
                                });
                            }

                           
                            $scope.editDocumentPopup = function (id) {
        
                              getDocumentDetailbyId(id);
                               

                              $scope.edit='edit';
                              var modalInstanceEdit = $modal.open({
                                  templateUrl: 'views/front/client/document.html',
                                  scope : $scope,
                              });

                              modalInstanceEdit.result.then(function (selectedItem) {
                                  $scope.selected = selectedItem;
                              }, function () {
                                  //$log.info('Modal dismissed at: ' + new Date());
                              });

                              $scope.closePopup = function (cancel)
                              {
                                  modalInstanceEdit.dismiss('cancel');
                              };

                                $scope.updateDoc=function(updateDoc)
                                  {
                                      var updateDocData = {};
                                      updateDocData.data = updateDoc;
                                      $http.post('api/public/client/updateDoc',updateDocData).success(function(Listdata) {
                                          get_document_list(getclient_id);
                                      });
                                      modalInstanceEdit.dismiss('cancel');
                                  };
                            };


                            $scope.addDocumentPopup = function () {

                                      $scope.edit='add';
                                      var modalInstance = $modal.open({
                                                              templateUrl: 'views/front/client/document.html',
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
                                      
                                      $scope.saveDoc=function(saveDocDetails)
                                      {
                                          var doc_data = {};
                                          doc_data.data = saveDocDetails;
                                          doc_data.data.client_id = getclient_id;
                                         

                                          $http.post('api/public/client/saveDoc',doc_data).success(function(Listdata) {
                                             get_document_list(getclient_id);
                                          });
                                          modalInstance.dismiss('cancel');
                                      };
                                  };


                              function getDocumentDetailbyId(id)
                                {
                                    $http.get('api/public/client/getDocumentDetailbyId/'+id).success(function(result) {

                                        if(result.data.success == '1') 
                                        {
                                            $scope.thisorderNote =result.data.records;
                                           
                                        }
                                        else
                                        {
                                            $scope.thisorderNote=[];
                                        }
                                    });
                                }


                                $scope.removeDoc = function(index,id) {
        
                                      $http.get('api/public/client/deleteClientDoc/'+id).success(function(Listdata) {
                                          //getNotesDetail(order_id);
                                      });
                                      $scope.alldocumentnotes.splice(index,1);
                                  }
                              
                              



                         
}]);
