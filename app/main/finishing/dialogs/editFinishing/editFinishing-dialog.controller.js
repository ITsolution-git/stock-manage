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
            $mdDialog.hide();
        };

        function closeDialog()
        {
            $mdDialog.hide();
        }

        $scope.setTime = function (finishing,param)
        {
            var d = new Date();
            var hours = d.getHours();
            var minutes = d.getMinutes();
            var seconds = d.getSeconds();

            var ampm = hours >= 12 ? 'PM' : 'AM';

            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0'+minutes : minutes;
            var strTime = hours + ':' + minutes + ' ' + ampm;

            if(param == 'start')
            {
                /*if($scope.finishing_data.start_time == '')
                {*/
                    $scope.finishing_data.end_time = '';
                    $scope.finishing_data.start_time2 = hours + ":" + minutes + ":" + seconds;
                    var start_time2 = $scope.finishing_data.start_time2;

                    $scope.finishing_data.start_time = strTime;
                    var start_time = $scope.finishing_data.start_time;

                    var a = start_time2.split(':');
                    var start_time2 = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);

                    var est = (new Date).clearTime()
                      .addSeconds(start_time2)
                      .toString('H:mm:ss');

                    $scope.finishing_data.est2 = est;

                    var e = est.split(':');

                    e[0] = e[0] % 12;
                    e[1] = e[0] ? e[0] : 12; // the hour '0' should be '12'
                    minutes = e[1] < 10 ? '0'+e[1] : e[1];
                    var est2 = e[0] + ':' + e[1] + ' ' + ampm;

                    $scope.finishing_data.est = est2;
               // }
            }
            if(param == 'end')
            {
                if($scope.finishing_data.start_time != '' && $scope.finishing_data.end_time == '')
                {
                    $scope.finishing_data.end_time2 = hours + ":" + minutes + ":" + seconds;
                    var end_time2 = $scope.finishing_data.end_time2;
                    var start_time2 = $scope.finishing_data.start_time2;

                    $scope.finishing_data.end_time = strTime;
                    var start_time = $scope.finishing_data.start_time;
                    var end_time = $scope.finishing_data.end_time;

                    var a = start_time2.split(':');
                    var b = end_time2.split(':');
                    
                    var strtime1 = (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2]);
                    var strtime2 = (+b[0]) * 60 * 60 + (+b[1]) * 60 + (+b[2]);

                    var time_diff = parseInt(strtime2) - parseInt(strtime1);
                    
                    var est = (new Date).clearTime()
                      .addSeconds(time_diff)
                      .toString('H:mm:ss');

                    $scope.finishing_data.est2 = est;

                    var e = est.split(':');

                    e[0] = e[0] % 12;
                    e[1] = e[0] ? e[0] : 12; // the hour '0' should be '12'
                    minutes = e[1] < 10 ? '0'+e[1] : e[1];
                    var est2 = e[0] + ':' + e[1] + ' ' + ampm;

                    $scope.finishing_data.est = est2;
                }
            }
        }

        $scope.editFinishing = function()
        {
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