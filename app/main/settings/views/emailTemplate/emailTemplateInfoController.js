(function ()
{
    'use strict';

    angular
        .module('app.settings')
        .controller('emailTemplateInfoController', emailTemplateInfoController);

    /** @ngInject */
    function emailTemplateInfoController($mdDialog,$controller,$state,$scope,sessionService,$resource,$http,$stateParams,notifyService)
    {

    /*  tinymce.init({
        selector: 'textarea',
        height: 500,
        menubar: false,
        plugins: [
          'advlist autolink lists link image charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table contextmenu paste code'
        ],
        toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        content_css: '//www.tinymce.com/css/codepen.min.css'
      });*/



  $scope.tinymceOptions = {
    plugins: [
          'advlist autolink lists link image charmap print preview anchor',
          'searchreplace visualblocks code fullscreen',
          'insertdatetime media table contextmenu paste code'
        ],
    toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image'
  };


      
        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='CA' || $scope.role_slug=='AM')
        {
            $scope.allow_access = 1; // OTHER ROLES CAN NOT ALLOW TO EDIT, CAN VIEW ONLY
        }
        else
        {
            $scope.allow_access = 0;  // THESE ROLES CAN ALLOW TO EDIT
        }


    $scope.templateDetail = function(){


            var allData = {};
                allData.table ='email_template';
                allData.sort ='id';
                allData.sortcond ='desc';
                allData.cond ={id:$stateParams.id}

                $http.post('api/public/common/GetTableRecords',allData).success(function(result)
                {   
                  $("#ajax_loader").hide();
                    if(result.data.success=='1')
                    {   
                        $scope.allTemplateData = result.data.records[0];
                       
                        
                    } else {
                        $scope.allTemplateData = {};
                       
                    }     
                        
                });


        }

        $scope.templateDetail();


        $scope.saveTemplate = function (templateData,selected) {



            if(templateData.label == undefined || templateData.label == '') {

                      var data = {"status": "error", "message": "Name should not be blank"}
                              notifyService.notify(data.status, data.message);
                              return false;
            }

            if(templateData.description == undefined || templateData.description == '') {

                      var data = {"status": "error", "message": "Description should not be blank"}
                              notifyService.notify(data.status, data.message);
                              return false;
            }

           
                var UpdateArray = {};
                UpdateArray.table ='email_template'
                UpdateArray.data =templateData
                UpdateArray.cond ={id:$stateParams.id}
            
                $http.post('api/public/common/UpdateTableRecords',UpdateArray).success(function(result) {
                     $mdDialog.hide();
                      var data = {"status": "success", "message": "Template Updated Successfully."}
                     notifyService.notify(data.status, data.message);
                     $state.go('app.settings.emailTemplate');
                });


           
           
        };



    }
})();