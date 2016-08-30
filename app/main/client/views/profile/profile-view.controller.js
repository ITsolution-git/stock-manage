(function ()
{
    'use strict';

    angular
            .module('app.client')
            .controller('ProfileViewController', ProfileViewController)
            .controller('CompanyInfo', CompanyInfo);
 
    /** @ngInject */
    function ProfileViewController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter)
    {

        $scope.NoImage = AllConstant.NoImage;
        $scope.NoDocument = AllConstant.NoDocument
        $scope.Current_date = AllConstant.currentdate;
        var vm = this;
        //Dummy models data
        vm.client_id = $stateParams.id;
        vm.company_id = sessionService.get('company_id');
        $scope.company_id = sessionService.get('company_id');
        $scope.client_id = vm.client_id ;


        vm.documents = [
        ];
        vm.screenSets = [
            
        ];
        vm.arts = [
            
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
            $("#ajax_loader").show();
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
                    $scope.allContacts = vm.Response.allContacts;
                    $scope.allclientnotes = vm.Response.allclientnotes;
                    $scope.Arrdisposition = vm.Response.Arrdisposition;
                    $scope.Client_orders = vm.Response.Client_orders;
                    $scope.screenset_detail = vm.Response.screenset_detail;
                    $scope.addressAll=vm.Response.addressAll.result;
                    $scope.Distribution_address= vm.Response.Distribution_address.result;
                    $scope.documents= vm.Response.documents;
                }
                $("#ajax_loader").hide();
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
                $scope.AllPriceGrid=Response.data.result.AllPriceGrid;
                $scope.approval_all = Response.data.result.approval;
            }
        });
        vm.editCompanyInfo = editCompanyInfo;
        vm.editCompanyConatct=editCompanyConatct;
        vm.formPopup = 'app/main/client/views/forms';
        function editCompanyInfo(ev,popup_page)
        {
             var params = {};
             params = $scope;
             params.client = $scope.company_info;

            open_popup(ev,params,'CompanyInfo',popup_page);
        }

        $scope.getDocument = function (ev,id)
        {
            $http.get('api/public/client/getDocumentDetailbyId/'+id+'/'+$scope.company_id).success(function(result) 
            {
                if(result.data.success == '1') 
                {
                    var params = {contact_arr: result.data.records[0], alldata:$scope};
                    open_popup(ev,params,'CompanyInfo','document_form');
                }
                else
                {
                    notifyService.notify('error',result.data.message);
                }
            });
        }
        $scope.AddDocument = function (ev)
        {
            open_popup(ev,$scope,'CompanyInfo','add_document');
        }
        $scope.EditTaxDoc = function (ev)
        {
            open_popup(ev,$scope,'CompanyInfo','tax_document');
        }



// ====================== GLOBAL CALL FOR GET RECORD, ADD/EDIT THEN OPEN POPUP ===========//        
        function editCompanyConatct(ev,operation,table,popup_page,cond_field,cond_value,extra)
        {
            if(operation=='add') // CHECK CONTACT ADD/EDIT CONTIDION 
            {
                var InserArray = {}; // INSERT RECORD ARRAY
                InserArray.data = {client_id:vm.client_id};
                if(extra=='notes')
                {
                    InserArray.data = {client_id:vm.client_id,user_id:sessionService.get('user_id'),created_date:$scope.Current_date};
                }
                InserArray.table =table;            

                // INSERT API CALL
                $http.post('api/public/common/InsertRecords',InserArray).success(function(Response) 
                {   
                    if(Response.data.success=='1')
                    {   
                        // AFTER INSERT CLIENT CONTACT, GET LAST INSERTED ID WITH GET THAT RECORD
                        var companyData = {};
                        companyData.table =table;
                        //companyData.cond ={id:Response.data.id}

                        var condition_obj = {};
                        condition_obj[cond_field] =  Response.data.id;
                        companyData.cond = angular.copy(condition_obj);
                        // GET CLIENT TABLE CALL
                        $http.post('api/public/common/GetTableRecords',companyData).success(function(result) 
                        {   
                            if(result.data.success=='1')
                            {   
                                var params = {};
                                params = { contact_arr: result.data.records[0],states_all:$scope.states_all,AddrTypeData:$scope.AddrTypeData};
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
                        params = { contact_arr: result.data.records[0],states_all:$scope.states_all,AddrTypeData:$scope.AddrTypeData};                     
                        open_popup(ev,params,'CompanyInfo',popup_page); // OPEN POPUP FOR CONTACT
                    }
                });
            }
        }
// ====================== OPEN DYNAMIC POPUP WITH PARAMS, CONDITION AND CONTROLLER ===========//
        function open_popup(ev,params,controller,page)
        {
            $("#ajax_loader").show();
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

            if(extra=='document')
            {
                var permission = confirm("Are you sure to delete this Record ?");
                if (permission == true) 
                {

                }
                else
                {
                    return false;
                }
            }

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
                       if(extra=='salesweb') // SECOND CALL CONDITION WITH EXTRA PARAMS
                       {
                            $scope.getClientProfile();
                       }
                       if(extra=='document') 
                       {
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
                            notifyService.notify('success',result.data.message);
                            $scope.getClientProfile();
                        }
                        else
                        {
                             notifyService.notify('error',result.data.message);
                        }
                    });
                }
      }
