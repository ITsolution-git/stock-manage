app.controller('orderListCtrl', ['$scope','$rootScope','$http','$location','$state','$filter','$modal','AuthService','$log','AllConstant', function($scope,$rootScope,$http,$location,$state,$filter,$modal,AuthService,$log,AllConstant) {


          $("#ajax_loader").show();

            var company_id = $rootScope.company_profile.company_id;
            var login_id = $scope.app.user_id;
   
                
    $http.post('api/public/order/listOrder',company_id).success(function(Listdata) {
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
    companyData.cond ={status:1,is_delete:1,company_id:company_id}
    
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

            var order_data = {};
            orderData.company_id =company_id;
            orderData.login_id =login_id;

            
            order_data.data = orderData;
           
            order_data.data.created_date = $filter('date')(new Date(), 'yyyy-MM-dd');;

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

app.controller('orderEditCtrl', ['$scope','$rootScope','$http','logger','notifyService','$location','$state','$stateParams','$modal','getPDataByPosService','AuthService','$log','sessionService','AllConstant', function($scope,$rootScope,$http,logger,notifyService,$location,$state,$stateParams,$modal,getPDataByPosService,AuthService,$log,sessionService,dateWithFormat,AllConstant) {

    var order_id = $stateParams.id
    $scope.order_id = $stateParams.id
    var client_id = $stateParams.client_id
    $scope.client_id = $stateParams.client_id
    $scope.address_id = '0';
    
    var company_id = $rootScope.company_profile.company_id;
     var AJloader = $("#ajax_loader");

    get_order_details(order_id,client_id,company_id);
    get_po_detail(order_id,client_id);
    get_distribution_list(order_id,client_id);

    function get_po_detail(order_id,client_id)
    {
        if($stateParams.id && $stateParams.client_id) {
            var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            combine_array_id.client_id = $stateParams.client_id;

            $http.post('api/public/order/PODetail',combine_array_id).success(function(result, status, headers, config) {
            
                if(result.data.success == '1') {
                  $scope.order_po_data = result.data.order_po_data;
                } else {
                   
                }
            });
        }
    }

    function get_distribution_list(order_id,client_id)
    {
        if($stateParams.id && $stateParams.client_id) {
            var combine_array_id = {};
            combine_array_id.client_id = $stateParams.client_id;
            combine_array_id.order_id = $stateParams.id;
            combine_array_id.address_id = $scope.address_id;

            $http.post('api/public/order/distributionDetail',combine_array_id).success(function(result, status, headers, config) {
            
                if(result.data.success == '1') {
                    $scope.dist_addr =result.data.dist_addr;
                    $scope.items =result.data.order_items;
                    $scope.distributed_items =result.data.distributed_items;
                    $scope.distributed_address =result.data.distributed_address;
                }
            });
        }
    }

    function get_order_details(order_id,client_id,company_id)
    {
       
        if($stateParams.id && $stateParams.client_id && company_id != 0 && company_id) {

            var combine_array_id = {};
            combine_array_id.id = $stateParams.id;
            combine_array_id.client_id = $stateParams.client_id;
            combine_array_id.company_id = company_id;
            
            $("#ajax_loader").show();

            $http.post('api/public/order/orderDetail',combine_array_id).success(function(result, status, headers, config) {
            
                if(result.data.success == '1') {
               
                    $scope.order = result.data.records[0];
                    $scope.client_data = result.data.client_data;
                    $scope.client_main_data = result.data.client_main_data;
                    $scope.orderPositionAll = result.data.order_position;
                    $scope.orderLineAll = result.data.order_line;
                    $scope.order_items = result.data.order_item;
                    $scope.orderTaskAll = result.data.order_task;
                   // $scope.order_po_data = result.data.order_po_data;

                    $scope.price_grid =result.data.price_grid[0];
                    $scope.allGrid =result.data.price_grid;
                    $scope.price_grid_markup =result.data.price_garment_mackup;
                    $scope.price_screen_primary =result.data.price_screen_primary;
                    $scope.price_screen_secondary =result.data.price_screen_secondary;
                    $scope.price_direct_garment =result.data.price_direct_garment;
                    $scope.embroidery_switch_count =result.data.embroidery_switch_count;

                    $scope.vendors = result.data.vendors;
                    $scope.allCompany =result.data.client;
                    $scope.allProduct =result.data.products;
                    $scope.staffList =result.data.staff;
                    $scope.brandCoList =result.data.brandCo;

                    $scope.orderline_id = 0;
                    $scope.total_qty = 0;
                    $scope.order.order_line_total = 0;
                    var order_line_total = 0;

                    angular.forEach($scope.orderLineAll, function(value) {
                        $scope.orderline_id = parseInt(value.id);
                        $scope.total_qty += parseInt(value.qnty);
                        order_line_total += parseFloat(value.per_line_total);
                    });

                    $scope.order.order_line_total = order_line_total.toFixed(2);

                    $scope.position_total_qty = 0;
                    $scope.pos_total_qty = 0;
                    angular.forEach($scope.orderPositionAll, function(value) {
                        $scope.position_total_qty += parseInt(value.qnty);
                        $scope.pos_total_qty = parseInt(value.qnty);
                    });

                    $("#ajax_loader").hide();
                }
                else {
                    $state.go('order.list');
                    $("#ajax_loader").hide();
                }
            });
        }
    }

    
    $scope.modalInstanceEdit  ='';
    $scope.CurrentUserId =  sessionService.get('user_id');
    $scope.CurrentController=$state.current.controller;

    $http.get('api/public/common/getAllMiscDataWithoutBlank').success(function(result, status, headers, config) {
              $scope.miscData = result.data.records;
    });

    $scope.addOrder = function(){
        
        var single_position = { position_id:'' ,description:'', placement_type:'', color_stitch_count:'', qnty:'',discharge_qnty:''
                                                    ,speciality_qnty:'', foil_qnty:'', ink_charge_qnty:'', number_on_light_qnty:'',number_on_dark_qnty:''
                                                  ,oversize_screens_qnty:'', press_setup_qnty:'', screen_fees_qnty:'', dtg_size:'',dtg_on:''};
        $scope.orderPositionAll.push(single_position);

        $scope.addPosition(single_position);
    }

    $scope.assign_item = function(order_id,order_item_id,item_name,item_charge) {

        var Selected = $('#item_'+order_item_id);

        if(Selected.hasClass('chargesApplyActive'))
        {
            $("#ajax_loader").show();
            Selected.removeClass('chargesApplyActive')

            angular.forEach($scope.orderLineAll, function(value) {
                var subtract = parseFloat(value.peritem) - parseFloat(item_charge);
                value.peritem = subtract.toFixed(2);
            });

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
                var sum = parseFloat(value.peritem) + parseFloat(item_charge);
                value.peritem = sum.toFixed(2);
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
            for(i=1;i<=7;i++)
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
            for(i=1;i<=7;i++)
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



        $scope.calculate_all(id);
    }

    $scope.update_override = function(override_value,orderline_id) {

        if(override_value > '0')
        {
            angular.forEach($scope.orderLineAll, function(value) {
                
                if(override_value != '' || override_value != '0')
                {
                    var subtract = parseFloat(override_value) - parseFloat(value.peritem);
                    value.override_diff = subtract.toFixed(2);
                    value.peritem = override_value;

                    var mul = parseInt(value.qnty) * parseFloat(value.peritem);
                    value.per_line_total = mul.toFixed(2);

                    var orderline_data = {};
                    orderline_data.data = {
                                            'peritem' : value.peritem,
                                            'override': override_value,
                                            'per_line_total' : value.per_line_total,
                                            'override_diff' : value.override_diff
                                        };
                    orderline_data.cond = {};
                    orderline_data['table'] ='order_orderlines';
                    orderline_data.cond['id'] = orderline_id;
                    $http.post('api/public/common/UpdateTableRecords',orderline_data).success(function(result) {

                    });
                }
            });
        }
        else
        {
            $scope.calculate_all(orderline_id);
        }
        
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
            setTimeout(function () {
                angular.forEach($scope.orderLineAll, function(value) {
                    $scope.calculate_all(value.id);
                });                    
            }, 300);
        }
    }

    $scope.addOrderLine = function() {
        
        $scope.orderline_id = parseInt($scope.orderline_id + 1);

        var single_line = { size_group_id:'' ,
                                    product_id:'',
                                    vendor_id:'',
                                    color_id:'',
                                    client_supplied:'0',
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
                                    override:'0',
                                    peritem:'0',
                                    per_line_total:'0'
                                }

        $scope.orderLineAll.push(single_line);
        
        $scope.insertOrderLine(single_line);
    }

    $scope.removeOrderLine = function(index,id) {

        var permission = confirm("Are you sure want to delete this record ? Clicking Ok will delete record permanently.");
        if (permission == true) {
  
            if(angular.isUndefined(id)) {
                $scope.orderLineAll.splice(index,1);
            } 
            else {

                var order_data = {};
                order_data = {id:id}
                $http.post('api/public/order/deleteOrderLine',order_data).success(function(result) {
                    
                    var data = {"status": "success", "message": "Orderline has been deleted"}
                    notifyService.notify(data.status, data.message);
                });

                $scope.orderLineAll.splice(index,1);
            }
            setTimeout(function () {
                angular.forEach($scope.orderLineAll, function(value) {
                    $scope.calculate_all(value.id);
                });                    
            }, 300);
        }
    }

    $scope.addPosition = function(postArray)
    {
        order_id = $stateParams.id;
        client_id = $stateParams.client_id;

        var order_data_insert  = {};
        postArray.order_id = order_id;
        order_data_insert.data = postArray;
        order_data_insert.table ='order_positions'

        $http.post('api/public/order/insertPositions',order_data_insert).success(function(result) {

        });

        get_order_details(order_id,client_id,company_id);
    }
    $scope.updatePosition = function(postArray,position_id)
    {
        angular.forEach($scope.orderPositionAll, function(value) {

            if(value.id == position_id)
            {
                var order_data = {};
                order_data.table ='order_positions'
                order_data.data =value
                order_data.cond ={id:value.id}
                $http.post('api/public/order/updatePositions',order_data).success(function(result) {

                });
                angular.forEach($scope.orderLineAll, function(value) {
                    $scope.calculate_all(value.id);
                });
            }
        });

        $scope.calculate_charge();
    }
    $scope.savePositionData=function(postArray)
    {
        $scope.position_qty = 0;
        $scope.press_setup_qnty = 0;
        $scope.screen_fees_qnty = 0;

        if($scope.orderPositionAll.length > 0)
        {
            angular.forEach($scope.orderPositionAll, function(value) {
                
                if(value.press_setup_qnty == '') {
                    value.press_setup_qnty = 0;
                }
                if(value.screen_fees_qnty == '') {
                    value.screen_fees_qnty = 0;
                }
                if(value.qnty == '') {
                    value.qnty = 0;
                }

                $scope.press_setup_qnty += parseInt(value.press_setup_qnty);
                $scope.screen_fees_qnty += parseInt(value.screen_fees_qnty);
                $scope.position_qty += parseInt(value.qnty);
            });

            if($scope.screen_fees_qnty > 0)
            {
                $scope.order.screen_charge = parseFloat($scope.price_grid.screen_fees) * parseInt($scope.screen_fees_qnty);
            }
            if($scope.press_setup_qnty > 0)
            {
                $scope.order.press_setup_charge = parseFloat($scope.price_grid.press_setup) * parseInt($scope.press_setup_qnty);
            }

            $scope.order.grand_total = parseFloat($scope.order.screen_charge) + parseFloat($scope.order.press_setup_charge) + parseFloat($scope.order.order_line_total) + parseFloat($scope.order.order_total);

            var order_data = {};
            order_data.table ='orders'
            order_data.data ={screen_charge:$scope.order.screen_charge,press_setup_charge:$scope.order.press_setup_charge,grand_total:$scope.order.grand_total}
            order_data.cond ={id:$scope.order_id}
            
            $http.post('api/public/common/UpdateTableRecords',order_data).success(function(result) {
            });
        }

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
                                    get_order_details(order_id,client_id,company_id);
                                }, 1000);
        }
    }

    $scope.insertOrderLine = function(postArray)
    {
        $("#ajax_loader").show();

        var order_data_insert  = {};
        postArray.order_id = order_id;
        order_data_insert.data = postArray;
        order_data_insert.table ='order_orderlines';

        $http.post('api/public/order/orderLineAdd',order_data_insert).success(function(result) {
        });

        setTimeout(function () {
                                $('.form-control').removeClass('ng-dirty');
                                $("#ajax_loader").hide();
                                get_order_details(order_id,client_id,company_id);
        }, 500);
    }
    $scope.updateOrderLine = function(postArray,orderline_id)
    {
        $("#ajax_loader").show();
        
        angular.forEach(postArray, function(value, key) {

            if(value.id == orderline_id)
            {
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
                                $("#ajax_loader").hide();
        }, 500);
    }

     $scope.updateOrderAll = function($event,id,table_name,match_condition)
    {
          var order_main_data = {};
          order_main_data.table =table_name;
          $scope.name_filed = $event.target.name;
          var obj = {};
          obj[$scope.name_filed] =  $event.target.value;
          order_main_data.data = angular.copy(obj);


          var condition_obj = {};
          condition_obj[match_condition] =  id;
          order_main_data.cond = angular.copy(condition_obj);
          

            $http.post('api/public/common/UpdateTableRecords',order_main_data).success(function(result) {

                var data = {"status": "success", "message": "Data Updated Successfully."}
                    notifyService.notify(data.status, data.message);
            });
      
    }


    $scope.removeOrderPO = function(index,id) {

        var permission = confirm("Are you sure want to delete this record ? Clicking Ok will delete record permanently.");
        if (permission == true) {
  
                var order_data = {};
                order_data.table ='purchase_order'
                order_data.cond ={po_id:id}
                $http.post('api/public/common/DeleteTableRecords',order_data).success(function(result) {
                    
                    var data = {"status": "success", "message": "Purchase Order has been deleted"}
                    notifyService.notify(data.status, data.message);
                });

                $scope.order_po_data.splice(index,1);
           
        }
    }


    $scope.removePOLine = function(index,id) {

        var permission = confirm("Are you sure want to delete this record ? Clicking Ok will delete record permanently.");
        if (permission == true) {
  
                var order_data = {};
                order_data.table ='purchase_order_line'
                order_data.cond ={id:id}
                $http.post('api/public/common/DeleteTableRecords',order_data).success(function(result) {
                    
                    var data = {"status": "success", "message": "Purchase Order Line has been deleted"}
                    notifyService.notify(data.status, data.message);
                });

                $scope.ArrPoLine.splice(index,1);
           
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

        if (id != 0) {

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
        }
        else {
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
                                                 get_order_details(order_id,client_id_new,company_id);
                                            });



                                       }  
                              });
                            }
                            
          }
    }





    $scope.saveButtonData=function(textdata)
    {
         
        if(textdata == 'cp') {
            

            var log = [];
                angular.forEach($scope.orderPositionAll, function(value, key) {
                   
                  this.push(value.placement_type);
                }, log);

                if(log.indexOf('43') <= -1 ) {
                     alert("Please select Screen Print in Placement Type to generate Contract Screen");
                     return false;
                                   
                }
        } else if(textdata == 'ce') {
            

            var log = [];
                angular.forEach($scope.orderPositionAll, function(value, key) {
                   
                  this.push(value.placement_type);
                }, log);

                if(log.indexOf('45') <= -1 ) {
                     alert("Please select Embroidery in Placement Type to generate Contract Embroidery");
                     return false;
                                   
                }
        }

            var po_data  = {};
            po_data.order_id = order_id;
            po_data.textdata =textdata

            $http.post('api/public/order/saveButtonData',po_data).success(function(result) {
                                get_po_detail(order_id,client_id);
                                $('.tab5').tab('show');

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
            
            if(value.orderline_id == id)
            {
                $scope.avg_garment_cost = '$'+value.avg_garment_cost;
                $scope.markup_default = value.markup+'%';
                $scope.avg_garment_price = '$'+value.avg_garment_price;
                $scope.print_charges = '$'+value.print_charges;
                $scope.order_line_charge = '$'+value.peritem;
            }
            if(value.orderline_id == undefined ||  value.orderline_id == '')
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
                                    'order_charges_total' : $scope.order.order_charges_total,
                                    'sales_order_total' : $scope.order.sales_order_total
                                    };
        }
        if(field == 'rush_charge')
        {
            value = $scope.order.rush_charge;
            $scope.order_data.data = {'rush_charge' : value,
                                    'order_charges_total' : $scope.order.order_charges_total,
                                    'sales_order_total' : $scope.order.sales_order_total
                                    };
        }
        if(field == 'shipping_charge')
        {
            value = $scope.order.shipping_charge;
            $scope.order_data.data = {'shipping_charge' : value,
                                    'order_charges_total' : $scope.order.order_charges_total,
                                    'sales_order_total' : $scope.order.sales_order_total
                                    };
        }
        if(field == 'setup_charge')
        {
            value = $scope.order.setup_charge;
            $scope.order_data.data = {'setup_charge' : value,
                                    'order_charges_total' : $scope.order.order_charges_total,
                                    'sales_order_total' : $scope.order.sales_order_total
                                    };
        }
        if(field == 'distribution_charge')
        {
            value = $scope.order.distribution_charge;
            $scope.order_data.data = {'distribution_charge' : value,
                                    'order_charges_total' : $scope.order.order_charges_total,
                                    'sales_order_total' : $scope.order.sales_order_total
                                    };
        }
        if(field == 'digitize_charge')
        {
            value = $scope.order.digitize_charge;
            $scope.order_data.data = {'digitize_charge' : value,
                                    'order_charges_total' : $scope.order.order_charges_total,
                                    'sales_order_total' : $scope.order.sales_order_total
                                    };
        }
        if(field == 'artwork_charge')
        {
            value = $scope.order.artwork_charge;
            $scope.order_data.data = {'artwork_charge' : value,
                                    'order_charges_total' : $scope.order.order_charges_total,
                                    'sales_order_total' : $scope.order.sales_order_total
                                    };
        }
        if(field == 'discount')
        {
            value = $scope.order.discount;
            $scope.order_data.data = {'discount' : value,
                                    'order_charges_total' : $scope.order.order_charges_total,
                                    'sales_order_total' : $scope.order.sales_order_total
                                    };
        }
        
        $scope.order_data.cond = {id: order_id};
        $scope.order_data['table'] ='orders'
        $http.post('api/public/common/UpdateTableRecords',$scope.order_data).success(function(result) {

        });

        $scope.calculate_charge();
    }

    $scope.calculate_all = function(orderline_id)
    {
        $("#ajax_loader").show();
        angular.forEach($scope.orderLineAll, function(value) {
            
            if(value.orderline_id == orderline_id)
            {
                $scope.line_qty = parseInt(value.qnty);
            }
        });

        $scope.color_stitch_count = 0;
        $scope.position_qty = 0;
        $scope.discharge_qnty = 0;
        $scope.speciality_qnty = 0;
        $scope.foil_qnty = 0;
        $scope.ink_charge_qnty = 0;
        $scope.number_on_dark_qnty = 0;
        $scope.number_on_light_qnty = 0;
        $scope.oversize_screens_qnty = 0;
        $scope.press_setup_qnty = 0;
        $scope.screen_fees_qnty = 0;
        $scope.screen_fees_qnty_total = 0;

        $scope.print_charges = 0;

        if($scope.orderPositionAll.length > 0)
        {
            angular.forEach($scope.orderPositionAll, function(value) {
                
                if(value.color_stitch_count == '') {
                    value.color_stitch_count = 0;
                }
                if(value.discharge_qnty == '') {
                    value.discharge_qnty = 0;
                }
                if(value.speciality_qnty == '') {
                    value.speciality_qnty = 0;
                }
                if(value.foil_qnty == '') {
                    value.foil_qnty = 0;
                }
                if(value.ink_charge_qnty == '') {
                    value.ink_charge_qnty = 0;
                }
                if(value.number_on_dark_qnty == '') {
                    value.number_on_dark_qnty = 0;
                }
                if(value.oversize_screens_qnty == '') {
                    value.oversize_screens_qnty = 0;
                }
                if(value.oversize_screens_qnty == '') {
                    value.oversize_screens_qnty = 0;
                }
                if(value.press_setup_qnty == '') {
                    value.press_setup_qnty = 0;
                }
                if(value.screen_fees_qnty == '') {
                    value.screen_fees_qnty = 0;
                    value.screen_fees_qnty_total = 0;
                }

                $scope.color_stitch_count = parseInt(value.color_stitch_count);

                angular.forEach($scope.orderPositionAll, function(position) {
                    $scope.position_qty += position.qnty;
                });
                
                $scope.discharge_qnty = parseInt(value.discharge_qnty);
                $scope.speciality_qnty = parseInt(value.speciality_qnty);
                $scope.foil_qnty = parseInt(value.foil_qnty);
                $scope.ink_charge_qnty = parseInt(value.ink_charge_qnty);
                $scope.number_on_dark_qnty = parseInt(value.number_on_dark_qnty);
                $scope.number_on_light_qnty = parseInt(value.number_on_light_qnty);
                $scope.oversize_screens_qnty = parseInt(value.oversize_screens_qnty);
                $scope.press_setup_qnty = parseInt(value.press_setup_qnty);
                $scope.screen_fees_qnty = parseInt(value.screen_fees_qnty);
                $scope.screen_fees_qnty_total += parseInt(value.screen_fees_qnty);

                if($scope.color_stitch_count == 0)
                {
                    var calc_descharge =  parseInt($scope.discharge_qnty) * parseFloat($scope.price_grid.discharge);
                    var calc_speciality =  parseInt($scope.speciality_qnty) * parseFloat($scope.price_grid.specialty);
                    var calc_foil =  parseInt($scope.foil_qnty) * parseFloat($scope.price_grid.foil);

                    var calc_ink_charge = parseFloat($scope.price_grid.ink_changes) / parseInt($scope.position_qty) * parseInt($scope.ink_charge_qnty);
                    var calc_number_on_dark = parseFloat($scope.price_grid.number_on_dark) / parseInt($scope.position_qty) * parseInt($scope.number_on_dark_qnty);
                    var calc_number_on_light = parseFloat($scope.price_grid.number_on_light) / parseInt($scope.position_qty) * parseInt($scope.number_on_light_qnty);

                    var calc_oversize =  parseInt($scope.oversize_screens_qnty) * parseFloat($scope.price_grid.over_size_screens);
                    var calc_press_setup =  parseInt($scope.press_setup_qnty) * parseFloat($scope.price_grid.press_setup);
                    var calc_screen_fees =  parseInt($scope.screen_fees_qnty) * parseFloat($scope.price_grid.screen_fees);

                    var calc_total = calc_descharge + calc_speciality + calc_foil + calc_ink_charge + calc_number_on_dark + calc_number_on_light + calc_oversize + calc_press_setup + calc_screen_fees;
                    $scope.print_charges +=  calc_total.toFixed(2);
                }
                else if($scope.color_stitch_count > 0)
                {
                    if(value.placement_type == 43)
                    {
                        angular.forEach($scope.price_screen_primary, function(primary) {
                            
                            var price_field = 'pricing_'+$scope.color_stitch_count+'c';

                            if(value.qnty >= primary.range_low && value.qnty <= primary.range_high)
                            {
                                $scope.print_charges += parseFloat(primary[price_field]);
                            }
                        });
                    }
                    else if(value.placement_type == 44)
                    {
                        angular.forEach($scope.price_screen_secondary, function(secondary) {
                            
                            var price_field = 'pricing_'+$scope.color_stitch_count+'c';

                            if(value.qnty >= secondary.range_low && value.qnty <= secondary.range_high)
                            {
                                $scope.print_charges += parseFloat(secondary[price_field]);
                            }
                        });   
                    }
                    else if(value.placement_type == 45)
                    {
                        angular.forEach($scope.embroidery_switch_count, function(embroidery) {
                            
                            var price_field = 'pricing_'+$scope.color_stitch_count+'c';

                            if($scope.color_stitch_count >= embroidery.range_low_1 && $scope.color_stitch_count <= embroidery.range_high_1)
                            {
                                $scope.switch_id = embroidery.id;
                                $scope.embroidery_field = 'pricing_1c';
                            }
                            else if($scope.color_stitch_count >= embroidery.range_low_2 && $scope.color_stitch_count <= embroidery.range_high_2)
                            {
                                $scope.switch_id = embroidery.id;
                                $scope.embroidery_field = 'pricing_2c';
                            }
                            else if($scope.color_stitch_count >= embroidery.range_low_3 && $scope.color_stitch_count <= embroidery.range_high_3)
                            {
                                $scope.switch_id = embroidery.id;
                                $scope.embroidery_field = 'pricing_3c';
                            }
                            else if($scope.color_stitch_count >= embroidery.range_low_4 && $scope.color_stitch_count <= embroidery.range_high_4)
                            {
                                $scope.switch_id = embroidery.id;
                                $scope.embroidery_field = 'pricing_4c';
                            }
                            else if($scope.color_stitch_count >= embroidery.range_low_5 && $scope.color_stitch_count <= embroidery.range_high_5)
                            {
                                $scope.switch_id = embroidery.id;
                                $scope.embroidery_field = 'pricing_5c';
                            }
                            else if($scope.color_stitch_count >= embroidery.range_low_6 && $scope.color_stitch_count <= embroidery.range_high_6)
                            {
                                $scope.switch_id = embroidery.id;
                                $scope.embroidery_field = 'pricing_6c';
                            }
                            else if($scope.color_stitch_count >= embroidery.range_low_7 && $scope.color_stitch_count <= embroidery.range_high_7)
                            {
                                $scope.switch_id = embroidery.id;
                                $scope.embroidery_field = 'pricing_7c';
                            }
                            else if($scope.color_stitch_count >= embroidery.range_low_8 && $scope.color_stitch_count <= embroidery.range_high_8)
                            {
                                $scope.switch_id = embroidery.id;
                                $scope.embroidery_field = 'pricing_8c';
                            }
                            if($scope.color_stitch_count >= embroidery.range_low_9 && $scope.color_stitch_count <= embroidery.range_high_9)
                            {
                                $scope.switch_id = embroidery.id;
                                $scope.embroidery_field = 'pricing_9c';
                            }
                            else if($scope.color_stitch_count >= embroidery.range_low_10 && $scope.color_stitch_count <= embroidery.range_high_10)
                            {
                                $scope.switch_id = embroidery.id;
                                $scope.embroidery_field = 'pricing_10c';
                            }
                            else if($scope.color_stitch_count >= embroidery.range_low_11 && $scope.color_stitch_count <= embroidery.range_high_11)
                            {
                                $scope.switch_id = embroidery.id;
                                $scope.embroidery_field = 'pricing_11c';
                            }
                            else if($scope.color_stitch_count >= embroidery.range_low_12 && $scope.color_stitch_count <= embroidery.range_high_12)
                            {
                                $scope.switch_id = embroidery.id;
                                $scope.embroidery_field = 'pricing_12c';
                            }
                        });
                        var screen_embroidery = {};
                        screen_embroidery.table ='price_screen_embroidery';
                        screen_embroidery.cond ={price_id:$scope.order.price_id,embroidery_switch_id:$scope.switch_id,is_delete:1,status:1}
                        
                        $http.post('api/public/common/GetTableRecords',screen_embroidery).success(function(result) {
                            
                            if(result.data.success == '1')
                            {
                                $scope.price_screen_embroidery =result.data.records;
                            } 
                            else
                            {
                                $scope.price_screen_embroidery=[];
                            }

                            angular.forEach($scope.price_screen_embroidery, function(embroidery) {
                            
                                if(value.qnty >= embroidery.range_low && value.qnty <= embroidery.range_high)
                                {
                                    $scope.print_charges += parseFloat(embroidery[$scope.embroidery_field]);
                                }
                            });
                        });
                    }
                    if(value.placement_type == 46)
                    {
                        if(value.dtg_size == 17 && value.dtg_on == 16){
                            var garment_field = 'pricing_1c';
                        }
                        else if(value.dtg_size == 17 && value.dtg_on == 15){
                            var garment_field = 'pricing_2c';
                        }
                        else if(value.dtg_size == 18 && value.dtg_on == 16){
                            var garment_field = 'pricing_3c';
                        }
                        else if(value.dtg_size == 18 && value.dtg_on == 15){
                            var garment_field = 'pricing_4c';
                        }
                        else if(value.dtg_size == 19 && value.dtg_on == 16){
                            var garment_field = 'pricing_5c';
                        }
                        else if(value.dtg_size == 19 && value.dtg_on == 15){
                            var garment_field = 'pricing_6c';
                        }
                        else if(value.dtg_size == 20 && value.dtg_on == 16){
                            var garment_field = 'pricing_7c';
                        }
                        else if(value.dtg_size == 20 && value.dtg_on == 15){
                            var garment_field = 'pricing_8c';
                        }
                        angular.forEach($scope.price_direct_garment, function(garment) {
                            
                            if(value.qnty >= garment.range_low && value.qnty <= garment.range_high)
                            {
                                $scope.print_charges += parseFloat(garment[garment_field]);
                            }
                        });
                    }
                }
            });
        }

        setTimeout(function () {
            
            if($scope.price_grid_markup.length > 0)
            {
                angular.forEach($scope.price_grid_markup, function(value) {

                    $scope.shipping_charge = value.percentage;
                    var garment_mackup = parseInt(value.percentage)/100;
                    var avg_garment_price = parseFloat($scope.price_grid.shipping_charge) * parseFloat(garment_mackup) + parseFloat($scope.price_grid.shipping_charge);
                    $scope.avg_garment_price = avg_garment_price.toFixed(2);
                    $scope.avg_garment_cost = parseFloat($scope.price_grid.shipping_charge);

                    $scope.per_item = parseFloat($scope.avg_garment_price) + parseFloat($scope.print_charges);
                    var line_total = parseFloat($scope.per_item) * parseInt($scope.line_qty);
                    $scope.per_line_total = line_total.toFixed(2);

                    if(parseInt($scope.position_qty) >= parseInt(value.range_low) && parseInt($scope.position_qty) <= parseInt(value.range_high))
                    {
                        var orderline_data = {};
                        orderline_data.data = {'avg_garment_cost' : $scope.avg_garment_cost,
                                                'avg_garment_price' : $scope.avg_garment_price,
                                                'print_charges' : $scope.print_charges,
                                                'peritem' : $scope.per_item,
                                                'per_line_total' : $scope.per_line_total,
                                                'markup' : $scope.shipping_charge,
                                            };
                        orderline_data.cond = {};
                        orderline_data['table'] ='order_orderlines'
                        orderline_data.cond['id'] = orderline_id;
                        $http.post('api/public/common/UpdateTableRecords',orderline_data).success(function(result) {

                        });
                    }
                });

                $scope.orderLineAllNew = [];

                $scope.order.order_line_total = 0;
                var order_line_total = 0;
                $scope.total_qty = 0;
                angular.forEach($scope.orderLineAll, function(value) {
                    
                    if(value.orderline_id == orderline_id)
                    {
                        value.avg_garment_cost = $scope.avg_garment_cost;
                        value.avg_garment_price = $scope.avg_garment_price;
                        value.print_charges = $scope.print_charges;
                        value.peritem = $scope.per_item;
                        value.per_line_total = $scope.per_line_total;
                        value.markup = $scope.shipping_charge;

                        if(value.override == '' || value.override == '0')
                        {
                            value.override_diff = '0';
                            var orderline_data = {};
                            orderline_data.data = {'override_diff' : '0'
                                                };
                            orderline_data.cond = {};
                            orderline_data['table'] ='order_orderlines'
                            orderline_data.cond['id'] = orderline_id;
                            $http.post('api/public/common/UpdateTableRecords',orderline_data).success(function(result) {

                            });
                        }
                    }
                    $scope.orderLineAllNew = value;
                    order_line_total += parseFloat(value.per_line_total);
                    $scope.total_qty += parseInt(value.qnty);
                });


                $scope.position_total_qty = 0;
                $scope.pos_total_qty = 0;
                angular.forEach($scope.orderPositionAll, function(value) {
                    $scope.position_total_qty += parseInt(value.qnty);
                    $scope.pos_total_qty = parseInt(value.qnty);
                });

                $scope.order.order_line_total = order_line_total.toFixed(2);

                var sales_order_total = parseFloat($scope.order.order_line_total) + parseFloat($scope.order.order_charges_total);
                $scope.order.sales_order_total = sales_order_total.toFixed(2);
                
                var grand_total = parseFloat($scope.order.screen_charge) + parseFloat($scope.order.press_setup_charge) + parseFloat($scope.order.order_line_total) + parseFloat($scope.order.tax);
                $scope.order.grand_total = grand_total.toFixed(2);

                var order_data = {};
                order_data.data = {'order_line_total' : $scope.order.order_line_total,'sales_order_total' : $scope.order.sales_order_total,'grand_total':$scope.order.grand_total};
                order_data.cond = {id: $scope.order_id};
                order_data['table'] ='orders'
                $http.post('api/public/common/UpdateTableRecords',order_data).success(function(result) {

                });
                $scope.updateOrderLine($scope.orderLineAll,orderline_id);
            }

        }, 500);
        $("#ajax_loader").hide();
    }

    $scope.calulate_tax = function(tax_rate)
    {
        if(tax_rate != '' && tax_rate != '0')
        {
            var cal = parseFloat($scope.order.grand_total) * parseFloat(tax_rate) / parseInt(100);
            $scope.order.tax = cal.toFixed(2);
            var grand_total = parseFloat($scope.order.grand_total) + parseFloat($scope.order.tax);
            $scope.order.grand_total = grand_total.toFixed(2);
            $scope.order.tax_rate = parseFloat(tax_rate); 
        }
        else
        {
            var grand_total = parseFloat($scope.order.screen_charge) + parseFloat($scope.order.press_setup_charge) + parseFloat($scope.order.order_line_total) + parseFloat($scope.order.tax);
            $scope.order.grand_total = grand_total.toFixed(2);
            $scope.order.tax_rate = '0';
            $scope.order.tax = '0';
        }
        var order_data = {};
        order_data.data = {'grand_total':$scope.order.grand_total,'tax_rate':$scope.order.tax_rate,'tax':$scope.order.tax};
        order_data.cond = {id: $scope.order_id};
        order_data['table'] ='orders'
        $http.post('api/public/common/UpdateTableRecords',order_data).success(function(result) {

        });
    }

    $scope.calculate_charge = function()
    {
        if($scope.orderPositionAll.length > 0)
        {
            $scope.oversize_screens_qnty = 0;
            $scope.screen_fees_qnty = 0;
            $scope.press_setup_qnty = 0;

            angular.forEach($scope.orderPositionAll, function(value) {
                
                if(value.press_setup_qnty == '') {
                    value.press_setup_qnty = 0;
                }
                if(value.screen_fees_qnty == '') {
                    value.screen_fees_qnty = 0;
                }
                if(value.qnty == '') {
                    value.qnty = 0;
                }

                $scope.oversize_screens_qnty += parseInt(value.oversize_screens_qnty);
                $scope.press_setup_qnty += parseInt(value.press_setup_qnty);
                $scope.screen_fees_qnty += parseInt(value.screen_fees_qnty);
                $scope.position_qty += parseInt(value.qnty);
            });

            $scope.oversize_screens_charge = 0;
            $scope.order.screen_charge = 0;
            $scope.order.press_setup_charge = 0;

            if($scope.oversize_screens_qnty > 0)
            {
                $scope.oversize_screens_charge = parseFloat($scope.price_grid.over_size_screens) * parseInt($scope.oversize_screens_qnty);
            }
            if($scope.screen_fees_qnty > 0)
            {
                $scope.order.screen_charge = parseFloat($scope.price_grid.screen_fees) * parseInt($scope.screen_fees_qnty);
            }
            if($scope.press_setup_qnty > 0)
            {
                $scope.order.press_setup_charge = parseFloat($scope.price_grid.press_setup) * parseInt($scope.press_setup_qnty);
            }

            var order_charges_total = parseFloat($scope.oversize_screens_charge) + parseFloat($scope.order.screen_charge) + parseFloat($scope.order.press_setup_charge);
            $scope.order.order_charges_total = parseFloat($scope.order.separations_charge) + parseFloat($scope.order.rush_charge) + parseFloat($scope.order.shipping_charge) + parseFloat($scope.order.setup_charge) + parseFloat($scope.order.distribution_charge) + parseFloat($scope.order.artwork_charge) + parseFloat($scope.order.discount) + parseFloat($scope.order.digitize_charge) + parseFloat(order_charges_total.toFixed(2));

            var sales_order_total = parseFloat($scope.order.order_charges_total) + parseFloat($scope.order.order_line_total);
            $scope.order.sales_order_total = sales_order_total.toFixed(2);

            $scope.order.grand_total = parseFloat($scope.order.screen_charge) + parseFloat($scope.order.press_setup_charge) + parseFloat($scope.order.order_line_total) + parseFloat($scope.order.order_total);

            var order_data = {};
            order_data.table ='orders'
            order_data.data ={
                                'screen_charge':$scope.order.screen_charge,
                                'press_setup_charge':$scope.order.press_setup_charge,
                                'grand_total':$scope.order.grand_total,
                                'order_charges_total':$scope.order.order_charges_total,
                                'sales_order_total':$scope.order.sales_order_total
                            }
            order_data.cond ={id:$scope.order_id}
            
            $http.post('api/public/common/UpdateTableRecords',order_data).success(function(result) {
            });
        }
    }
    $scope.add_address_to_distribute = function(address_id)
    {
        $("#ajax_loader").show();
        var address_data = {};
        address_data.order_id = $scope.order_id;
        address_data.address_id = address_id;

        $http.post('api/public/order/addToDistribute',address_data).success(function(result, status, headers, config) {
            get_distribution_list($scope.order_id,$scope.client_id);
        });
        $("#ajax_loader").hide();
    }
    $scope.remove_address_from_distribute = function(address_id)
    {
        $("#ajax_loader").show();
        var address_data = {};
        address_data.order_id = $scope.order_id;
        address_data.address_id = address_id;

        $http.post('api/public/order/removeFromDistribute',address_data).success(function(result, status, headers, config) {
            get_distribution_list($scope.order_id,$scope.client_id);
        });
        $("#ajax_loader").hide();
    }

    $scope.select_address = function(id)
    {
        $("#ajax_loader").show();
        $scope.address_id = id;
        get_distribution_list($scope.order_id,$scope.client_id);
        $("#ajax_loader").hide();
    }
    $scope.add_item_to_distribute = function(item_id)
    {
        $("#ajax_loader").show();
        if($scope.address_id > 0)
        {
            var address_data = {};
            address_data.order_id = $scope.order_id;
            address_data.address_id = $scope.address_id;
            address_data.item_id = item_id;

           $http.post('api/public/order/addToDistribute',address_data).success(function(result, status, headers, config) {
                get_distribution_list($scope.order_id,$scope.client_id);
            });
        }
        else
        {
            var data = {"status": "error", "message": "Please select your distribution address"}
            notifyService.notify(data.status, data.message);
        }
        $("#ajax_loader").hide();
    }
    $scope.remove_item_from_distribute = function(item_id)
    {
        $("#ajax_loader").show();
        var item_data = {};
        item_data.order_id = $scope.order_id;
        item_data.address_id = $scope.address_id;
        item_data.item_id = item_id;

        $http.post('api/public/order/removeFromDistribute',item_data).success(function(result, status, headers, config) {
            get_distribution_list($scope.order_id,$scope.client_id);
        });
        $("#ajax_loader").hide();
    }
    
    $scope.updateOrderTask = function($event,id,table_name,match_condition)
    {
        var order_main_data = {};
        order_main_data.table =table_name;
        $scope.name_filed = $event.target.name;
        var obj = {};
        obj[$scope.name_filed] =  $event.target.value;
        order_main_data.data = angular.copy(obj);


        var condition_obj = {};
        condition_obj[match_condition] =  id;
        order_main_data.cond = angular.copy(condition_obj);

        $http.post('api/public/order/updateOrderTask',order_main_data).success(function(result) {
            var data = {"status": "success", "message": "Order Task has been updated"}
                    notifyService.notify(data.status, data.message);
        });
      
    }

    $scope.removeOrderText = function(index,id) {

        var permission = confirm("Are you sure want to delete this record ? Clicking Ok will delete record permanently.");
        if (permission == true) {
  
                var order_data = {};
                order_data.table ='order_tasks'
                order_data.cond ={id:id}
                $http.post('api/public/common/DeleteTableRecords',order_data).success(function(result) {
                    
                    var data = {"status": "success", "message": "Order Task has been deleted"}
                    notifyService.notify(data.status, data.message);
                });

                $scope.orderTaskAll.splice(index,1);
           
        }
    }

    $scope.openTaskPopup = function (page,id,position_index) {

        if (id != 0) {

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
        }
        else {
            alert("Please select position.");return false;
        }
    };

    $scope.openTab = function(){
        get_distribution_list($scope.order_id,$scope.client_id);
    }
  // **************** NOTES TAB CODE END  ****************


  $scope.getPoAllData = function(poline_id){
                          
                    GetPoAll(poline_id);
                }

                function GetPoAll(po_id)
                           {
                                AJloader.show();
                                $http.get('api/public/order/GetPoAlldata/'+po_id ).success(function(PoData) 
                                  {
                                          
                                          $scope.ArrPoLine = PoData.data.records.poline;
                                          AJloader.hide();
                                  });
                            }

                                       
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
