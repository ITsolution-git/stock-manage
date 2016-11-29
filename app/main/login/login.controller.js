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

                                  sessionService.set('oldLoginId',0);
                                   sessionService.set('oldEmail','');
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
                                   if(result.data.records.reset_password=='1'){
                                    sessionService.set('reset_password',result.data.records.reset_password);
                                   }else{
                                    sessionService.set('reset_password','0');
                                   }

                                   sessionService.set('token',result.data.records.token);
                                   
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
        $scope.showResetPassword = false;
        return false;
    }
    function DashboardController(sessionService,$scope,$http,notifyService,$state,AllConstant,$q,$mdDialog,$document,$mdSidenav,DTOptionsBuilder,DTColumnBuilder,$resource,$stateParams)
    {
        var vm = this;
        //console.log(sessionService.get('company_name'));
        vm.company_name = sessionService.get('company_name');
        vm.role_slug = sessionService.get('role_slug');
        if(sessionService.get('reset_password')=='1'){
          $scope.showResetPassword = true;
        }else{
          $scope.showResetPassword = false;
        }
        vm.name = sessionService.get('name');

        $scope.active = 0;


        var data = {company_id :sessionService.get('company_id')};

        //$("#ajax_loader").show();

        if(vm.role_slug=='CA' || vm.role_slug=='AM' || vm.role_slug=='FM' || vm.role_slug=='SO' || vm.role_slug=='PU'){
            
            $scope.showAvgItem = true;
            $scope.showAvgAmount = false;
            $scope.active = 1;
            $scope.showItemAvg = function(){
              $scope.showAvgItem = true;
              $scope.showAvgAmount = false;
              $scope.active = 1;
            }
            $scope.showAmountAvg = function(){
              $scope.showAvgItem = false;
              $scope.showAvgAmount = true;
              $scope.active = 2;
            }

            //Full dashboard
            var combineDashboard = {};
            var dashboardProduction  = '';
            combineDashboard.company_id = sessionService.get('company_id');
            combineDashboard.comparisonPeriod1 = 'currentYear';

            // Numbers of Orders in Production ng-module watch
            $scope.$watch('productionPersonName', function(newVal, oldVal){
                if(newVal == undefined && oldVal == undefined){
                    dashboardProduction = true;
                }else{
                    dashboardProduction = false;
                }
                if(oldVal != undefined && !dashboardProduction){
                    $("#ajax_loader").show();
                    var combineProduction = {};
                    combineProduction.company_id = sessionService.get('company_id');
                    combineProduction.sales_id = newVal;
                    $http.post('api/public/invoice/getProduction',combineProduction,{headers: {"Authorization": sessionService.get('token')}}).success(function(resultProduction){
                        $("#ajax_loader").hide();
                        if(resultProduction.data.success == '1') {
                            $scope.productionTotal=resultProduction.data.allData[0].totalProduction;
                        }else{
                          var data = {"status": "error", "message": "Data not found."}
                          notifyService.notify(data.status, data.message);
                          return false;
                        }
                    });
                }
            });
            // Sales Closed ng-module watch
            var dashboardClosedSales  = '';
            $scope.$watch('[closedSalesDuration, closedSalesMan]', function(newValCS, oldValCS){

                if(newValCS[0] == undefined && oldValCS[0] == undefined && newValCS[1] == undefined && oldValCS[1] == undefined){
                    dashboardClosedSales = true;
                }else{
                    dashboardClosedSales = false;
                }

                if(oldValCS[0] != undefined && oldValCS[1] != undefined && !dashboardClosedSales){
                    $("#ajax_loader").show();
                    var combineSalesClosed = {};
                    combineSalesClosed.company_id = sessionService.get('company_id');
                    combineSalesClosed.sales_id = newValCS[1];
                    combineSalesClosed.duration = newValCS[0];
                    $http.post('api/public/invoice/getSalesClosed',combineSalesClosed,{headers: {"Authorization": sessionService.get('token')}}).success(function(resultSalesClosed){
                        if(resultSalesClosed.data.success == '1') {
                            $("#ajax_loader").hide();
                            $scope.salesClosed=resultSalesClosed.data.allData[0].totalSales;
                        }else{
                          $("#ajax_loader").hide();
                          var data = {"status": "error", "message": "Data not found."}
                          notifyService.notify(data.status, data.message);
                          return false;
                        }
                    });
                }
            });

            // On Time In Full ng-module watch
            var dashboardFullShipped  = '';
            $scope.$watch('fullDuration', function(newValFS, oldValFS){
                if(newValFS == undefined && oldValFS == undefined){
                    dashboardFullShipped = true;
                }else{
                    dashboardFullShipped = false;
                }
                if(oldValFS != undefined && !dashboardFullShipped){
                    $("#ajax_loader").show();
                    var combineFullShipped = {};
                    combineFullShipped.company_id = sessionService.get('company_id');
                    combineFullShipped.duration = newValFS;
                    $http.post('api/public/invoice/getFullShipped',combineFullShipped,{headers: {"Authorization": sessionService.get('token')}}).success(function(resultFullShipped){
                        $("#ajax_loader").hide();
                        if(resultFullShipped.data.success == '1') {
                          $scope.fullshippedTotal=resultFullShipped.data.allData[0].totalShipped;
                        }else{
                          var data = {"status": "error", "message": "Data not found."}
                          notifyService.notify(data.status, data.message);
                          return false;
                        }
                    });
                }
            });

            // Estimates ng-module watch
            var dashboardEstimates  = '';
            $scope.$watch('[estimatesDuration, estimatesPersonName]', function(newValED, oldValED){
                if(newValED[0] == undefined && oldValED[0] == undefined && newValED[1] == undefined && oldValED[1] == undefined){
                    dashboardEstimates = true;
                }else{
                    dashboardEstimates = false;
                }
                if(oldValED[0] != undefined && oldValED[1] != undefined && !dashboardFullShipped){
                    $("#ajax_loader").show();
                    var combineEstimates = {};
                    combineEstimates.company_id = sessionService.get('company_id');
                    combineEstimates.sales_id = newValED[1];
                    combineEstimates.duration = newValED[0];
                    $http.post('api/public/invoice/getEstimates',combineEstimates,{headers: {"Authorization": sessionService.get('token')}}).success(function(resultEstimated){
                        $("#ajax_loader").hide();
                        if(resultEstimated.data.success == '1') {
                          $scope.estimated=resultEstimated.data.allData[0].totalEstimated;
                          $scope.estimatedTotal=resultEstimated.data.allData[0].totalInvoice;
                        }else{
                          var data = {"status": "error", "message": "Data not found."}
                          notifyService.notify(data.status, data.message);
                          return false;
                        }
                    });
                }
            });

            $("#ajax_loader").show();
            $http.post('api/public/invoice/getFullDashboard',combineDashboard,{headers: {"Authorization": sessionService.get('token')}}).success(function(resultDashboard){
                $("#ajax_loader").hide();
                if(resultDashboard.data.success == '1') {
                  // Fetch Sales Persons for Filtering
                  $scope.salesPersons=resultDashboard.data.allData.salesPersons;
                  // Average Order
                  $scope.avgAmount=resultDashboard.data.allData.averageOrders[0].avgOrderAmount;
                  if(resultDashboard.data.allData.averageOrders[0].avgOrderItems){
                      $scope.avgItems=resultDashboard.data.allData.averageOrders[0].avgOrderItems;
                  }else{
                      $scope.avgItems=0;
                  }
                  // Yearly Gross Compare
                  $scope.estimatedCurrent=resultDashboard.data.allData.yearlyComparison[0].totalEstimated;
                  $scope.estimatedPrevious=resultDashboard.data.allData.yearlyComparison[0].totalEstimatedPrevious;
                  $scope.estimatedComparisonPeriod=resultDashboard.data.allData.yearlyComparison[0].year2;
                  $scope.estimatedComparisonPercent=resultDashboard.data.allData.yearlyComparison[0].percentDifference;

                  // Latest Orders
                  $scope.latestOrders=resultDashboard.data.allData.latestOrders;

                  // Orders not send to Quickbooks
                  $scope.noqbinvoice=resultDashboard.data.allData.noQuickbook[0].totalInvoice;

                  // Orders to be shipped
                  $scope.unshipped=resultDashboard.data.allData.totalUnshipped[0].totalUnshipped;
                  $scope.unshippedTotal=resultDashboard.data.allData.totalUnshipped[0].totalInvoice;

                  // Orders with Balances
                  $scope.unpaid=resultDashboard.data.allData.totalUnpaid[0].totalUnpaid;
                  $scope.unpaidTotal=resultDashboard.data.allData.totalUnpaid[0].totalInvoice;

                  // Numbers of Orders in Production
                  $scope.productionTotal=resultDashboard.data.allData.totalProduction[0].totalProduction;

                  // Sales Closed
                  $scope.salesClosed=resultDashboard.data.allData.salesClosed[0].totalSales;

                  // On Time In Full
                  $scope.fullshippedTotal=resultDashboard.data.allData.fullShipped[0].totalShipped;

                  // Estimates
                  $scope.estimated=resultDashboard.data.allData.totalEstimated[0].totalEstimated;
                  $scope.estimatedTotal=resultDashboard.data.allData.totalEstimated[0].totalInvoice;

                }else{
                  /*var data = {"status": "error", "message": "Data not found."}
                  notifyService.notify(data.status, data.message);*/
                  return false;
                }
            });

            // Estimates with sales man filtering
            /*
            $scope.getEstimatesSalesMan = function(){
                //if(sales_id != 0){
                  if($scope.estimatesPersonName != undefined && $scope.estimatesDuration != undefined) {
                  $("#ajax_loader").show();
                  var combineEstimates = {};
                  combineEstimates.company_id = sessionService.get('company_id');
                  combineEstimates.sales_id = $scope.estimatesPersonName;
                  combineEstimates.duration = $scope.estimatesDuration;
                  $http.post('api/public/invoice/getEstimates',combineEstimates,{headers: {"Authorization": sessionService.get('token')}}).success(function(resultEstimated){
                      if(resultEstimated.data.success == '1') {
                        $("#ajax_loader").hide();
                        $scope.estimated=resultEstimated.data.allData[0].totalEstimated;
                        $scope.estimatedTotal=resultEstimated.data.allData[0].totalInvoice;
                      }else{
                        $("#ajax_loader").hide();
                        var data = {"status": "error", "message": "Data not found."}
                        notifyService.notify(data.status, data.message);
                        return false;
                      }
                  });
                }
                //}
            }*/

            // Sales Closed
            /*var combineSalesClosed = {};
            combineSalesClosed.company_id = sessionService.get('company_id');
            $http.post('api/public/invoice/getSalesClosed',combineSalesClosed).success(function(resultSalesClosed){
                if(resultSalesClosed.data.success == '1') {
                  $scope.salesClosed1=resultSalesClosed.data.allData[0].totalSales[0];
                  $scope.salesClosed2=resultSalesClosed.data.allData[0].totalSales[1];
                }
            });*/
            // Sales Closed with sales man filtering
            /*
            $scope.getSalesClosedSalesMan = function(sales_id){
                //if(sales_id != 0){
                    $("#ajax_loader").show();
                    var combineSalesClosed = {};
                    combineSalesClosed.company_id = sessionService.get('company_id');
                    combineSalesClosed.sales_id = sales_id;
                    $http.post('api/public/invoice/getSalesClosed',combineSalesClosed,{headers: {"Authorization": sessionService.get('token')}}).success(function(resultSalesClosed){
                        if(resultSalesClosed.data.success == '1') {
                            $("#ajax_loader").hide();
                            $scope.salesClosed=resultSalesClosed.data.allData[0].totalSales;
                        }else{
                          $("#ajax_loader").hide();
                          var data = {"status": "error", "message": "Data not found."}
                          notifyService.notify(data.status, data.message);
                          return false;
                        }
                    });
                //}
            };*/

            // Numbers of Orders in Production with sales man filtering
            /*
            $scope.getProductionSalesMan = function(sales_id){
                //if(sales_id != 0){
                  $("#ajax_loader").show();
                  var combineProduction = {};
                  combineProduction.company_id = sessionService.get('company_id');
                  combineProduction.sales_id = sales_id;
                  $http.post('api/public/invoice/getProduction',combineProduction,{headers: {"Authorization": sessionService.get('token')}}).success(function(resultProduction){
                  if(resultProduction.data.success == '1') {
                      $("#ajax_loader").hide();
                      $scope.productionTotal=resultProduction.data.allData[0].totalProduction;
                  }else{
                    $("#ajax_loader").hide();
                    var data = {"status": "error", "message": "Data not found."}
                    notifyService.notify(data.status, data.message);
                    return false;
                  }
                });
              //}
            }*/

            // On Time In Full
            /*
            $scope.getFullOrdersDuration = function(duration){
                $("#ajax_loader").show();
                var combineFullShipped = {};
                combineFullShipped.company_id = sessionService.get('company_id');
                combineFullShipped.duration = duration;
                $http.post('api/public/invoice/getFullShipped',combineFullShipped,{headers: {"Authorization": sessionService.get('token')}}).success(function(resultFullShipped){
                  if(resultFullShipped.data.success == '1') {
                    $("#ajax_loader").hide();
                    $scope.fullshippedTotal=resultFullShipped.data.allData[0].totalShipped;
                  }else{
                    $("#ajax_loader").hide();
                    var data = {"status": "error", "message": "Data not found."}
                    notifyService.notify(data.status, data.message);
                    return false;
                  }
                });
            }
            */
        }
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
                        sessionService.set('reset_password','0');
                        $scope.showResetPassword = false;
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