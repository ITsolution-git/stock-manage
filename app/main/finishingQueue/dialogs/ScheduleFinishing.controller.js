(function ()
{
    'use strict';

    angular
        .module('app.finishingQueue')
        .controller('ScheduleFinishingController', ScheduleFinishingController);

    /** @ngInject */
    function ScheduleFinishingController(finishing_id,$mdDialog,$controller,$state,event,$scope,sessionService,$resource,DTOptionsBuilder,DTColumnBuilder,$http,notifyService)
    {
        var vm = this;
        $scope.save = 0;
        $scope.finishing_id = finishing_id;

        $scope.role_slug = sessionService.get('role_slug');
        if($scope.role_slug=='AT' || $scope.role_slug=='SU')
        {
            $scope.allow_access = 0;
        }
        else
        {
            $scope.allow_access = 1;
        }

        var companyData = {company_id:sessionService.get('company_id'),finishing_id:finishing_id};

        $http.post('api/public/finishingQueue/GetShiftMachine',companyData).success(function(result) 
        {
            if(result.data.success=='1')
            {
                $scope.machine_data = result.data.machine_data;
                $scope.shift_data = result.data.shift_data;
                
                if(result.data.Finishing_scheduleData.length > 0)
                {
                    $scope.Finishing_scheduleData = result.data.Finishing_scheduleData[0];
                    if($scope.Finishing_scheduleData.rush_job == 1)
                    {
                        $scope.Finishing_scheduleData.rush_job = true;
                    }
                    else
                    {
                        $scope.Finishing_scheduleData.rush_job = false;
                    }
                }
                else
                {
                    $scope.Finishing_scheduleData = {};
                }
            }
            else
            {
                notifyService.notify('error',result.data.message);
            }
            $("#ajax_loader").hide();
        });
        
        $scope.scheduleFinishing = function()
        {
            if($scope.Finishing_scheduleData.run_date == undefined || $scope.Finishing_scheduleData.run_date == '')
            {
                notifyService.notify('error','Please select start date');
                return false;
            }
            if($scope.Finishing_scheduleData.shift_id == undefined || $scope.Finishing_scheduleData.shift_id == '')
            {
                notifyService.notify('error','Please select shift');
                return false;
            }
            if($scope.Finishing_scheduleData.machine_id == undefined || $scope.Finishing_scheduleData.machine_id == '0')
            {
                notifyService.notify('error','Please select machine');
                return false;
            }

            if($scope.Finishing_scheduleData.rush_job == true)
            {
                $scope.Finishing_scheduleData.rush_job = 1;                
            }
            else
            {
                $scope.Finishing_scheduleData.rush_job = 0;
            }

            $scope.Finishing_scheduleData.finishing_id = $scope.finishing_id;

            $http.post('api/public/finishingQueue/scheduleFinishing',$scope.Finishing_scheduleData).success(function(result) 
            {
                notifyService.notify('success','Position scheduled successfully and will now show on the scheduling board.');
                $scope.closeDialog();
            });
        }

        $scope.closeDialog = function()
        {
            $mdDialog.hide();
        }
    }
})();