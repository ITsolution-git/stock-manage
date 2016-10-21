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
            // Fetch Sales Persons for Filtering
            $scope.showAvgItem = true;
            $scope.showAvgAmount = false;
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
            var combineSalesPersons = {};
            combineSalesPersons.company_id = sessionService.get('company_id');

            $http.post('api/public/invoice/getSalesPersons',combineSalesPersons).success(function(resultSales){
                if(resultSales.data.success == '1') {
                  $scope.salesPersons=resultSales.data.allData;
                }
                /*$scope.brand_coordinator = sessionService.get('role_title');*/
            });

            // Estimates
            /*var combineEstimates = {};
            combineEstimates.company_id = sessionService.get('company_id');
            $http.post('api/public/invoice/getEstimates',combineEstimates).success(function(resultEstimated){
                if(resultEstimated.data.success == '1') {
                  $scope.estimated1=resultEstimated.data.allData[0].totalEstimated[0];
                  $scope.estimated2=resultEstimated.data.allData[0].totalEstimated[1];
                  $scope.estimatedTotal=resultEstimated.data.allData[0].totalInvoice;
                }
            });*/
            // Estimates with sales man filtering
            $scope.getEstimatesSalesMan = function(){
                //if(sales_id != 0){
                  if($scope.estimatesPersonName != undefined && $scope.estimatesDuration != undefined) {
                  $("#ajax_loader").show();
                  var combineEstimates = {};
                  combineEstimates.company_id = sessionService.get('company_id');
                  combineEstimates.sales_id = $scope.estimatesPersonName;
                  combineEstimates.duration = $scope.estimatesDuration;
                  $http.post('api/public/invoice/getEstimates',combineEstimates).success(function(resultEstimated){
                      if(resultEstimated.data.success == '1') {
                        $("#ajax_loader").hide();
                        $scope.estimated=resultEstimated.data.allData[0].totalEstimated;
                        /*$scope.estimated1=resultEstimated.data.allData[0].totalEstimated[0];
                        $scope.estimated2=resultEstimated.data.allData[0].totalEstimated[1];*/
                        $scope.estimatedTotal=resultEstimated.data.allData[0].totalInvoice;
                      }
                  });
                }
                //}
            }

            // Average Orders
            var combineAverageOrders = {};
            combineAverageOrders.company_id = sessionService.get('company_id');

            $http.post('api/public/invoice/getAverageOrders',combineAverageOrders).success(function(resultAverageOrder){
                if(resultAverageOrder.data.success == '1') {
                  $scope.avgAmount=resultAverageOrder.data.allData[0].avgOrderAmount;
                  if(resultAverageOrder.data.allData[0].avgOrderItems){
                      $scope.avgItems=resultAverageOrder.data.allData[0].avgOrderItems;
                  }else{
                      $scope.avgItems=0;
                  }
                  /*$scope.avgAmount1=resultAverageOrder.data.allData[0].avgOrderAmount[0];
                  $scope.avgAmount2=resultAverageOrder.data.allData[0].avgOrderAmount[1];
                  $scope.avgItems1=resultAverageOrder.data.allData[0].avgOrderItems[0];
                  $scope.avgItems2=resultAverageOrder.data.allData[0].avgOrderItems[1];*/
                }
            });
            // Average Orders with sales man filtering
            /*$scope.getAverageOrdersSalesMan = function(sales_id){
                //if(sales_id != 0){
                  $("#ajax_loader").show();
                  var combineAverageOrders = {};
                  combineAverageOrders.company_id = sessionService.get('company_id');
                  combineAverageOrders.sales_id = sales_id;

                  $http.post('api/public/invoice/getAverageOrders',combineAverageOrders).success(function(resultAverageOrder){
                      if(resultAverageOrder.data.success == '1'){
                          $("#ajax_loader").hide();
                          $scope.avgAmount=resultAverageOrder.data.allData[0].avgOrderAmount;
                          if(resultAverageOrder.data.allData[0].avgOrderItems){
                              $scope.avgItems=resultAverageOrder.data.allData[0].avgOrderItems;
                          }else{
                              $scope.avgItems=0;
                          }
                      }
                  });
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
            $scope.getSalesClosedSalesMan = function(sales_id){
                //if(sales_id != 0){
                    $("#ajax_loader").show();
                    var combineSalesClosed = {};
                    combineSalesClosed.company_id = sessionService.get('company_id');
                    combineSalesClosed.sales_id = sales_id;
                    $http.post('api/public/invoice/getSalesClosed',combineSalesClosed).success(function(resultSalesClosed){
                        if(resultSalesClosed.data.success == '1') {
                            $("#ajax_loader").hide();
                            $scope.salesClosed=resultSalesClosed.data.allData[0].totalSales;
                            /*$scope.salesClosed1=resultSalesClosed.data.allData[0].totalSales[0];
                            $scope.salesClosed2=resultSalesClosed.data.allData[0].totalSales[1];*/
                        }
                    });
                //}
            }

            // Orders not send to Quickbooks
            var combineNoQuickbook = {};
            combineNoQuickbook.company_id = sessionService.get('company_id');
            $http.post('api/public/invoice/getNoQuickbook',combineNoQuickbook).success(function(result){
                if(result.data.success == '1') {
                  $scope.noqbinvoice=result.data.allData[0].totalInvoice;
                }
            });

            // Orders with Balances
            var combineUnpaid = {};
            combineUnpaid.company_id = sessionService.get('company_id');
            $http.post('api/public/invoice/getUnpaid',combineUnpaid).success(function(resultUnpaid){
                if(resultUnpaid.data.success == '1') {
                  $scope.unpaid=resultUnpaid.data.allData[0].totalUnpaid;
                  /*$scope.unpaid1=resultUnpaid.data.allData[0].totalUnpaid[0];
                  $scope.unpaid2=resultUnpaid.data.allData[0].totalUnpaid[1];*/
                  $scope.unpaidTotal=resultUnpaid.data.allData[0].totalInvoice;
                }
            });

            // Latest Orders
            var combineLatestOrders = {};
            combineLatestOrders.company_id = sessionService.get('company_id');
            $http.post('api/public/invoice/getLatestOrders',combineLatestOrders).success(function(resultLatestOrders){
                if(resultLatestOrders.data.success == '1') {
                  $scope.latestOrders=resultLatestOrders.data.allData;
                }
            });

            // Comparison Report: Today, Last Week, Last Month, Last Year
            var combineComparison = {};
            combineComparison.company_id = sessionService.get('company_id');
            combineComparison.comparisonPeriod1 = 'currentYear';
            //combineComparison.comparisonPeriod2 = '2015';
            $http.post('api/public/invoice/getComparison',combineComparison).success(function(resultComparison){
                if(resultComparison.data.success == '1') {
                  $scope.estimatedCurrent=resultComparison.data.allData[0].totalEstimated;
                  /*$scope.estimatedCurrent1=resultComparison.data.allData[0].totalEstimated[0];
                  $scope.estimatedCurrent2=resultComparison.data.allData[0].totalEstimated[1];*/
                  $scope.estimatedPrevious=resultComparison.data.allData[0].totalEstimatedPrevious;
                  $scope.estimatedComparisonPeriod=resultComparison.data.allData[0].year2;
                  $scope.estimatedComparisonPercent=resultComparison.data.allData[0].percentDifference;
                }
                /*$scope.brand_coordinator = sessionService.get('role_title');*/
            });

            // Orders to be shipped
            var combineUnshipped = {};
            combineUnshipped.company_id = sessionService.get('company_id');
            $http.post('api/public/invoice/getUnshipped',combineUnshipped).success(function(resultUnshipped){
                if(resultUnshipped.data.success == '1') {
                  $scope.unshipped=resultUnshipped.data.allData[0].totalUnshipped;
                  //$scope.unshipped2=resultUnshipped.data.allData[0].totalUnpaid[1];
                  $scope.unshippedTotal=resultUnshipped.data.allData[0].totalInvoice;
                }
            });

            // Numbers of Orders in Production
            /*var combineProduction = {};
            combineProduction.company_id = sessionService.get('company_id');
            $http.post('api/public/invoice/getProduction',combineProduction).success(function(resultProduction){
                if(resultProduction.data.success == '1') {
                  $scope.productionTotal=resultProduction.data.allData[0].totalProduction;
                }
            });*/

            // Numbers of Orders in Production with sales man filtering
            $scope.getProductionSalesMan = function(sales_id){
                //if(sales_id != 0){
                  $("#ajax_loader").show();
                  var combineProduction = {};
                  combineProduction.company_id = sessionService.get('company_id');
                  combineProduction.sales_id = sales_id;
                  $http.post('api/public/invoice/getProduction',combineProduction).success(function(resultProduction){
                  if(resultProduction.data.success == '1') {
                      $("#ajax_loader").hide();
                      $scope.productionTotal=resultProduction.data.allData[0].totalProduction;
                  }
                });
              //}
            }

            $scope.getFullOrdersDuration = function(duration){
                //if(sales_id != 0){
                  $("#ajax_loader").show();
                  var combineFullShipped = {};
                  combineFullShipped.company_id = sessionService.get('company_id');
                  combineFullShipped.duration = duration;
                  $http.post('api/public/invoice/getFullShipped',combineFullShipped).success(function(resultFullShipped){
                    if(resultFullShipped.data.success == '1') {
                      $("#ajax_loader").hide();
                      $scope.fullshippedTotal=resultFullShipped.data.allData[0].totalShipped;
                    }
                  });
              //}
            }

            // On Time In Full
            /*var combineFullShipped = {};
            combineFullShipped.company_id = sessionService.get('company_id');
            $http.post('api/public/invoice/getFullShipped',combineFullShipped).success(function(resultFullShipped){
                if(resultFullShipped.data.success == '1') {
                  $scope.fullshippedTotal=resultFullShipped.data.allData[0].totalShipped;
                }
            });*/
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