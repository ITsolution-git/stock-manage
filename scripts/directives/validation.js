'use strict';

/**
 * @ngdoc function
 * @name app.directive:lazyload
 * @description
 * # uiScroll
 * Directive of the app
 */
angular.module('app')
  app.directive('numeric_tag', function () {
    return {
        restrict: 'EA',
        template: '<input name="{{inputName}}" ng-model="inputValue" />',
        scope: {
            inputValue: '=',
            inputName: '='
        },
        link: function (scope) {
            scope.$watch('inputValue', function(newValue,oldValue) {
                if(String(newValue).indexOf(',') != -1)
                    scope.inputValue = String(newValue).replace(',', '.');
                else {
                    var index_dot,
                        arr = String(newValue).split("");
                    if (arr.length === 0) return;
                    if (arr.length === 1 && (arr[0] == '-' || arr[0] === '.')) return;
                    if (arr.length === 2 && newValue === '-.') return;
                    if (isNaN(newValue) || ((index_dot = String(newValue).indexOf('.')) != -1 && String(newValue).length - index_dot > 3 )) {
                        scope.inputValue = oldValue;
                    }
                }
            });
        }
    };
});
