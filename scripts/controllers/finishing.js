app.controller('finishingListCtrl', ['$scope','$rootScope','$http','$location','$state','$modal','AuthService','$log','AllConstant','$filter', function($scope,$rootScope,$http,$location,$state,$modal,AuthService,$log,AllConstant,$filter) {
                          
    var company_id = $rootScope.company_profile.company_id;
    var login_id = $scope.app.user_id;

    $scope.tab_name = '';
    
    getFinishingData();
    function getFinishingData()
    {
        $("#ajax_loader").show();
        $http.post('api/public/finishing/listFinishing',company_id).success(function(result)
        {
            $("#ajax_loader").hide();


                if($scope.tab_name == '')
                {
                    $scope.orders = result.data.records.active;
                }

                $scope.openTab = function(tab_name) {
                    if(tab_name == 'active'){
                        $scope.tab_name = 'active';
                        $scope.orders = result.data.records.active;
                    }
                    else if(tab_name == 'poly_bagging') {
                        $scope.tab_name = 'poly_bagging';
                        $scope.orders = result.data.records.poly_bagging;
                    }
                    else if(tab_name == 'hang_tag') {
                        $scope.tab_name = 'hang_tag';
                        $scope.orders = result.data.records.hang_tag;
                    }
                    else if(tab_name == 'tag_removal') {
                        $scope.tab_name = 'tag_removal';
                        $scope.orders = result.data.records.tag_removal;
                    }
                    else if(tab_name == 'speciality') {
                        $scope.tab_name = 'speciality';
                        $scope.orders = result.data.records.speciality;
                    }
                    else if(tab_name == 'packing') {
                        $scope.tab_name = 'packing';
                        $scope.orders = result.data.records.packing;
                    }
                    else if(tab_name == 'sticker') {
                        $scope.tab_name = 'sticker';
                        $scope.orders = result.data.records.sticker;
                    }
                    else if(tab_name == 'sew_on_women_tag') {
                        $scope.tab_name = 'sew_on_women_tag';
                        $scope.orders = result.data.records.sew_on_women_tag;
                    }
                    else if(tab_name == 'inside_tag') {
                        $scope.tab_name = 'inside_tag';
                        $scope.orders = result.data.records.inside_tag;
                    }
                    else if(tab_name == 'completed') {
                        $scope.tab_name = 'completed';
                        $scope.orders = result.data.records.completed;
                    }
                    getFinishingData();
                    if($scope.orders == undefined)
                    {
                        $scope.orders = [];
                    }
                }

                var init;

                $scope.searchKeywords = '';
                $scope.filteredOrders = [];
                $scope.row = '';
                $scope.select = function (page) {
                  var end, start;
                  start = (page - 1) * $scope.numPerPage;
                  end = start + $scope.numPerPage;
                  return $scope.currentPageOrders = $scope.filteredOrders.slice(start, end);
                };
                $scope.onFilterChange = function () {
                  $scope.select(1);
                  $scope.currentPage = 1;
                  return $scope.row = '';
                };
                $scope.onNumPerPageChange = function () {
                  $scope.select(1);
                  return $scope.currentPage = 1;
                };
                $scope.onOrderChange = function () {
                  $scope.select(1);
                  return $scope.currentPage = 1;
                };
                $scope.search = function () {
                  $scope.filteredOrders = $filter('filter')($scope.orders, $scope.searchKeywords);
                  return $scope.onFilterChange();
                };
                $scope.order = function (rowName) {
                  if ($scope.row === rowName) {
                      return;
                  }
                  $scope.row = rowName;
                  $scope.filteredOrders = $filter('orderBy')($scope.orders, rowName);
                  return $scope.onOrderChange();
                };
                $scope.numPerPageOpt = [10, 20, 50, 100];
                $scope.numPerPage = 10;
                $scope.currentPage = 1;
                $scope.currentPageOrders = [];

                init = function () {
                  $scope.search();

                  return $scope.select($scope.currentPage);
                };
                return init();
        });
    }

    $scope.finishing = {date: null};

    $scope.deleteorder = function (order_id)
    {
        var permission = confirm(AllConstant.deleteMessage);
        if (permission == true)
        {
            $http.post('api/public/finishing/deleteFinishing',order_id).success(function(result, status, headers, config)
            {
                      if(result.data.success=='1')
                      {
                        $state.go('finishing.list');
                        $("#order_"+order_id).remove();
                        return false;
                      }  
            });
        }
    } // DELETE ORDER FINISH

    $scope.getList = function ()
    {
        if($scope.finishing.date != null)
        {
            var date = get_formated_date($scope.finishing.date);
            var data = {order_date:date};
        }
        else
        {
            var data = {};
        }

        $http.post('api/public/finishing/listFinishing',data).success(function(result)
        {
            $scope.listFinishing = result.data;
        });
    }

    $scope.updateFinishing = function (table,field,id,db_value,tab)
    {
        if(field == 'category_name')
        {
            var value = $("#category_name_"+tab+id).val();
        }
        if(field == 'job_name')
        {
            var value = $("#job_name_"+tab+id).val();
        }
        if(field == 'note')
        {
            var value = $("#note_"+tab+id).val();
        }
        if(field == 'status')
        {
            var value = $("#status_"+tab+id).val();
        }

        if(value != db_value)
        {
            var finishing = {value:value,table:table,field:field,id:id};
            $http.post('api/public/finishing/updateFinishing',finishing).success(function(result)
            {
                getFinishingData();
            });
        }
    }
    $scope.setTime = function (id,param,tab)
    {
        var d = new Date();
        var hours = ("0" + d.getHours()).slice(-2);
        var minutes = ("0" + d.getMinutes()).slice(-2);
        var seconds = ("0" + d.getSeconds()).slice(-2);

        
        if(param == 'start')
        {
            var start_time = $("#start_time_"+tab+id).val();
            var time = $("#time_"+tab+id).val();

            if(start_time == '')
            {
                var time1 = hours + ":" + minutes + ":" + seconds;
                $("#start_time_"+tab+id).val(time1);

                var a = time1.split(':');
                var strtime1 = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);

                var time_diff = parseInt(strtime1) + parseInt(time);
                
                var est = (new Date).clearTime()
                  .addSeconds(time_diff)
                  .toString('H:mm:ss');

                $("#est_"+tab+id).val(est);

                var finishing = {'start_time':time1,'est':est,table:'finishing',id:id,field:''};
                $http.post('api/public/finishing/updateFinishing',finishing).success(function(result)
                {
                    getFinishingData();                
                });
            }
        }
        if(param == 'end')
        {
            var time2 = hours + ":" + minutes + ":" + seconds;
            var start_time = $("#start_time_"+tab+id).val();
            var end_time = $("#end_time_"+tab+id).val();

            if(start_time != '' && end_time == '')
            {
                $("#end_time_"+tab+id).val(time2);

                var a = start_time.split(':');
                var b = time2.split(':');
                
                var strtime1 = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);
                var strtime2 = (+b[0]) * 60 * 60 + (+b[1]) * 60 + (+b[2]);

                var time_diff = parseInt(strtime2) - parseInt(strtime1);
                
                var est = (new Date).clearTime()
                  .addSeconds(time_diff)
                  .toString('H:mm:ss');

                var c = est.split(':');

                $("#est_"+tab+id).val(est);

                var finishing = {'end_time':time2,'est':est,table:'finishing',id:id,field:'','status':'1'};
                $http.post('api/public/finishing/updateFinishing',finishing).success(function(result)
                {
                    getFinishingData();                
                });
            }
        }
    }
}]);