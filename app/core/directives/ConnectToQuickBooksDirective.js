'use strict';

angular.module('app.core')

.directive('connectToQuickbooks', function($window){
  return {
    restrict: 'E',    template: "<ipp:connectToIntuit></ipp:connectToIntuit>",
    link: function(scope) {
        var script = $window.document.createElement("script");
        script.type = "text/javascript";
        script.src = "//js.appcenter.intuit.com/Content/IA/intuit.ipp.anywhere.js";
        script.onload = function () {
           scope.$emit('intuitjs:loaded');
        };
        $window.document.body.appendChild(script);
        scope.$on('intuitjs:loaded', function (evt) {
          $window.intuit.ipp.anywhere.setup({
            menuProxy: 'http://localhost/stokkup/api/vendor/consolibyte/quickbooks/docs/partner_platform/example_app_ipp_v3/menu.php',
            grantUrl: 'http://localhost/stokkup/api/public/qbo/oauth'
           });
          scope.connected = true;
          scope.$apply();
        });
    }
  }
});