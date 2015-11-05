app.controller('orderListCtrl', ['$scope','$http','$location','$state','$modal','AuthService','$log','AllConstant', function($scope,$http,$location,$state,$modal,AuthService,$log,AllConstant) {
                          
    $http.get('api/public/order/listOrder').success(function(Listdata) {
        $scope.listOrder = Listdata.data;
    });

    $scope.deleteorder = function (order_id) {

        var permission = confirm(AllConstant.deleteMessage);
        if (permission == true) {
            
            $http.post('api/public/order/deleteOrder',order_id).success(function(result, status, headers, config) {

                if(result.data.success=='1')
                {
                    $state.go('order.list');
                    $("#order_"+order_id).remove();
                    return false;
                }
            });
        }
    } // DELETE ORDER FINISH

    var companyData = {};
    companyData.table ='client'
    companyData.cond ={status:1,is_delete:1}
    
    $http.post('api/public/common/GetTableRecords',companyData).success(function(result) {
        
        if(result.data.success == '1') 
        {
            $scope.allCompany =result.data.records;
        } 
        else
        {
            $scope.allCompany=[];
        }
    });

    $scope.openpopup = function () {

        var modalInstance = $modal.open({
                                        animation: $scope.animationsEnabled,
                                        templateUrl: 'views/front/order/add.html',
                                        scope: $scope,
                                        size: 'sm'
                            });

        modalInstance.result.then(function (selectedItem) {
            $scope.selected = selectedItem;
        }, function () {
            $log.info('Modal dismissed at: ' + new Date());
        });

        $scope.ok = function (orderData) {

            /*$http.post('api/public/order/orderAdd',data).success(function(result, status, headers, config) {
                
                                           $state.go('order.list');
                                            return false;
                                 
                                  });*/

            var order_data = {};
            order_data.data = orderData;
            // Address_data.data.client_id = $stateParams.id;
            order_data.table ='orders'

            $http.post('api/public/common/InsertRecords',order_data).success(function(result) {
                
                if(result.data.success == '1') 
                {
                    modalInstance.close($scope);
                    $state.go('order.edit',{id: result.data.id,client_id:order_data.data.client_id});
                    return false;
                }
                else
                {
                    console.log(result.data.message);
                }
            });
            // modalInstance.close($scope.selected.item);
        };

        $scope.cancel = function () {
            modalInstance.dismiss('cancel');
        };
    };

}]);

