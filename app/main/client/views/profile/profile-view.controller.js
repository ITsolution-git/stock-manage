(function ()
{
    'use strict';

    angular
            .module('app.client')
            .controller('ProfileViewController', ProfileViewController)
            .controller('CompanyInfo', CompanyInfo);

    /** @ngInject */
    function ProfileViewController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant)
    {
        $scope.NoImage = AllConstant.NoImage;
       // console.log($scope.NoImage);
        var vm = this;
        //Dummy models data
        vm.client_id = $stateParams.id
        vm.company_id = sessionService.get('company_id');
        $scope.company_id = sessionService.get('company_id');
        $scope.client_id = vm.client_id ;


        vm.salesDetail={
            "web":"www.website.com",
            "anniversaryDate":"2/20/2013",
            "salesPerson":"Salesperson Name",
            "defaultPriceGrid":"CS 2011 Supplied Garments Copy"
        };
        vm.tax={
            "id":123456789,
            "ratePercentage":"10.75%",
            "exempt":"No",
            "idDocument":"taxdoc.pdf"
        };
        vm.documents = [
            {"fileName": "doc.pdf", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"},
            {"fileName": "doc.pdf", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"},
            {"fileName": "doc.pdf", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"},
            {"fileName": "doc.pdf", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"},
            {"fileName": "doc.pdf", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"}
        ];
        vm.notes = [
            {"createdBy": "John Smith", "notes": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"},
            {"createdBy": "John Smith", "notes": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"},
            {"createdBy": "John Smith", "notes": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"},
            {"createdBy": "John Smith", "notes": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"},
            {"createdBy": "John Smith", "notes": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"}
        ];
        vm.orders = [
            {"orderId": "31", "orderName": "Shirts", "dateCreated": "2/2/1016", "approvalStatus": "Order-estimate"},
            {"orderId": "32", "orderName": "Shirts", "dateCreated": "2/2/1016", "approvalStatus": "Sales Review"},
            {"orderId": "33", "orderName": "Shirts", "dateCreated": "2/2/1016", "approvalStatus": "Production Dept"},
            {"orderId": "34", "orderName": "Shirts", "dateCreated": "2/2/1016", "approvalStatus": "Ready to Ship"},
            {"orderId": "35", "orderName": "Shirts", "dateCreated": "2/2/1016", "approvalStatus": "DTG Department", }
        ];
        vm.distributedAddress = [
            {"description": "Lorem ipsum dolor sit amet, consectetur adipisci.", "streetAddress": "123 1st St", "city": "Chicago", "state": "IL", "zipcode": "60611"},
            {"description": "Lorem ipsum dolor sit amet, consectetur adipisci.", "streetAddress": "123 1st St", "city": "Chicago", "state": "IL", "zipcode": "60611"},
            {"description": "Lorem ipsum dolor sit amet, consectetur adipisci.", "streetAddress": "123 1st St", "city": "Chicago", "state": "IL", "zipcode": "60611"},
            {"description": "Lorem ipsum dolor sit amet, consectetur adipisci.", "streetAddress": "123 1st St", "city": "Chicago", "state": "IL", "zipcode": "60611"},
            {"description": "Lorem ipsum dolor sit amet, consectetur adipisci.", "streetAddress": "123 1st St", "city": "Chicago", "state": "IL", "zipcode": "60611"}
        ];
        vm.screenSets = [
            {"description": "1-25152", "graphicSize": "Oversized 25 x 36", "images": "chicago"},
            {"description": "1-25152", "graphicSize": "Oversized 25 x 36", "images": "chicago"},
            {"description": "1-25152", "graphicSize": "Oversized 25 x 36", "images": "chicago"},
            {"description": "1-25152", "graphicSize": "Oversized 25 x 36", "images": "chicago"}
        ];
        vm.webPortal={
            "clientUrl":"www.url.com"
        };

        vm.arts = [
            {"fileName": "screen1.png", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis at hendrerit risus.", "dateAdded": "2/2/1016"},
            {"fileName": "screen1.png", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis at hendrerit risus.", "dateAdded": "2/2/1016"},
            {"fileName": "screen1.png", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis at hendrerit risus.", "dateAdded": "2/2/1016"},
            {"fileName": "screen1.png", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis at hendrerit risus.", "dateAdded": "2/2/1016"},
            {"fileName": "screen1.png", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis at hendrerit risus.", "dateAdded": "2/2/1016"}
        ];



        vm.dtOptions = {
            dom: '<"top"f>rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };
        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
        
        vm.dtInstanceCB = dtInstanceCB;
        //vm.closeDialog = closeDialog;
        

        $scope.getClientProfile = function()
        {
            var combine_array_id = {};
            combine_array_id.client_id = vm.client_id;
            combine_array_id.company_id = vm.company_id;

            var checkSession = $resource('api/public/client/GetclientDetail',null,{
                post : {
                       method : 'post'
                       }
            });
            checkSession.post(combine_array_id,function(result) 
            {   
                if(result.data.success=='1')
                {   
                    vm.Response = result.data.records;
                    $scope.mainaddress = vm.Response.clientDetail.address;
                    $scope.salesDetails =vm.Response.clientDetail.sales;
                    $scope.maincompcontact =vm.Response.clientDetail.contact;
                    $scope.company_info =vm.Response.clientDetail.main;
                    $scope.client_tax =vm.Response.clientDetail.tax;
                    $scope.pl_imp =vm.Response.clientDetail.pl_imp;
                    $scope.AddrTypeData =vm.Response.AddrTypeData;
                    $scope.StaffList =vm.Response.StaffList;
                    $scope.ArrCleintType =vm.Response.ArrCleintType;
                    //  vm.PriceGrid = vm.Response.PriceGrid;
                    $scope.allContacts = vm.Response.allContacts;
                    $scope.allclientnotes = vm.Response.allclientnotes;
                    $scope.Arrdisposition = vm.Response.Arrdisposition;
                    $scope.Client_orders = vm.Response.Client_orders;
                    $scope.art_detail = vm.Response.art_detail;
                    $scope.addressAll=vm.Response.addressAll.result;


                    //vm.currentProjectUrl = $sce.trustAsResourceUrl(vm.main.salesweb);
                }
            });
        }
        $scope.getClientProfile();
        var checkSession = $resource('api/public/client/SelectionData/'+vm.company_id,null,{
        AjaxCall : {
           method : 'get'
           }
        });
        checkSession.AjaxCall(null,function(Response) 
        {   
            if(Response.data.success=='1')
            {   
                $scope.states_all  = Response.data.result.state;
            }
        });
        vm.editCompanyInfo = editCompanyInfo;
        vm.editCompanyConatct=editCompanyConatct;
        vm.formPopup = 'app/main/client/views/forms';
        function editCompanyInfo(ev)
        {
             var params = {};
             params = { states_all:$scope.states_all,
                        client: $scope.company_info,
                        Arrdisposition:$scope.Arrdisposition,
                        ArrCleintType:$scope.ArrCleintType,};

            open_popup(ev,params,'CompanyInfo','company_form');
        }

// ====================== GLOBAL CALL FOR GET RECORD, ADD/EDIT THEN OPEN POPUP ===========//        
        function editCompanyConatct(ev,cond,table,popup_page,cond_field,cond_value)
        {
            if(cond=='add') // CHECK CONTACT ADD/EDIT CONTIDION 
            {

                var InserArray = {}; // INSERT RECORD ARRAY
                InserArray.data = {client_id:vm.client_id};
                InserArray.table =table;            

                // INSERT API CALL
                $http.post('api/public/common/InsertRecords',InserArray).success(function(Response) 
                {   
                    if(Response.data.success=='1')
                    {   
                        // AFTER INSERT CLIENT CONTACT, GET LAST INSERTED ID WITH GET THAT RECORD
                        var companyData = {};
                        companyData.table =table;
                        companyData.cond ={id:Response.data.id}

                        var condition_obj = {};
                        condition_obj[cond_field] =  cond_value;
                        companyData.cond = angular.copy(condition_obj);
                        // GET CLIENT TABLE CALL
                        $http.post('api/public/common/GetTableRecords',companyData).success(function(result) 
                        {   
                            if(result.data.success=='1')
                            {   
                                var params = {};
                                params = { contact_arr: result.data.records[0]};
                                open_popup(ev,params,'CompanyInfo',popup_page); // OPEN POPUP FOR CONTACT
                            }
                        });
                    }
                });
            }
            else
            {
                // AFTER INSERT CLIENT CONTACT, GET LAST INSERTED ID WITH GET THAT RECORD
                var companyData = {};
                companyData.table =table;
                var condition_obj = {};
                condition_obj[cond_field] =  cond_value;
                companyData.cond = angular.copy(condition_obj);
                // GET CLIENT TABLE CALL
                $http.post('api/public/common/GetTableRecords',companyData).success(function(result) 
                {   
                    if(result.data.success=='1')
                    {  
                        var params = {};
                        params = { contact_arr: result.data.records[0]};                     
                        open_popup(ev,params,'CompanyInfo',popup_page); // OPEN POPUP FOR CONTACT
                    }
                });
            }
        }
// ====================== OPEN DYNAMIC POPUP WITH PARAMS, CONDITION AND CONTROLLER ===========//
        function open_popup(ev,params,controller,page)
        {
            $mdDialog.show({
                controllerAs: $scope,
                controller:controller,
                templateUrl: vm.formPopup+'/'+page+'.html',
                parent: angular.element($document.body),
                targetEvent: ev,
                clickOutsideToClose: false,
                    locals: {
                        Params:params,
                        event: ev
                    },
                onRemoving : $scope.getClientProfile
            });
        }
        //methods
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
        } 


        // ============= UPDATE TABLE RECORD WITH CONDITION ============= // 
        $scope.UpdateTableField = function(field_name,field_value,table_name,cond_field,cond_value,extra,param)
        {
            var vm = this;
            var UpdateArray = {};
            UpdateArray.table =table_name;
            
            $scope.name_filed = field_name;
            var obj = {};
            obj[$scope.name_filed] =  field_value;
            UpdateArray.data = angular.copy(obj);

            var condition_obj = {};
            condition_obj[cond_field] =  cond_value;
            UpdateArray.cond = angular.copy(condition_obj);

                $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
                    if(result.data.success=='1')
                    {
                       notifyService.notify('success', "Record Updated Successfully!");
                       if(extra=='contact_main') // SECOND CALL CONDITION WITH EXTRA PARAMS
                       {
                            $scope.UpdateTableField('contact_main','1',table_name,'id',param,'','');
                            $scope.getClientProfile();
                       }
                       if(extra=='address_main') // SECOND CALL CONDITION WITH EXTRA PARAMS
                       {
                            $scope.UpdateTableField('address_main','1',table_name,'id',param,'','');
                            $scope.getClientProfile();
                       }
                       if(extra=='address_shipping') // SECOND CALL CONDITION WITH EXTRA PARAMS
                       {
                            $scope.UpdateTableField('address_shipping','1',table_name,'id',param,'','');
                            $scope.getClientProfile();
                       }
                       if(extra=='address_billing') // SECOND CALL CONDITION WITH EXTRA PARAMS
                       {
                            $scope.UpdateTableField('address_billing','1',table_name,'id',param,'','');
                            $scope.getClientProfile();
                       }

                    }
                    else
                    {
                        notifyService.notify('error',result.data.message);
                    }
                   });
        }
        
// ============= REMOVE TABLE RECORD WITH CONDITION ============= // 
        $scope.RemoveFields = function(table,cond_field,cond_value){
              
                var delete_data = {};
                
                $scope.name_filed = cond_field;
                var obj = {};
                obj[$scope.name_filed] =  cond_value;
                delete_data.cond = angular.copy(obj);
                
                delete_data.table =table;
                var permission = confirm("Are you sure to delete this Record ?");
                if (permission == true) 
                {
                    $http.post('api/public/common/DeleteTableRecords',delete_data).success(function(result) 
                    {
                        if(result.data.success=='1')
                        {
                            $scope.getClientProfile();
                        }
                    });
                }
      }

// ============= UPLOAD IMAGE ============= // 
        $scope.ImagePopup = function (column_name,folder_name,table_name,default_image,primary_key_name,primary_key_value,image_name) 
        {

                $scope.column_name=column_name;
                $scope.table_name=table_name;
                $scope.folder_name=folder_name;
                $scope.primary_key_name=primary_key_name;
                $scope.primary_key_value=primary_key_value;
                $scope.default_image=default_image;
                $scope.unlink_url = image_name;

                $mdDialog.show({
                   //controllerAs: $scope,
                    controller: function($scope,params){
                            $scope.params = params;
                            $scope.SaveImageAll=function(image_array)
                            {
                                var Image_data = {};
                                Image_data.image_array = image_array;
                                Image_data.field = params.column_name;
                                Image_data.table = params.table_name;
                                Image_data.image_name = params.table_name+"-logo";
                                Image_data.image_path = params.company_id+"/"+params.folder_name+"/"+params.primary_key_value;
                                Image_data.cond = params.primary_key_name;
                                Image_data.value = params.primary_key_value;
                                Image_data.unlink_url = params.unlink_url;

                                $http.post('api/public/common/SaveImage',Image_data).success(function(result) {
                                    if(result.data.success=='1')
                                    {
                                        notifyService.notify("success", result.data.message);
                                        $mdDialog.hide();
                                    }
                                    else
                                    {
                                        notifyService.notify("error", result.data.message); 
                                    }
                                });
                            };
                            $scope.showtcprofileimg = false;
                            $scope.onLoad=function()
                                {
                                    $scope.showtcprofileimg = true;
                                }; 
                            $scope.removeProfileImage=function()
                                {
                                    $scope.showtcprofileimg = false;
                                }; 
                            $scope.closeDialog = function() 
                            {
                                $mdDialog.hide();
                            } 
                        },
                    templateUrl: 'app/main/image/image.html',
                    parent: angular.element($document.body),
                    clickOutsideToClose: false,
                        locals: {
                            params:$scope
                        },
                    onRemoving : $scope.getClientProfile
                });

        };
// ============= DELETE IMAGE ============= // 
        $scope.deleteImage=function(column_name,folder_name,table_name,default_image,primary_key_name,primary_key_value)
        {
            if(default_image == '') 
            {

                var data = {"status": "error", "message": "Please upload image first."}
                          notifyService.notify(data.status, data.message);
                          return false;
            }
              var permission = confirm("Are you sure to delete this Image ?");

            if (permission == true) {

                var image_data = {};
                image_data.table =table_name

                var obj = {};
                obj[column_name] =  '';
                image_data.data = angular.copy(obj);

                var cond_arr = {};
                cond_arr[primary_key_name] =  primary_key_value;
                image_data.cond =angular.copy(cond_arr);


                image_data.image_delete =  $scope.company_id+'/'+folder_name+'/' + primary_key_value +'/'+default_image;
            
                $http.post('api/public/common/deleteImage',image_data).success(function(result) 
                {

                    if(result.data.success=='1')
                    {
                        notifyService.notify("success", result.data.message);
                        $scope.getClientProfile();
                    }
                    else
                    {
                        notifyService.notify("error", result.data.message); 
                    }
 
                });
            }
        }
    }


    function CompanyInfo($mdDialog, $stateParams,$resource,sessionService,$scope,Params,$http,$controller,$state,notifyService)
    {
        $scope.client = Params.client;
        $scope.ArrCleintType = Params.ArrCleintType;
        $scope.Arrdisposition = Params.Arrdisposition;
        $scope.states_all = Params.states_all;
        $scope.contact_arr=Params.contact_arr;

        $scope.UpdateTableField = function(field_name,field_value,table_name,cond_field,cond_value,extra,param)
        {
            var vm = this;
            var UpdateArray = {};
            UpdateArray.table =table_name;
            
            $scope.name_filed = field_name;
            var obj = {};
            obj[$scope.name_filed] =  field_value;
            UpdateArray.data = angular.copy(obj);

            var condition_obj = {};
            condition_obj[cond_field] =  cond_value;
            UpdateArray.cond = angular.copy(condition_obj);

                $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
                    if(result.data.success=='1')
                    {
                       notifyService.notify('success', "Record Updated Successfully!");
                       if(extra=='contact_main')
                       {
                            $scope.UpdateTableField('contact_main','1','client_contact','id',param,'','');
                       }
                    }
                });
        }
        $scope.closeDialog = function() 
        {
            //$state.go($state.current, $stateParams, {reload: true, inherit: false});
            $mdDialog.hide();
        }
    }
})();