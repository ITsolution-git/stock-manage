(function ()
{
    'use strict';

    angular
            .module('app.settings')
            .controller('CompanyProfileController', CompanyProfileController);
            

    /** @ngInject */
    function CompanyProfileController($document, $window, $timeout, $mdDialog,$stateParams,sessionService,$http,$scope,$state,notifyService,AllConstant)
    {
    	var vm = this;

        $scope.NoImage = AllConstant.NoImage;
        $scope.valid_phone = AllConstant.VALID_PHONE;
        $scope.currentyear = AllConstant.currentyear;

    
    $scope.company_id =sessionService.get("company_id");
    $scope.user_id = sessionService.get("user_id");
    $scope.role_slug = sessionService.get('role_slug');
    $scope.company_name = sessionService.get('company_name');
    

    //console.log($scope.role_slug);
    $scope.profile_id = $scope.company_id;
    if($scope.role_slug=='CA')
    {
        $scope.allow_access = 1;
    }
    else
    {
        $scope.allow_access = 0;
    }
 
    
        $scope.cancel = function () {
            $mdDialog.hide();
        };
        

       $http.get('api/public/client/SelectionData/'+$scope.company_id).success(function(Response) 
        {   
            if(Response.data.success=='1')
            {   
                $scope.states_all   = Response.data.result.state;
                $scope.AllPriceGrid = Response.data.result.AllPriceGrid;
            }
        });

       $scope.YearRange ={};
       for(var i=$scope.currentyear; i>1970; i--)
       {
            $scope.YearRange[i] = i;
       }

       //console.log($scope.YearRange);
        /**
         * Close dialog
         */
        function closeDialog()
        {
            $mdDialog.hide();
        }


    //console.log($scope.app.company_roleid);
      // GET ADMIN ROLE LIST
     
      $scope.GetCompany =  function() {
      $("#ajax_loader").show();
      $http.get('api/public/admin/company/edit/'+$scope.profile_id+'/'+$scope.company_id).success(function(Listdata) 
      {
            if(Listdata.data.success==1)
            {
              $scope.company = Listdata.data.records[0];
              $("#ajax_loader").hide();
            }
            else
            {
              notifyService.notify("error", "Something Wrong, Please try again !");
              $state.go('app.dashboard');
              $("#ajax_loader").hide();
              return false;
            }
              
             // console.log(Listdata); 
      });
    }


$scope.allShiftData =  function() {

            var allData = {};
        allData.table ='company_shift';
        allData.sort ='id';
        allData.sortcond ='desc';
        allData.cond ={is_delete:1,status:1,company_id:sessionService.get('company_id')}

        $http.post('api/public/common/GetTableRecords',allData).success(function(result)
        {   
            if(result.data.success=='1')
            {   
                $scope.allshiftData = result.data.records;
                $scope.shiftDataDisplay = 1;
            } else {
                $scope.allshiftData = {};
                $scope.shiftDataDisplay = 0;
            }     
                
        });
 }




$scope.updateiph = function(column_name,id,value,table_name,match_condition)
        {

            var position_main_data = {};
            position_main_data.table =table_name;
            $scope.name_filed = column_name;
          
            var obj = {};
            obj[$scope.name_filed] =  value;
            position_main_data.data = angular.copy(obj);

            var condition_obj = {};
            condition_obj[match_condition] =  id;
            position_main_data.cond = angular.copy(condition_obj);

            $http.post('api/public/common/UpdateTableRecords',position_main_data).success(function(result) {
               return true;
            });
        }


$scope.addiph = function(){
      
             var InserArray = {};
            InserArray.table ='iph'
            InserArray.data ={company_id:sessionService.get('company_id')}


            // INSERT API CALL
            $http.post('api/public/common/InsertRecords',InserArray).success(function(Response) 
            {
                notifyService.notify('success','Record added successfully');
                $scope.alliphDataAll();   
                
            });
        
    }


    
 $scope.removeIph =  function(id){
          
          var permission = confirm("Are you sure want to delete this record ? Clicking Ok will delete record permanently.");

            if (permission == true) {

                var combine_array_id = {};
                    combine_array_id.id = id;
                    
                    $http.post('api/public/admin/company/deleteIph',combine_array_id).success(function(result, status, headers, config) {
                       
                        if(result.data.success == '1') {
                            $scope.alliphDataAll();  
                        } 
                        
                    });
              }

        };

 $scope.alliphDataAll =  function() {

            var allData = {};
        allData.table ='iph';
        allData.sort ='id';
        allData.sortcond ='desc';
        allData.cond ={is_delete:1,status:1,company_id:sessionService.get('company_id')}

        $http.post('api/public/common/GetTableRecords',allData).success(function(result)
        {   
            if(result.data.success=='1')
            {   
                $scope.alliphData = result.data.records;
                
            } else {
                $scope.alliphData = {};
               
            }     
                
        });
 }   






   $scope.GetCompany();
   $scope.allShiftData();
   $scope.alliphDataAll();



    // COMPANY EDIT TIME CALL
    $scope.UpdateTableProfile = function(field_name,field_value,table_name,cond_field,cond_value,extra,param,validation)
    {
        //console.log(field_name); console.log(field_value);
        //console.log(Object.keys(validation).length);
        
        if($scope.allow_access=='0')
        {
            notifyService.notify('error','You have no rights to Edit.');
            return false;
        }
        if(!angular.isUndefined(validation) && Object.keys(validation).length>0 )
        {
            notifyService.notify('error','Please enter valid Input.');
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
        
        if(param=='check_email')
        {
            var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;  
            if(!emailReg.test(field_value) || field_value.trim() == '')
            {  
               return false;
            }
            else
            {
                 $http.get('api/public/common/checkemail/'+field_value+'/'+$scope.user_id).success(function(result, status, headers, config) {
          
                    if(result.data.success=='2')
                    {
                      //return false;
                    }
                    else
                    {
                       notifyService.notify('error', "Email exist, Please choose different email address.");
                       return false;
                    }
                });
            }    
        }

        $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
            if(result.data.success=='1')
            {
               notifyService.notify('success', result.data.message);
            }
            else
            {
               //notifyService.notify('error', result.data.message);
            }
        });
    }      
      $scope.btns = [{
        label: "SCREEN PRINTING",
        state: false
        }, {
            label: "EMBROIDERING",
            state: true
        }, {
            label: "PACKAGING",
            state: false
        }, {
            label: "SHIPPING",
            state: false
        }, {
            label: "ART WORK",
            state: false
        }];

    $scope.toggle = function () {
        this.b.state = !this.b.state;
    };
   
    $scope.getCompanyInfo = function (){
        $("#ajax_loader").show();
        $http.get('api/public/admin/company/getCompanyInfo/'+$scope.company_id).success(function(result) 
        {   
            if(result.data.success=='1')
            {  
                $scope.company_data = result.data.data;
                //console.log($scope.copmany_data.screen_print);
            }
            else
            {
                notifyService.notify('error',result.data.message);
            }
            $("#ajax_loader").hide();
        });
    }
    $scope.getCompanyInfo();
    $scope.UpdateTableField = function(field_name,field_value,table_name,cond_field,cond_value,extra,param)
    {
        if($scope.role_slug!='CA' && $scope.role_slug!='FM')
        {
            notifyService.notify('error','You have no rights to Edit.');
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
       // console.log(UpdateArray); return false;
        $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
            if(result.data.success=='1')
            {
                notifyService.notify('success', result.data.message);
                $scope.getCompanyInfo();
            }
            else
            {
                notifyService.notify('error', result.data.message);
            }
        });
    }

                    
    $scope.checkemail = function () {
           var mail = $('#comp_email').val();
           if(mail.trim() != '')
           {
               $http.get('api/public/common/checkemail/'+mail+'/'+$scope.user_id).success(function(result, status, headers, config) 
               {
                    if(result.data.success=='2')
                    {
                      $("#company_email").hide();
                      return false;
                    }
                    else
                    {
                      $("#company_email").val(result.data.message);
                      $("#company_email").show();
                      return false;
                    }
               });
           }
    }   


        $scope.onLoad=function()
        {
            $scope.showtcprofileimg = true;
        }; 
        // ============= UPLOAD IMAGE ============= // 
        $scope.ImagePopup = function (column_name,folder_name,table_name,default_image,primary_key_name,primary_key_value,image_name,is_drag,drag_image) 
        {

                $scope.column_name=column_name;
                $scope.table_name=table_name;
                $scope.folder_name=folder_name;
                $scope.primary_key_name=primary_key_name;
                $scope.primary_key_value=primary_key_value;
                $scope.default_image=default_image;
                $scope.unlink_url = image_name;
                $scope.is_drag = is_drag;
                $scope.drag_image = drag_image;
                ///console.log(drag_image); return false;
                if(drag_image!=null)
                {
                    $mdDialog.show({
                       //controllerAs: $scope,
                        controller: function($scope,params){
                                $scope.params = params;
                                $scope.logo_image = 'ok';
                                if($scope.params.is_drag=='image_drag' && $scope.params.drag_image!=null)
                                {
                                    $scope.logo_image = $scope.params.drag_image[0];
                                }
                                
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
                                            // IF PROFILE IMAGE CHANGED, SET API CALL.
                                            if(params.column_name=="profile_photo")
                                            {
                                                sessionService.AccessService('ALL','true','1');
                                            }
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
                        templateUrl: 'app/main/image/'+is_drag+'.html',
                        parent: angular.element($document.body),
                        clickOutsideToClose: false,
                            locals: {
                                params:$scope
                            },
                        onRemoving :  $scope.GetCompany
                    });
                }

        };

           $scope.qbClientSetup = function(){

                $("#ajax_loader").show();
                var companyId = {};

                companyId ={company_id :sessionService.get('company_id')};

                $http.post('api/public/common/AddEditClient',companyId).success(function(result) {
                    $("#ajax_loader").hide();
                            if(result != '0')
                            {
                                notifyService.notify('success',"Client Sync successfully");   
                            }
                            else
                            {
                                notifyService.notify('error',"Please connect to quickbook first");
                            }

                           

                           });
            }

            $scope.qbUpdateInovice = function(){
                $("#ajax_loader").show();
                var company_id = {};

                company_id ={company_id :sessionService.get('company_id')};

                $http.post('api/public/qbo/updateInvoicePayment',company_id).success(function(result) {
                $("#ajax_loader").hide();
                    if(result.data.success=='1')
                    {
                        notifyService.notify('success', result.data.message);
                    }
                    else if(result.data.success=='2')
                    {
                        notifyService.notify('error', result.data.message);
                    }
                    else
                    {
                        notifyService.notify('error',"Please connect to quickbook first");
                    }
                   });
            }


             $scope.delete_shift = function (ev,id)
        {
            var UpdateArray = {};
            UpdateArray.table ='company_shift';
            UpdateArray.data = {is_delete:0};
            UpdateArray.cond = {id: id};
            
            var permission = confirm(AllConstant.deleteMessage);
            if (permission == true) 
            {
                $("#ajax_loader").show();
                $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
                    if(result.data.success=='1')
                    {
                        notifyService.notify('success', "Record Deleted Successfully.");
                        $scope.allShiftData();
                    }
                    else
                    {
                        notifyService.notify('error', result.data.message);
                    }
                    $("#ajax_loader").hide();
                });
            }
        }



            $scope.company_shift = {};

            $scope.saveCompanyShift = function (companyShift) {



            if(companyShift.shift_name == undefined || companyShift.shift_name == '') {

                      var data = {"status": "error", "message": "Shift Name should not be blank"}
                              notifyService.notify(data.status, data.message);
                              return false;
            }

             


          if(companyShift.shift_start_time == undefined || companyShift.shift_start_time == '') {

                      var data = {"status": "error", "message": "Shift Start Time should not be blank"}
                              notifyService.notify(data.status, data.message);
                              return false;
            }

          if(companyShift.shift_end_time == undefined || companyShift.shift_end_time == '') {

                      var data = {"status": "error", "message": "Shift End Time should not be blank"}
                              notifyService.notify(data.status, data.message);
                              return false;
            }

          var today = new Date();

          var format_check = companyShift.shift_end_time;
          var format_check_start = companyShift.shift_start_time;
          var format_end = format_check.match(/\s(.*)$/)[1];
          var format_start = format_check_start.match(/\s(.*)$/)[1];
          var todayend = new Date();
          if (format_end == "AM" && format_start == "PM") {
            todayend.setDate(todayend.getDate() + 1);                  
          }                     

          var valuestart =companyShift.shift_start_time;              
          var valuestop = companyShift.shift_end_time;//$("select[name='timestop']").val();              //create date format                
          var timeStart = new Date(today.toDateString() + " " + valuestart).getTime();              
          var timeEnd = new Date(todayend.toDateString() + " " + valuestop).getTime();              
          var hourDiff = (timeEnd - timeStart) / (1000 * 60 * 60);                


          
          if(hourDiff < 8.5) {

                      var data = {"status": "error", "message": "The shift must be more than 8.5 hrs."}
                              notifyService.notify(data.status, data.message);
                              return false;
            }

            companyShift.total_shift_hours = hourDiff;

            companyShift.company_id = sessionService.get('company_id'); 
           
            var combine_array_id = {};
            combine_array_id.data = companyShift;
           
            combine_array_id.table ='company_shift';

            $http.post('api/public/common/InsertRecords',combine_array_id).success(function(result) {
                    $scope.allShiftData();
                    $scope.company_shift = {};
                    $scope.company_shift.shift_start_time = '';
                    $scope.company_shift.shift_end_time = '';
                    var data = {"status": "success", "message": "Shift Added Successfully."}
                     notifyService.notify(data.status, data.message);
                 
                
            });


        }


    }
    
})();


