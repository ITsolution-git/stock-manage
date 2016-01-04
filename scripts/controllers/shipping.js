app.controller('shippingListCtrl', ['$scope','$rootScope','$http','$location','$state','$filter','$modal','AuthService','$log','AllConstant', function($scope,$rootScope,$http,$location,$state,$filter,$modal,AuthService,$log,AllConstant) {

    $("#ajax_loader").show();

    var company_id = $rootScope.company_profile.company_id;
    var login_id = $scope.app.user_id;
   
                
    $http.post('api/public/shipping/listShipping',company_id).success(function(Listdata) {
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


    function get_company_data()
    {
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
     }

    $scope.openpopup = function () {

        get_company_data();

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

app.controller('shippingEditCtrl', ['$scope','$rootScope','$http','logger','notifyService','$location','$state','$stateParams','$modal','AuthService','$log','sessionService','AllConstant', function($scope,$rootScope,$http,logger,notifyService,$location,$state,$stateParams,$modal,AuthService,$log,sessionService,dateWithFormat,AllConstant) {

    $('.tab2').tab('show');
    $scope.shipping_id = $stateParams.id;
    $scope.order_id = $stateParams.order_id;
    $scope.client_id = $stateParams.client_id;

    $scope.address_id = '0';
    $scope.box_id = '0';
    
    var company_id = $rootScope.company_profile.company_id;
    var AJloader = $("#ajax_loader");

    get_distribution_list();
    get_shipping_details();
    
    function get_shipping_details()
    {
        AJloader.show();
        var shipping_arr = {};
        shipping_arr.client_id = $stateParams.client_id;
        shipping_arr.order_id = $stateParams.order_id;
        shipping_arr.address_id = $scope.address_id;
        shipping_arr.shipping_id = $scope.shipping_id;

        $http.post('api/public/shipping/shippingDetail',shipping_arr).success(function(result, status, headers, config) {
        
            if(result.data.success == '1') {
                $scope.shipping =result.data.records[0];
                $scope.shipping_type =result.data.shipping_type;
                $scope.shipping_items =result.data.shippingItems;
                $scope.shipping_boxes =result.data.shippingBoxes;
            }
            AJloader.hide();
        });
    }

    $http.get('api/public/shipping/getShippingOrders').success(function(result) {
        
        if(result.data.success == '1') 
        {
            $scope.allorders =result.data.records;
        } 
        else
        {
            $scope.allorders=[];
        }
    });

    function get_box_items(id)
    {
        AJloader.show();
        var box_arr = {};
        box_arr.box_id = id;
        $http.post('api/public/shipping/getBoxItems',box_arr).success(function(result, status, headers, config) {
        
            if(result.data.success == '1') {
                $scope.boxing_items =result.data.boxingItems;
            }
            AJloader.hide();
        });
    }

    $http.get('api/public/common/getAllMiscDataWithoutBlank').success(function(result, status, headers, config) {
              $scope.miscData = result.data.records;
    });

    function get_distribution_list()
    {
        var combine_array_id = {};
        combine_array_id.client_id = $stateParams.client_id;
        combine_array_id.order_id = $stateParams.order_id;
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
    
    $scope.add_address_to_distribute = function(address_id)
    {
        $("#ajax_loader").show();
        var address_data = {};
        address_data.order_id = $scope.order_id;
        address_data.address_id = address_id;

        $http.post('api/public/order/addToDistribute',address_data).success(function(result, status, headers, config) {
            $scope.closePopup('cancel')
            get_distribution_list();
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
    $scope.select_box = function(id)
    {
        $("#ajax_loader").show();
        $scope.box_id = id;
        get_box_items(id);
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

    $scope.openTab = function(tab_name){
       if(tab_name == 'distribution'){
        get_distribution_list($scope.order_id,$scope.client_id);

       } else if(tab_name == 'purchaseorder') {
        get_po_detail($scope.order_id,$scope.client_id);

       } else if(tab_name == 'notes') {
        getNotesDetail($scope.order_id);
       } else if((tab_name == 'orderline')){
            angular.forEach($scope.orderLineAll, function(value) {
                    $scope.calculate_all(value.id);
            });
       }
       else if(tab_name == 'tasks') {
            get_task_list($scope.order_id);
       }
    }

    $scope.updateDistributedQty = function(id,qty)
    {
        $("#ajax_loader").show();
        var task = {};
        task.id = id;
        task.qty = qty;

        $http.post('api/public/order/updateDistributedQty',task).success(function(result, status, headers, config) {
            get_distribution_list($scope.order_id,$scope.client_id);
            $("#ajax_loader").hide();
        });
        
    }

    $scope.openAddressPopup = function () {

        var modalInstance = $modal.open({
                            templateUrl: 'views/front/shipping/add_address.html',
                            scope : $scope,
                        });

        modalInstance.result.then(function (selectedItem) {
            $scope.selected = selectedItem;
        }, function () {
        });

        $scope.closePopup = function (cancel)
        {
            modalInstance.dismiss('cancel');
        };

        $scope.updateTask=function()
        {
            modalInstance.dismiss('cancel');

            var order_main_data = {};
            order_main_data.data = $scope.task_detail;
            order_main_data.cond = {id:id};
            order_main_data.action = 'update';

            $http.post('api/public/order/updateOrderTask',order_main_data).success(function(result) {
                var data = {"status": "success", "message": "Order Task has been updated"}
                notifyService.notify(data.status, data.message);
                get_task_list($scope.order_id);
            });
        };
    }
    $scope.updateShippedQnty = function(id,qnty) {
        $("#ajax_loader").show();
        $scope.ship_data = {};
        $scope.ship_data['table'] ='distribution_detail';
        $scope.ship_data.data = {'shipped_qnty' : qnty};
        $scope.ship_data.cond = {id: id};
        $http.post('api/public/common/UpdateTableRecords',$scope.ship_data).success(function(result) {
            $("#ajax_loader").hide();
        });
    }
    $scope.updateShippingAll = function($event,id,table_name,match_condition)
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
                //get_shipping_details();
            });
    }
    $scope.create_box_shipment = function(shipping_items)
    {
        $http.post('api/public/shipping/CreateBoxShipment',shipping_items).success(function(result) {

            if(result.data.success == '1') {
                var data = {"status": "success", "message": "Data Updated Successfully."}
            }
            else
            {
                var data = {"status": "error", "message": "Delete all boxes in the boxes tab to rebox shipment."}
            }
            notifyService.notify(data.status, data.message);
        });
    }
}]);