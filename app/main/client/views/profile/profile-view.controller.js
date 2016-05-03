(function ()
{
    'use strict';

    angular
            .module('app.client')
            .controller('ProfileViewController', ProfileViewController)
            .controller('CompanyInfo', CompanyInfo);

    /** @ngInject */
    function ProfileViewController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService)
    {
        var vm = this;
        //Dummy models data
        vm.client_id = $stateParams.id
        vm.company_id = sessionService.get('company_id');

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
        vm.logos={
            "blindApprovalText":"\" Please review and advise(action may be required): \" & \"¶¶\" & \"Please reply to this email with APPROVED, NAME, AND DATE. \"& \"¶¶\" & \"Dear  Valued Customer, \"& \"¶¶\" &\n"+
                    +"\"Please check the following carefully: size, layout, spelling, punctuation, colors, etc.  Changes if required should be noted on the proof when sent back.    Customer assumes full responsibility once proof is signed as approved.  Colors will be printed as close to proof as possible.  Graphics will be sized according to industry standard unless indicated by customer. \"  & \"¶¶\" &"
                    +"\n\"Please note that the JPEG proofs are for layout purposes. It is only to show how the artwork will be positioned on the garment and its size. It will not be legible to indicate on how colors, overprints, knock outs and/or transparency images will or will not appear on the prints. Any JPEG proofs we sent to you can be in low resolution to transfer the file(s) smoothly during the email process but please also check the original file(s) if they are in a good minimum of 300 dpi resolution or Vector images.\" & \"¶¶\" &",
            "colorLogo":"",
            "bwLogo":"",
            "shippingLogo":""
        }
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
                    vm.salesDetails =vm.Response.clientDetail.sales;
                    $scope.maincompcontact =vm.Response.clientDetail.contact;
                    $scope.company_info =vm.Response.clientDetail.main;
                    vm.client_tax =vm.Response.clientDetail.tax;
                    vm.pl_imp =vm.Response.clientDetail.pl_imp;
                    vm.AddrTypeData =vm.Response.AddrTypeData;
                    vm.StaffList =vm.Response.StaffList;
                    $scope.ArrCleintType =vm.Response.ArrCleintType;
                    //  vm.PriceGrid = vm.Response.PriceGrid;
                    $scope.allContacts = vm.Response.allContacts;
                    vm.allclientnotes = vm.Response.allclientnotes;
                    $scope.Arrdisposition = vm.Response.Arrdisposition;
                    vm.Client_orders = vm.Response.Client_orders;
                    vm.art_detail = vm.Response.art_detail;
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
        function editCompanyConatct(ev,cond,table,popup_page)
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
                companyData.cond ={id:cond};
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