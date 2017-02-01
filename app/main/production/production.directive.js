(function ()
{
    'use strict';

    angular
        .module('app.core')
        .directive('positionsCard', positionsCardDirective);

    /** @ngInject */
    function positionsCardDirective()
    {
        return {
            restrict: 'E',
            scope   : {
                positions        : '=ngModel',
            },
            template: `<div ng-show="positions.rush_job==1" class="position-rush-stripe"><span>rush</span></div>
                      <div class="pull-left position-text">
                          <span class="position-title">{{positions.name}}-{{positions.position_name}}</span>
                          <br/>
                          <span>Due Date: {{positions.due_date}}</span>
                          <br/>
                          <span>ERT: {{positions.run_time}}</span><span> C: {{positions.position_colors}}</span>
                          <br/>
                          <span class="position-status">{{positions.mark_as_complete==1?'Completed':'Pending'}}</span>
                          <span class="stokkup-title-h4 small" style="bottom:20px" ng-class="positions.screen_icon==2?'garment-logo':(positions.screen_icon==1?'garment-logo-orange':'garment-logo disabled')">S</span>
                          <span class="garment-logo stokkup-title-h4 small" style="bottom:5px" ng-class="positions.garment==0?'':' disabled'">G</span>
                      </div>
                      <div class="position-image">
                          <img ng-src="{{positions.image_1}}">
                      </div>`,
            compile : function (tElement)
            {
                // // Add class
                // tElement.addClass('ms-card');
                //
                // return function postLink(scope, iElement)
                // {
                //     // Methods
                //     scope.cardTemplateLoaded = cardTemplateLoaded;
                //
                //     //////////
                //
                //     /**
                //      * Emit cardTemplateLoaded event
                //      */
                //     function cardTemplateLoaded()
                //     {
                //         scope.$emit('msCard::cardTemplateLoaded', iElement);
                //     }
                // };
            }
        };
    }
})();
