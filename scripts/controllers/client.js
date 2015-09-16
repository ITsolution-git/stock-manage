app.controller('clientAddCtrl', ['$scope','$http','$location','$state','$modal','AuthService','$log', function($scope,$http,$location,$state,$modal,AuthService,$log) {
                          AuthService.AccessService('BC');
                          $scope.CurrentController=$state.current.controller;

                          $http.get('api/public/common/type/client').success(function(Listdata) {
                                  $scope.Typelist = Listdata.data
                            });

                          $scope.SaveClient = function () {
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
app.controller('clientListCtrl', ['$scope','$http','$location','$state','$modal','AuthService','$log', function($scope,$http,$location,$state,$modal,AuthService,$log) {
                          AuthService.AccessService('BC');
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
                          $http.get('api/public/client/ListClient').success(function(Listdata) {
                                       if(Listdata.data.success=='1')
                                       {
                                          $scope.ListClient = Listdata.data;
                                       }
                                  });


}]);
app.controller('clientEditCtrl', ['$scope','$http','$location','$state','$modal','$stateParams','AuthService','$log','Common_Misc', function($scope,$http,$location,$state,$modal,$stateParams,AuthService,$log,Common_Misc) {
                          var client_contacts=[];
                          var AddrTypeData={};
                          var PriceGrid={};
                          var Arrdisposition={};

                          AuthService.AccessService('BC');
                          $scope.CurrentController=$state.current.controller;
                          var getclient_id = $stateParams.id;
                         // address_type = Common_Misc.GetMicType('art_type');

                          $http.get('api/public/common/GetMicType/address_type').success(function(result, status, headers, config) 
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
                              
                          });
                           $http.get('api/public/admin/price').success(function(result, status, headers, config) 
                          {
                              if(result.data.success == '1') 
                              {
                                  $scope.PriceGrid =result.data.records;
                              } 
                              
                          });

                          $http.get('api/public/common/getStaffList').success(function(result, status, headers, config) 
                          {
                              if(result.data.success == '1') 
                              {
                                  $scope.StaffList =result.data.records;
                              } 
                              
                          });

                          $http.get('api/public/common/type/company').success(function(result, status, headers, config) 
                          {
                              if(result.data.success == '1') 
                              {
                                  $scope.ArrCleintType =result.data.records;
                              } 
                              
                          });  
                           getClientDetail(getclient_id );
                           function getClientDetail(getclient_id)
                           {
                                $http.get('api/public/client/GetclientDetail/'+getclient_id).success(function(result, status, headers, config) 
                                {
                                    if(result.data.success == '1') 
                                    {
                                        $scope.mainaddress =result.data.records.address;
                                        $scope.salesDetails =result.data.records.sales;
                                        $scope.maincompcontact =result.data.records.contact;
                                        $scope.main =result.data.records.main;
                                        $scope.client_tax =result.data.records.tax;
                                    } 
                                    
                                });
                            }
                         


    //****************  CONTACTS TAB CODE START  ****************                          
                          getContacts($stateParams.id );
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


                          $scope.items = ['item1', 'item2', 'item3'];
                          $scope.open = function (size) {
                            var modalInstance = $modal.open({
                              templateUrl: 'views/front/client/document.html',
                              size: size,
                              resolve: {
                                items: function () {
                                  return $scope.items;
                                }
                              }
                            });

                            modalInstance.result.then(function (selectedItem) {
                              $scope.selected = selectedItem;
                            }, function () {
                              $log.info('Modal dismissed at: ' + new Date());
                            });
                          };
                          
                          $scope.selected = {
                            item: $scope.items[0]
                          };

                          $scope.ok = function () {
                            $modalInstance.close($scope.selected.item);
                          };

                          $scope.cancel = function () {
                            $modalInstance.dismiss('cancel');
                          };

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
                                    getClientDetail(getclient_id );
                              });
                          };
                         $scope.SaveTaxDetails=function(TaxDetails)
                          {
                              var Tax_data = {};
                              //console.log(TaxDetails); return false;
                              Tax_data.data = TaxDetails;
                              Tax_data.id = $stateParams.id;
                              $http.post('api/public/client/SaveCleintTax',Tax_data).success(function(Listdata) {
                                    //getClientDetail(getclient_id );
                              });
                          };
}]);
