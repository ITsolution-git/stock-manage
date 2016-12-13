(function ()
{
    'use strict';

    angular
        .module('app.finishing')
        .controller('EditFinishingDialogController', EditFinishingDialogController);

    /** @ngInject */
    function EditFinishingDialogController(Finishing,$mdDialog,$controller,$state,event,$scope,sessionService,$resource,DTOptionsBuilder,DTColumnBuilder,$http,notifyService)
    {
        var vm = this;
        $scope.save = 0;

        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='AT' || $scope.role_slug=='SU')
        {
            $scope.allow_access = 0;
        }
        else
        {
            $scope.allow_access = 1;
        }

        $scope.finishing_data = Finishing;

        if($scope.finishing_data.start_time == '00:00:00')
        {
            $scope.finishing_data.start_time = '';
        }
        if($scope.finishing_data.end_time == '00:00:00')
        {
            $scope.finishing_data.end_time = '';
        }
        if($scope.finishing_data.est == '00:00:00')
        {
            $scope.finishing_data.est = '';
        }

        vm.dtOptions = {
            dom: '<"top">rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };

        // Methods
       vm.dtInstanceCB = dtInstanceCB;
        vm.searchTable = searchTable;

        // -> Filter menu
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
        }
        function searchTable() {
            var query = vm.searchQuery;
            vm.tableInstance.search(query).draw();
        }

        vm.editFinishing = editFinishing ;

        function editFinishing(ev, settings)
            {
                $mdDialog.show({
                    controller: 'EditFinishingDialogController',
                    controllerAs: 'vm',
                    templateUrl: 'app/main/finishing/dialogs/editFinishing/editFinishing-dialog.html',
                    parent: angular.element($document.body),
                    targetEvent: ev,
                    clickOutsideToClose: true,
                    locals: {
                        
                    }
                });
            }
       
        $scope.cancel = function () {
            if($scope.save == 0)
            {
                $scope.finishing_data.start_time = '';
                $scope.finishing_data.end_time = '';
                $scope.finishing_data.est = '';
            }
            $mdDialog.hide();
            $state.go($state.current, '', {reload: true, inherit: false});
        };

        function closeDialog()
        {
            if($scope.save == 0)
            {
                $scope.finishing_data.start_time = '';
                $scope.finishing_data.end_time = '';
                $scope.finishing_data.est = '';
            }
            $mdDialog.hide();
            $state.go($state.current, '', {reload: true, inherit: false});
        }

        $scope.setTime = function (finishing,param)
        {
            if(param == 'start')
            {
                $scope.finishing_data.end_time = '';
                $scope.finishing_data.est = '';
            }
            if(param == 'end')
            {
                if($scope.finishing_data.start_time == '')
                {
                    var data = {"status": "error", "message": "Please select start time"}
                    notifyService.notify(data.status, data.message);
                    $scope.finishing_data.end_time = '';
                    return false;
                }
                else if($scope.finishing_data.end_time == '')
                {
                    $scope.finishing_data.est = '';
                }
                else
                {
                    var time = $scope.finishing_data.start_time;
                    var hours = Number(time.match(/^(\d+)/)[1]);
                    var minutes = Number(time.match(/:(\d+)/)[1]);
                    var AMPM = time.match(/\s(.*)$/)[1];
                    if(AMPM == "PM" && hours<12) hours = hours+12;
                    if(AMPM == "AM" && hours==12) hours = hours-12;
                    var sHours = hours.toString();
                    var sMinutes = minutes.toString();
                    if(hours<10) sHours = "0" + sHours;
                    if(minutes<10) sMinutes = "0" + sMinutes;

                    var start_time = sHours + ":" + sMinutes + ":00";

                    var time = $scope.finishing_data.end_time;
                    var hours = Number(time.match(/^(\d+)/)[1]);
                    var minutes = Number(time.match(/:(\d+)/)[1]);
                    var AMPM = time.match(/\s(.*)$/)[1];
                    if(AMPM == "PM" && hours<12) hours = hours+12;
                    if(AMPM == "AM" && hours==12) hours = hours-12;
                    var sHours = hours.toString();
                    var sMinutes = minutes.toString();
                    if(hours<10) sHours = "0" + sHours;
                    if(minutes<10) sMinutes = "0" + sMinutes;
                    
                    var end_time = sHours + ":" + sMinutes + ":00";

                    var a = start_time.split(':');
                    var b = end_time.split(':');
                    
                    var strtime1 = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);
                    var strtime2 = (+b[0]) * 60 * 60 + (+b[1]) * 60 + (+b[2]);

                    var time_diff = parseInt(strtime2) - parseInt(strtime1);

                    var est = (new Date).clearTime()
                      .addSeconds(time_diff)
                      .toString('H:mm:ss');

                    $scope.finishing_data.est = est;
                }
            }
        }
        $scope.editFinishing = function()
        {
            $scope.save = 1;
            $http.post('api/public/finishing/updateFinishing',$scope.finishing_data).success(function(result)
            {
                if(result.data.success == 1)
                {
                    var data = {"status": "success", "message": result.data.message}
                    notifyService.notify(data.status, data.message);
                    $mdDialog.hide();
                    $state.go($state.current, '', {reload: true, inherit: false});
                }
            });
        }
    }
})();