app.controller('orderEditCtrl', ['$scope','$http','logger','notifyService','$location','$state','$stateParams','$modal','AuthService','$log','sessionService','AllConstant', function($scope,$http,logger,notifyService,$location,$state,$stateParams,$modal,AuthService,$log,sessionService,dateWithFormat,AllConstant) {

    var order_id = $stateParams.id
    var client_id = $stateParams.client_id

    if($stateParams.id && $stateParams.client_id) {

        var combine_array_id = {};
        combine_array_id.id = $stateParams.id;
        combine_array_id.client_id = $stateParams.client_id;
        
        $http.post('api/public/order/orderDetail',combine_array_id).success(function(result, status, headers, config) {
        
            if(result.data.success == '1') {

            $scope.order = result.data.records[0];
            $scope.client_data = result.data.client_data;
            $scope.client_main_data = result.data.client_main_data;
            $scope.orderPositionAll = result.data.order_position;
            $scope.orderLineAll = result.data.order_line;

            }
            else {
                $state.go('app.dashboard');
            }
        });
    }

    $scope.notesave = function($event,id) {

        $scope.modalInstanceEdit  ='';

        var event_column_name =  $event.target.name;
        console.log(event_column_name);

        $scope.order_data_note = {};
        $scope.order_data_note.data = {};
        $scope.order_data_note.cond = {};
        $scope.order_data_note['table'] ='orders'
        $scope.order_data_note.data[event_column_name] = $event.target.value;
        $scope.order_data_note.cond['id'] = $stateParams.id;
        $http.post('api/public/common/UpdateTableRecords',$scope.order_data_note).success(function(result) {

        });
    }
    $scope.modalInstanceEdit  ='';
    $scope.CurrentUserId =  sessionService.get('user_id');
    $scope.CurrentController=$state.current.controller;

    $http.get('api/public/common/getAllMiscDataWithoutBlank').success(function(result, status, headers, config) {
              $scope.miscData = result.data.records;
    });

    $scope.addOrder = function(){
        $scope.orderPositionAll.push({ position_id:'' ,description:'', placement_type:'', color_stitch_count:'', qnty:'',discharge_qnty:''
                                                    ,speciality_qnty:'', foil_qnty:'', ink_charge_qnty:'', number_on_light_qnty:'',number_on_dark_qnty:''
                                                  ,oversize_screens_qnty:'', press_setup_qnty:'', screen_fees_qnty:'', dtg_size:'',dtg_on:''});
    }

    $scope.removeOrder = function(index,id) {

        var permission = confirm("Are you sure want to delete this record ? Clicking Ok will delete record permanently.");
        if (permission == true) {
  
            if(angular.isUndefined(id)) {
                $scope.orderPositionAll.splice(index,1);
            } else {

                var order_data = {};
                order_data.table ='order_positions'
                order_data.cond ={id:id}
                $http.post('api/public/common/DeleteTableRecords',order_data).success(function(result) {
                
                });

                $scope.orderPositionAll.splice(index,1);
            }
        }
    }

    $scope.addOrderLine = function() {
                            $scope.orderLineAll.push({ size_group_id:'' ,
                                                        product_id:'', 
                                                        qnty:'',
                                                        markup:'',
                                                        items:[
                                                                {size:'',qnty:'0'},
                                                                {size:'',qnty:'0'},
                                                                {size:'',qnty:'0'},
                                                                {size:'',qnty:'0'},
                                                                {size:'',qnty:'0'},
                                                                {size:'',qnty:'0'},
                                                                {size:'',qnty:'0'}
                                                        ],
                                                        override:'',
                                                        peritem:''
                                                    });
    }

    $scope.removeOrderLine = function(index,id) {

        var permission = confirm("Are you sure want to delete this record ? Clicking Ok will delete record permanently.");
        if (permission == true) {
  
            if(angular.isUndefined(id)) {
                $scope.orderLineAll.splice(index,1);
            } 
            else {

                var order_data = {};
                order_data.table ='order_orderlines'
                order_data.cond ={id:id}
                $http.post('api/public/common/DeleteTableRecords',order_data).success(function(result) {
                    
                    var data = {"status": "success", "message": "Orderline has been deleted"}
                    notifyService.notify(data.status, data.message);
                });

                $scope.orderLineAll.splice(index,1);
            }
        }
    }

    var companyData = {};
    companyData.table ='client'
    companyData.cond ={status:1,is_delete:1}

    $http.post('api/public/common/GetTableRecords',companyData).success(function(result) {
        
        if(result.data.success == '1') 
        {
            $scope.allCompany =result.data.records;
        } 
        else
        {
            $scope.allCompany=[];
        }
    });

    var priceGridData = {};
    priceGridData.table ='price_grid'
    priceGridData.cond ={status:1,is_delete:1}

    $http.post('api/public/common/GetTableRecords',priceGridData).success(function(result) {
        
        if(result.data.success == '1') 
        {
            $scope.allGrid =result.data.records;
        } 
        else
        {
            $scope.allGrid=[];
        }
    });

    var productData = {};
    productData.table ='products'
    productData.cond ={status:1,is_delete:1}

    $http.post('api/public/common/GetTableRecords',productData).success(function(result) {
        
        if(result.data.success == '1') 
        {
            $scope.allProduct =result.data.records;
        } 
        else
        {
            $scope.allProduct=[];
        }
    });

    $http.get('api/public/common/getStaffList').success(function(result, status, headers, config) 
    {
        if(result.data.success == '1') 
        {
            $scope.staffList =result.data.records;
        }
    });

    $http.get('api/public/common/getBrandCo').success(function(result, status, headers, config) 
    {
        if(result.data.success == '1') 
        {
            $scope.brandCoList =result.data.records;
        } 
    });

    $scope.saveOrderDetails=function(postArray,id)
    {
        var order_data = {};
        order_data.table ='orders'
        order_data.data =postArray
        order_data.cond ={id:id}
        
        $http.post('api/public/common/UpdateTableRecords',order_data).success(function(result) {
            $state.go('order.list');
        });
    }

    $scope.savePositionData=function(postArray)
    {
        if(postArray.length != 0) {

            order_id = $stateParams.id
            client_id = $stateParams.client_id

            angular.forEach(postArray, function(value, key) {

                if(angular.isUndefined(value.id)) {

                    var order_data_insert  = {};
                    value.order_id = order_id;

                    order_data_insert.data = value;
                    // Address_data.data.client_id = $stateParams.id;
                    order_data_insert.table ='order_positions'

                    $http.post('api/public/common/InsertRecords',order_data_insert).success(function(result) {

                    });
                } 
                else {

                    var order_data = {};
                    order_data.table ='order_positions'
                    order_data.data =value
                    order_data.cond ={id:value.id}
                    $http.post('api/public/common/UpdateTableRecords',order_data).success(function(result) {

                    });
                }
            });

            //$state.go('order.edit',{id: order_id,client_id:client_id},{reload:true});
            //return false;
        }
    }

    $scope.saveOrderLineData=function(postArray)
    {
        if(postArray.length != 0) {

            angular.forEach(postArray, function(value, key) {

                if(angular.isUndefined(value.id)) {

                    var order_data_insert  = {};
                    value.order_id = order_id;
                    order_data_insert.data = value;
                    order_data_insert.table ='order_orderlines'

                    $http.post('api/public/order/orderLineAdd',order_data_insert).success(function(result) {

                    });

                }
                else {

                    var order_data = {};
                    order_data.table ='order_orderlines'
                    order_data.data =value
                    order_data.cond ={id:value.id}
                    $http.post('api/public/order/orderLineUpdate',order_data).success(function(result) {

                    });
                }
            });

            setTimeout(function () {
                                    var data = {"status": "success", "message": "Orderline details has been updated"}
                                    notifyService.notify(data.status, data.message);
                                }, 1000);
//            $state.go('order.edit',{id: order_id,client_id:client_id},{reload:true});
        }
        else
        {
            var data = {"status": "error", "message": "Please add atleast one orderline"}
            notifyService.notify(data.status, data.message);
        }
    }
                            
    $scope.openOrderPopup = function (page) {

        $scope.edit='add';
        var modalInstance = $modal.open({
                                templateUrl: 'views/front/order/'+page,
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
        
        $scope.saveNotes=function(saveNoteDetails)
        {
            var Note_data = {};
            Note_data.data = saveNoteDetails;
            Note_data.data.order_id = $stateParams.id;
            Note_data.data.user_id = $scope.CurrentUserId;

            $http.post('api/public/order/saveOrderNotes',Note_data).success(function(Listdata) {
                getNotesDetail(order_id);
            });
            modalInstance.dismiss('cancel');
        };
    };
    
    // **************** NOTES TAB CODE END  ****************

    getNotesDetail(order_id);
    
    function getNotesDetail(order_id)
    {
        $http.get('api/public/order/getOrderNoteDetails/'+order_id).success(function(result, status, headers, config) 
        {
            if(result.data.success == '1') 
            {
                $scope.allordernotes =result.data.records;
            } 
            else
            {
                $scope.allordernotes=[];
            }
        });
    }

    function getOrderDetailById(id)
    {
        $http.get('api/public/order/getOrderDetailById/'+id).success(function(result) {

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

    $scope.removeordernotes = function(index,id) {
        
        $http.get('api/public/order/deleteOrderNotes/'+id).success(function(Listdata) {
            //getNotesDetail(order_id);
        });
        $scope.allordernotes.splice(index,1);
    }

    $scope.Editnotes=function(NoteDetails)
    {
        var Note_data = {};
        //console.log(Note_data); return false;
        Note_data.data = NoteDetails;
        Note_data.note_id = NoteDetails;
        $http.post('api/public/client/EditCleintNotes',Note_data).success(function(Listdata) {
            //getNotesDetail(order_id );
        });
    };

    $scope.editOrderPopup = function (id) {
        
        getOrderDetailById(id);
        $scope.edit='edit';
        //console.log($scope);
        var modalInstanceEdit = $modal.open({
            templateUrl: 'views/front/order/order_note.html',
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

        $scope.updateNotes=function(updateNote)
        {
            var updateNoteData = {};
            //console.log(Note_data); return false;
            updateNoteData.data = updateNote;
            $http.post('api/public/order/updateOrderNotes',updateNoteData).success(function(Listdata) {
                getNotesDetail(order_id );
            });
            modalInstanceEdit.dismiss('cancel');
        };
    };

  // **************** NOTES TAB CODE END  ****************
}]);

app.controller('orderAddCtrl', ['$scope','$http','$location','$state','$modal','AuthService','$log','AllConstant', function($scope,$http,$location,$state,$modal,AuthService,$log,AllConstant) {
            
    var companyData = {};
    companyData.table ='client'
    companyData.cond ={status:1,is_delete:1}
    $http.post('api/public/common/GetTableRecords',companyData).success(function(result) {
        
        if(result.data.success == '1') 
        {
            $scope.allCompany =result.data.records;
        } 
        else
        {
            $scope.allCompany=[];
        }
    });
}]);