// ============= REMOVE CLIENT LOCATION TABLE RECORD WITH NO MAIN,SHIIPING,BIllING ADDRESS CONDITION ============= // 
        $scope.RemoveLocationFields = function(table,cond_field,cond_value,main,shipping,billing){
              
                var delete_data = {};
                
                $scope.name_filed = cond_field;
                var obj = {};
                obj[$scope.name_filed] =  cond_value;
                delete_data.cond = angular.copy(obj);
                
                delete_data.table =table;
                if(main==0)
                {
                    if(shipping==0)
                    {
                        if(billing==0)
                        {
                            var permission = confirm("Are you sure to delete this Record ?");
                            if (permission == true) 
                            {
                                $http.post('api/public/common/DeleteTableRecords',delete_data).success(function(result) 
                                {
                                    if(result.data.success=='1')
                                    {
                                        notifyService.notify('success',result.data.message);
                                        $scope.getClientProfile();
                                    }
                                    else
                                    {
                                         notifyService.notify('error',result.data.message);
                                    }
                                });
                            }
                        }
                        else
                        {
                            notifyService.notify('error','Please select another Billing Location first !');
                        }
                    }
                    else
                    {
                        notifyService.notify('error','Please select another Shipping Location first !');
                    }
                }
                else
                {
                    notifyService.notify('error','Please select another Main Location first !');
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
                                if(image_array == null)
                                {
                                    $mdDialog.hide();
                                    return false;
                                }

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
              var permission = confirm(AllConstant.deleteMessage);

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
        $scope.validation_message = 'Please enter valid Input.';
        $("#ajax_loader").hide();
        $scope.client = Params.client;
        $scope.ArrCleintType = Params.ArrCleintType;
        $scope.Arrdisposition = Params.Arrdisposition;
        $scope.AddrTypeData = Params.AddrTypeData;
        $scope.states_all = Params.states_all;
        $scope.contact_arr=Params.contact_arr;
        $scope.StaffList = Params.StaffList;
        $scope.salesDetails = Params.salesDetails
        $scope.AllPriceGrid = Params.AllPriceGrid;
        $scope.Distribution_address = Params.Distribution_address;
        $scope.alldata = Params.alldata;
        $scope.client_id = Params.client_id;
        $scope.company_id = Params.company_id;
        $scope.client_tax = Params.client_tax;
        $scope.UpdateTableField = function(field_name,field_value,table_name,cond_field,cond_value,extra,param,validation)
        {
            //console.log(validation);
            // console.log(Object.keys(validation).length);
            if(!angular.isUndefined(validation) && Object.keys(validation).length>0 )
            {
                notifyService.notify('error',$scope.validation_message);
                return false;
            }
            
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
            UpdateArray.date_field = extra;

                $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
                    if(result.data.success=='1')
                    {
                        notifyService.notify('success', result.data.message);
                        if(extra=='contact_main')
                        {
                            $scope.UpdateTableField('contact_main','1','client_contact','id',param,'','');
                        }
                    }
                    else
                    {
                        notifyService.notify('error', result.data.message);
                    }
                });
        }
        $scope.saveDocument = function(saveDocDetails)
        {
            $("#ajax_loader").show();
            var doc_data = {};
              doc_data.data = saveDocDetails;
              doc_data.data.client_id = $scope.alldata.client_id;
              doc_data.data.company_id = $scope.alldata.company_id;
             
              //console.log(saveDocDetails); return false;
              $http.post('api/public/client/updateDoc',doc_data).success(function(result) 
              {
                    if(result.data.success=='1')
                    {
                        $scope.closeDialog();
                    }
                    else
                    {
                        notifyService('error',result.data.message);
                    }
                    $("#ajax_loader").hide();
              });
                                         
        }
        $scope.AddDocument = function (document_data)
        {
            $("#ajax_loader").show();
              var doc_data = {};
              doc_data.data = document_data;
              doc_data.data.client_id = $scope.client_id;
              doc_data.data.company_id = $scope.company_id;
             
              //console.log(saveDocDetails); return false;
              $http.post('api/public/client/saveDoc',doc_data).success(function(result) 
              {
                    if(result.data.success=='1')
                    {
                        $mdDialog.hide();
                    }
                    else
                    {
                        notifyService('error',result.data.message);
                    }
                    $("#ajax_loader").hide();
              });
        }

        $scope.showtcprofileimg = false;
        $scope.onLoad=function()
        {
            $scope.showtcprofileimg = true;
        }
        $scope.removeProfileImage=function()
        {
            $scope.showtcprofileimg = false;
        }
        $scope.closeDialog= function() 
        {
            $mdDialog.hide();
        }  
        $scope.SaveTaxDoc=function(taxDocDetail)
        {
             $("#ajax_loader").show();
              var doc_array = {};
              doc_array.data = taxDocDetail;
              doc_array.client_id = $scope.client_id;
              doc_array.company_id = $scope.company_id;
             
              $http.post('api/public/client/saveTaxDoc',doc_array).success(function(result) 
              {
                    if(result.data.success=='1')
                    {
                        $mdDialog.hide();
                    }
                    else
                    {
                        notifyService('error',result.data.message);
                    }
                    $("#ajax_loader").hide();
              });

        }
       
       

        }
})();