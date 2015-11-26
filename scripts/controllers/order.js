app.controller('orderListCtrl', ['$scope','$http','$location','$state','$modal','AuthService','$log','AllConstant', function($scope,$http,$location,$state,$modal,AuthService,$log,AllConstant) {
          
          $("#ajax_loader").show();
                
    $http.get('api/public/order/listOrder').success(function(Listdata) {
        $scope.listOrder = Listdata.data;
        $("#ajax_loader").hide();

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

app.controller('orderEditCtrl', ['$scope','$http','logger','notifyService','$location','$state','$stateParams','$modal','getPDataByPosService','AuthService','$log','sessionService','AllConstant', function($scope,$http,logger,notifyService,$location,$state,$stateParams,$modal,getPDataByPosService,AuthService,$log,sessionService,dateWithFormat,AllConstant) {

    var order_id = $stateParams.id
    $scope.order_id = $stateParams.id
    var client_id = $stateParams.client_id
    

    get_order_details(order_id,client_id);
    function get_order_details(order_id,client_id)
    {
        if($stateParams.id && $stateParams.client_id) {

            var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            combine_array_id.client_id = $stateParams.client_id;
            
            $("#ajax_loader").show();

            $http.post('api/public/order/orderDetail',combine_array_id).success(function(result, status, headers, config) {
            
                if(result.data.success == '1') {

                $scope.order = result.data.records[0];
                $scope.client_data = result.data.client_data;
                $scope.client_main_data = result.data.client_main_data;
                $scope.orderPositionAll = result.data.order_position;
                $scope.orderLineAll = result.data.order_line;
                $scope.order_items = result.data.order_item;

                $scope.orderline_id = 0;
                $scope.total_qty = 0;
                angular.forEach($scope.orderLineAll, function(value) {
                    $scope.orderline_id = parseInt(value.id);
                    $scope.total_qty += parseInt(value.qnty);
                });
                $("#ajax_loader").hide();


                }
                else {
                    $state.go('app.dashboard');
                }
            });
        }
    }

    $http.get('api/public/common/getAllVendors').success(function(result, status, headers, config) {
            $scope.vendors = result.data.records;
    });

    var color_data = {};
    color_data.table ='misc_type'
    color_data.cond ={type:'color_group',is_delete:1,status:1}
    color_data.notcond ={value:""}

    
    $http.post('api/public/common/GetTableRecords',color_data).success(function(result) {
        
        if(result.data.success == '1') 
        {
            $scope.colors =result.data.records;
        } 
        else
        {
            $scope.colors=[];
        }
    });
    

    $scope.notesave = function($event,id) {

        $scope.modalInstanceEdit  ='';

        var event_column_name =  $event.target.name;

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

    $scope.assign_item = function(order_id,order_item_id,item_name) {

        var Selected = $('#item_'+order_item_id);

        if(Selected.hasClass('chargesApplyActive'))
        {
            $("#ajax_loader").show();
            Selected.removeClass('chargesApplyActive')
            var order_data = {};
            order_data.table ='order_item_mapping'
            order_data.cond ={order_id:order_id,item_id:order_item_id}
            $http.post('api/public/common/DeleteTableRecords',order_data).success(function(result) {
            
            });

            var item_data = {item_name:item_name,order_id:order_id}
            $http.post('api/public/finishing/removeFinishingItem',item_data).success(function(result) {
            
                $("#ajax_loader").hide();
            });
        }
        else
        {
            $("#ajax_loader").show();
            $scope.total_qty = 0;
            angular.forEach($scope.orderLineAll, function(value) {
                $scope.total_qty += parseInt(value.qnty);
            });
            Selected.addClass('chargesApplyActive')
            var order_data = {};
            order_data.table ='order_item_mapping'
            order_data.data ={order_id:order_id,item_id:order_item_id}
            $http.post('api/public/common/InsertRecords',order_data).success(function(result) {
            
            });

            var item_data = {item_name:item_name,order_id:order_id,total_qty:$scope.total_qty}
            $http.post('api/public/finishing/addFinishingItem',item_data).success(function(result) {
                $("#ajax_loader").hide();
            });
        }
    }

    $scope.update_qnty = function(qty,id) {

        var i = '';
        var total = 0;

        if(qty == undefined && id == undefined)
        {
            for(i=1;i<=6;i++)
            {
                var size = $('#qnty__').val();
                if(size == '')
                {
                    size = '0';
                }
                total += parseInt(size);
            }
        }
        else
        {
            for(i=1;i<=6;i++)
            {
                var size = $('#qnty_'+id+'_'+i).val();
                if(size == '')
                {
                    size = '0';
                }
                total += parseInt(size);
            }
        }

        $scope.orderLineAllNew = [];

        $scope.total_qty = 0;
        angular.forEach($scope.orderLineAll, function(value) {
            
            if(value.orderline_id == id)
            {
                value.qnty = total;
            }
            $scope.total_qty += parseInt(value.qnty);
            $scope.orderLineAllNew = value;
        });
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
        
        $scope.orderline_id = parseInt($scope.orderline_id + 1);


        $scope.orderLineAll.push({ size_group_id:'' ,
                                    product_id:'',
                                    vendor_id:'',
                                    color_id:'',
                                    orderline_id:$scope.orderline_id,
                                    qnty:'0',
                                    markup:'',
                                    items:[
                                            {size:'',qnty:'0',number:'1'},
                                            {size:'',qnty:'0',number:'2'},
                                            {size:'',qnty:'0',number:'3'},
                                            {size:'',qnty:'0',number:'4'},
                                            {size:'',qnty:'0',number:'5'},
                                            {size:'',qnty:'0',number:'6'},
                                            {size:'',qnty:'0',number:'7'}
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
     
     console.log(postArray);return false;
       
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

                    $http.post('api/public/order/insertPositions',order_data_insert).success(function(result) {

                    });
                } 
                else {


                    var order_data = {};
                    order_data.table ='order_positions'
                    order_data.data =value
                    order_data.cond ={id:value.id}
                    $http.post('api/public/order/updatePositions',order_data).success(function(result) {

                    });
                }
            });

            setTimeout(function () {
                                    $('.form-control').removeClass('ng-dirty');
                                    var data = {"status": "success", "message": "Order position details has been updated"}
                                    notifyService.notify(data.status, data.message);
                                    get_order_details(order_id,client_id);
                                }, 1000);
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
                                    $('.form-control').removeClass('ng-dirty');
                                    var data = {"status": "success", "message": "Orderline details has been updated"}
                                    notifyService.notify(data.status, data.message);
                                    get_order_details(order_id,client_id);
                                }, 1000);
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


    $scope.openOrderPlacement = function (page,id,position_index) {


if (id) {


$scope.position_id = id;   
        
        getPDataByPosService.getPlacementDataBySizeGroup().then(function(result){
           
            if(result.data.data.success == '1') 
              {
                  $scope.size_group =result.data.data.records;
                  $scope.position_index =position_index;

              } 
              else
              {
                  $scope.size_group=[];
              }
        });


        getPDataByPosService.getPlacementDataByPosition(id).then(function(result){
           

            if(result.data.data.success == '1') 
              {
                  $scope.positiondata =result.data.data.records;

              } 
              else
              {
                  $scope.positiondata=[];
              }
        });

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
        

         $scope.saveSizeGroup=function(savePositionDataAll)
        {
           
           modalInstance.dismiss('cancel');
           
        };



    } else {
     alert("Please select position.");return false;
    }
    };



    $scope.openPriceGrid=function(gridid)
    {
       
        var url = $state.href('setting.priceedit', {id: gridid});
        window.open(url,'_blank');

    }


     $scope.openCompany=function(companyid)
    {
       
        var url = $state.href('client.edit', {id: companyid});
        window.open(url,'_blank');

    }

    $scope.addCompany=function()
    {
        $scope.refurl = "order";
        var modalInstance = $modal.open({
                                        animation: $scope.animationsEnabled,
                                        templateUrl: 'views/front/client/add.html',
                                        scope: $scope,
                                        size: 'lg',
                                        controller:'clientAddCtrl'
        });


        modalInstance.result.then(function (selectedItem) {
            $scope.selected = selectedItem;
        }, function () {
            $log.info('Modal dismissed at: ' + new Date());
        });

        $scope.ok = function (orderData) {

        };

        $scope.closePopup = function (cancel)
        {
            modalInstance.dismiss('cancel');
        };


        $scope.AddClientData = function (client) {

                            var company_post = client;
                            if(client.client_company!='')
                            {
                              $http.post('api/public/client/addclient',company_post).success(function(Listdata) {
                                      if(Listdata.data.success=='1')
                                       {

                                          modalInstance.dismiss('cancel');


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




                                          client_id_new = Listdata.data.data;


                                            var order_data = {};
                                            order_data.table ='orders'
                                            order_data.data ={client_id:client_id_new}
                                            order_data.cond ={id:order_id}
                                            
                                            $http.post('api/public/common/UpdateTableRecords',order_data).success(function(result) {
                                                 get_order_details(order_id,client_id_new);
                                            });



                                       }  
                              });
                            }
                            
          }
    }





    $scope.saveButtonData=function(newVal,ddd)
    {
        
            var buttonData = {};
            buttonData.data = newVal;
            buttonData.order_id = $stateParams.id;

            $http.post('api/public/order/saveButtonData',buttonData).success(function(Listdata) {
                
            });
            
    }

    
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
        Note_data.data = NoteDetails;
        Note_data.note_id = NoteDetails;
        $http.post('api/public/client/EditCleintNotes',Note_data).success(function(Listdata) {
            //getNotesDetail(order_id );
        });
    };

    $scope.editOrderPopup = function (id) {
        
        getOrderDetailById(id);
        $scope.edit='edit';
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
            updateNoteData.data = updateNote;
            $http.post('api/public/order/updateOrderNotes',updateNoteData).success(function(Listdata) {
                getNotesDetail(order_id );
            });
            modalInstanceEdit.dismiss('cancel');
        };
    };

    $scope.showOlDiv = function(id)
    {
        var popupBoxDiv = $('#orderLinepopup');
        
       /* var linkAddr = $('.OL_id_'+id);
        var linkPosTop = linkAddr.offset().top ; //- 150
        var linkPosLeft = linkAddr.offset().left ; // - 226

        var finalPosTop = parseInt(linkPosTop) - 150; 
        var finalPosLeft = parseInt(linkPosLeft) - 226; 

        alert('top:' + linkPosTop + ' Left:' +linkPosLeft);
        popupBoxDiv.css({ 
            'top':finalPosTop+'px',
            'left':finalPosLeft+'px'
        }); */

        angular.forEach($scope.orderLineAll, function(value) {
            
            if(value.id == id)
            {
                $scope.avg_garment_cost = '$'+value.avg_garment_cost;
                $scope.markup_default = '$'+value.markup_default;
                $scope.avg_garment_price = '$'+value.avg_garment_price;
                $scope.print_charges = '$'+value.print_charges;
                $scope.order_line_charge = '$'+value.order_line_charge;
            }
            if(value.id == undefined)
            {
                $scope.avg_garment_cost = '0';
                $scope.markup_default = '0';
                $scope.avg_garment_price = '0';
                $scope.print_charges = '0';
                $scope.order_line_charge = '0';
            }
            
            $scope.orderLineAllNew = value;
        });

        popupBoxDiv.show();
    }

    $scope.closeOLDiv = function()
    {
        var popupBoxDiv = $('#orderLinepopup');
        popupBoxDiv.hide();
    }

    $scope.update_order_charge = function(order_id,field)
    {
        $scope.order.order_charges_total = parseFloat($scope.order.separations_charge) + parseFloat($scope.order.rush_charge) + parseFloat($scope.order.shipping_charge) + parseFloat($scope.order.setup_charge) + parseFloat($scope.order.distribution_charge) + parseFloat($scope.order.artwork_charge) + parseFloat($scope.order.discount) + parseFloat($scope.order.digitize_charge);

        $scope.order.sales_order_total = parseFloat($scope.order.order_line_total) + parseFloat($scope.order.order_charges_total);

        $scope.order_data = {};

        if(field == 'separations_charge')
        {
            value = $scope.order.separations_charge;
            $scope.order_data.data = {'separations_charge' : value,
                                    'order_line_total' : $scope.order.order_line_total,
                                    'order_charges_total' : $scope.order.order_charges_total,
                                    'sales_order_total' : $scope.order.sales_order_total
                                    };
        }
        if(field == 'rush_charge')
        {
            value = $scope.order.rush_charge;
            $scope.order_data.data = {'rush_charge' : value,'order_line_total' : $scope.order.order_line_total,
                                    'order_charges_total' : $scope.order.order_charges_total,
                                    'sales_order_total' : $scope.order.sales_order_total
                                    };
        }
        if(field == 'shipping_charge')
        {
            value = $scope.order.shipping_charge;
            $scope.order_data.data = {'shipping_charge' : value,'order_line_total' : $scope.order.order_line_total,
                                    'order_charges_total' : $scope.order.order_charges_total,
                                    'sales_order_total' : $scope.order.sales_order_total
                                    };
        }
        if(field == 'setup_charge')
        {
            value = $scope.order.setup_charge;
            $scope.order_data.data = {'setup_charge' : value,'order_line_total' : $scope.order.order_line_total,
                                    'order_charges_total' : $scope.order.order_charges_total,
                                    'sales_order_total' : $scope.order.sales_order_total
                                    };
        }
        if(field == 'distribution_charge')
        {
            value = $scope.order.distribution_charge;
            $scope.order_data.data = {'distribution_charge' : value,'order_line_total' : $scope.order.order_line_total,
                                    'order_charges_total' : $scope.order.order_charges_total,
                                    'sales_order_total' : $scope.order.sales_order_total
                                    };
        }
        if(field == 'digitize_charge')
        {
            value = $scope.order.digitize_charge;
            $scope.order_data.data = {'digitize_charge' : value,'order_line_total' : $scope.order.order_line_total,
                                    'order_charges_total' : $scope.order.order_charges_total,
                                    'sales_order_total' : $scope.order.sales_order_total
                                    };
        }
        if(field == 'artwork_charge')
        {
            value = $scope.order.artwork_charge;
            $scope.order_data.data = {'artwork_charge' : value,'order_line_total' : $scope.order.order_line_total,
                                    'order_charges_total' : $scope.order.order_charges_total,
                                    'sales_order_total' : $scope.order.sales_order_total
                                    };
        }
        if(field == 'discount')
        {
            value = $scope.order.discount;
            $scope.order_data.data = {'discount' : value,'order_line_total' : $scope.order.order_line_total,
                                    'order_charges_total' : $scope.order.order_charges_total,
                                    'sales_order_total' : $scope.order.sales_order_total
                                    };
        }
        
        $scope.order_data.cond = {id: order_id};
        $scope.order_data['table'] ='orders'
        $http.post('api/public/common/UpdateTableRecords',$scope.order_data).success(function(result) {

        });
    }

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

app.factory('getPDataByPosService', function($http){
    return{
        getPlacementDataBySizeGroup: function(){
          var miscData = {};
          miscData.table ='misc_type'
          miscData.cond ={status:1,is_delete:1,type:'size_group'}
          miscData.notcond ={value:""}
          return $http.post('api/public/common/GetTableRecords',miscData);
        },

        getPlacementDataByPosition: function(id){
          var miscData = {};
          miscData.table ='placement'
          miscData.cond ={status:1,is_delete:1,misc_id:id}
          return $http.post('api/public/common/GetTableRecords',miscData);
        }


        
    };
});
