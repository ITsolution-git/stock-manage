app.controller('finishingListCtrl', ['$scope','$http','$location','$state','$modal','AuthService','$log','AllConstant', function($scope,$http,$location,$state,$modal,AuthService,$log,AllConstant) {
                          
    $http.get('api/public/finishing/listFinishing').success(function(Listdata) {
        $scope.listFinishing = Listdata.data;
    });

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

    $scope.updateFinishing = function (table,field,id,db_value)
    {
        if(field == 'category_name')
        {
            var value = $("#category_name_"+id).val();
        }
        if(field == 'job_name')
        {
            var value = $("#job_name_"+id).val();
        }
        if(field == 'note')
        {
            var value = $("#note_"+id).val();
        }
        if(field == 'status')
        {
            var value = $("#status_"+id).val();
        }
        alert(id);
        alert(value);
        alert(db_value);
        if(value != db_value)
        {
            var finishing = {value:value,table:table,field:field,id:id};
            $http.post('api/public/finishing/updateFinishing',finishing).success(function(result)
            {
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
    }
    $scope.setTime = function (id,param)
    {
        var d = new Date();
        var hours = ("0" + d.getHours()).slice(-2);
        var minutes = ("0" + d.getMinutes()).slice(-2);
        var seconds = ("0" + d.getSeconds()).slice(-2);

        
        if(param == 'start')
        {
            var start_time = $("#start_time_"+id).val();
            var time = $("#time_"+id).val();

            if(start_time == '')
            {
                var time1 = hours + ":" + minutes + ":" + seconds;
                $("#start_time_"+id).val(time1);

                var a = time1.split(':');
                var strtime1 = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);

                var time_diff = parseInt(strtime1) + parseInt(time);
                
                var est = (new Date).clearTime()
                  .addSeconds(time_diff)
                  .toString('H:mm:ss');

                $("#est_"+id).val(est);

                var finishing = {'start_time':time1,'est':est,table:'finishing',id:id,field:''};
                $http.post('api/public/finishing/updateFinishing',finishing).success(function(result)
                {
                
                });
            }
        }
        if(param == 'end')
        {
            var time2 = hours + ":" + minutes + ":" + seconds;
            var start_time = $("#start_time_"+id).val();
            var end_time = $("#end_time_"+id).val();

            if(start_time != '' && end_time == '')
            {
                $("#end_time_"+id).val(time2);

                var a = start_time.split(':');
                var b = time2.split(':');
                
                var strtime1 = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);
                var strtime2 = (+b[0]) * 60 * 60 + (+b[1]) * 60 + (+b[2]);

                var time_diff = parseInt(strtime2) - parseInt(strtime1);
                
                var est = (new Date).clearTime()
                  .addSeconds(time_diff)
                  .toString('H:mm:ss');

                var c = est.split(':');

                $("#est_"+id).val(est);

                var finishing = {'end_time':time2,'est':est,table:'finishing',id:id,field:''};
                $http.post('api/public/finishing/updateFinishing',finishing).success(function(result)
                {
                
                });
            }
        }
    }
}]);