(function ()
{
    'use strict';

    angular
        .module('app.login')
        .controller('LoginController', LoginController)
        .controller('LogoutController', LogoutController)
        .controller('DashboardController', DashboardController)
        .controller('ForgetController',ForgetController)
        .controller('ResetController',ResetController);


    /** @ngInject */
    function LoginController(sessionService,$rootScope,$resource,notifyService,$state,AllConstant)
    {
        var vm = this;
        // Data
        vm.path = AllConstant.base_path;

        vm.video_image = vm.path+"assets/images/login_bg/bg_vid.jpg";
        vm.video_1 = vm.path+"assets/images/login_bg/video1.webm";
        vm.video_2 = vm.path+"assets/images/login_bg/video1.mp4";
        
        vm.Login_verify = Login_verify;
        function Login_verify(data)
        {
            var user_data = data;
            sessionService.remove('role_slug');

            var login = $resource('api/public/admin/login',null,{
                post : {
                    method : 'post'
                }
            });


           login.post(user_data,function(result) 
            {   $("#ajax_loader").show();             
                  if(result.data.success == '0') {
                                  var data = {"status": "error", "message": "Please check Email and Password"}
                                  notifyService.notify(data.status, data.message);
                                  $state.go('app.login');
                                  $("#ajax_loader").hide();
                                  return false;

                                } else {

                                   sessionService.set('useremail',result.data.records.useremail);
                                   sessionService.set('role_slug',result.data.records.role_slug);
                                   sessionService.set('login_id',result.data.records.login_id);
                                   sessionService.set('name',result.data.records.name);
                                   sessionService.set('user_id',result.data.records.user_id);
                                   sessionService.set('role_title',result.data.records.role_title);
                                   sessionService.set('username',result.data.records.username);
                                   sessionService.set('password',result.data.records.password);
                                   sessionService.set('company_id',result.data.records.company_id);
                                   sessionService.set('company',result.data.records.company);
                                   sessionService.set('profile_photo',result.data.records.profile_photo);
                                   
                                   var data = {"status": "success", "message": "Login Successfully, Please wait..."}
                                   notifyService.notify(data.status, data.message);
                                   
                                   //window.location.href = $state.go('app.client');
                                    //$state.go('app.client');
                                    
                                   setTimeout(function(){ 
                                        window.open('dashboard', '_self'); }, 1000);
                                   // 
                                    //window.location.reload();
                                    return false;


                                }

                         
            });
        }
        
        // Methods

        //////////
    }
    function LogoutController(sessionService)
    {
        sessionService.destroy();
        return false;
    }
    function DashboardController(sessionService,$scope,$http)
    {
        var vm = this;
        //console.log(sessionService.get('company_name'));
        vm.company_name = sessionService.get('company_name');
        vm.role_slug = sessionService.get('role_slug');
        vm.name = sessionService.get('name');


        var data = {company_id :sessionService.get('company_id')};

        var company_id = document.createElement('input');
        company_id.name = 'company_id';
        company_id.setAttribute('value', sessionService.get('company_id'));

        var combine_array_id = {};
        combine_array_id.company_id = company_id.value;
        //$("#ajax_loader").show();

        // Orders not send to Quickbooks
        $http.post('api/public/invoice/getNoQuickbook',combine_array_id).success(function(result){
            if(result.data.success == '1') {
              $scope.noqbinvoice=result.data.allData[0].totalInvoice;
            }
            /*$scope.brand_coordinator = sessionService.get('role_title');*/
        });

        // Sales Closed
        $http.post('api/public/invoice/getSalesClosed',combine_array_id).success(function(resultSalesClosed){
            if(resultSalesClosed.data.success == '1') {
              $scope.salesClosed1=resultSalesClosed.data.allData[0].totalSales[0];
              $scope.salesClosed2=resultSalesClosed.data.allData[0].totalSales[1];
            }
            /*$scope.brand_coordinator = sessionService.get('role_title');*/
        });

        // Orders with Balances
        $http.post('api/public/invoice/getUnpaid',combine_array_id).success(function(resultUnpaid){
            if(resultUnpaid.data.success == '1') {
              $scope.unpaid1=resultUnpaid.data.allData[0].totalUnpaid[0];
              $scope.unpaid2=resultUnpaid.data.allData[0].totalUnpaid[1];
              $scope.unpaidTotal=resultUnpaid.data.allData[0].totalInvoice;
            }
            /*$scope.brand_coordinator = sessionService.get('role_title');*/
        });

        // Average Orders
        $http.post('api/public/invoice/getAverageOrders',combine_array_id).success(function(resultAverageOrder){
            if(resultAverageOrder.data.success == '1') {
              $scope.avgAmount1=resultAverageOrder.data.allData[0].avgOrderAmount[0];
              $scope.avgAmount2=resultAverageOrder.data.allData[0].avgOrderAmount[1];
              $scope.avgItems1=resultAverageOrder.data.allData[0].avgOrderItems[0];
              $scope.avgItems2=resultAverageOrder.data.allData[0].avgOrderItems[1];
            }
            /*$scope.brand_coordinator = sessionService.get('role_title');*/
        });

    }
    function ForgetController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter)
    {
        var vm = this;
        // Data
        vm.path = AllConstant.base_path;
        vm.video_image = vm.path+"assets/images/login_bg/bg_vid.jpg";
        vm.video_1 = vm.path+"assets/images/login_bg/video1.webm";
        vm.video_2 = vm.path+"assets/images/login_bg/video1.mp4";
        

        $scope.forgot_password = function(email)
        {
             $("#ajax_loader").show();
             var user_data = {};
             user_data = email;
             $http.post('api/public/admin/forgot_password',user_data).success(function(result,event, status, headers, config) {
                  if(result.data.success==0)
                  {
                      var data = {"status": "error", "message": result.data.message}
                      notifyService.notify(data.status, data.message);
                      $("#ajax_loader").hide();
                      return false;
                  }
                  else
                  {
                      var data = {"status": "success", "message": result.data.message}
                      notifyService.notify(data.status, data.message);
                      $("#ajax_loader").hide();
                      return false;
                }
              });
        }

    }
    function ResetController($document, $window, $timeout, $mdDialog,$state, $stateParams,$resource,sessionService,$scope,$http,notifyService,AllConstant,$filter)
    {
              var vm = this;
        // Data
        vm.path = AllConstant.base_path;
        vm.video_image = vm.path+"assets/images/login_bg/bg_vid.jpg";
        vm.video_1 = vm.path+"assets/images/login_bg/video1.webm";
        vm.video_2 = vm.path+"assets/images/login_bg/video1.mp4";
        

           // $("#ajax_loader").show();
            $scope.string = {};
            //console.log(234); return false;
            $scope.string.string = $stateParams.string;
            $http.post('api/public/admin/check_user_password',$scope.string).success(function(result,event, status, headers, config) {
                  if(result.data.success==0)
                  {
                      $("#ajax_loader").hide();
                      var data = {"status": "error", "message": result.data.message}
                      notifyService.notify(data.status, data.message);
                      $state.go('app.login');
                      return false;
                  }
            });


            $scope.change_password = function($user)
            {
                // $("#ajax_loader").show();
                 $scope.data = {};
                 $scope.data.form_data = $user;
                 $scope.data.string = $stateParams.string;
                // console.log($scope.data); return false;
                 $http.post('api/public/admin/change_password', $scope.data).success(function(result,event, status, headers, config) {

                    if(result.data.success==0)
                    {
                        var data = {"status": "error", "message": result.data.message}
                        notifyService.notify(data.status, data.message);
                        $("#ajax_loader").hide();
                        return false;
                    }
                    else
                    {
                        var data = {"status": "success", "message": result.data.message}
                        notifyService.notify(data.status, data.message);
                        $state.go('app.login');
                        $("#ajax_loader").hide();
                        return false;
                    }

                  });
            }

        }

    
})();