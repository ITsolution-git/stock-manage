 var app = angular.module('jmApp', []);


    app.directive('validNumber', function() {
      return {
        require: '?ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
          if(!ngModelCtrl) {
            return; 
          }

          ngModelCtrl.$parsers.push(function(val) {
            if (angular.isUndefined(val)) {
                var val = '';
            }
            
            var clean = val.replace(/[^-0-9\.]/g, '');
            var negativeCheck = clean.split('-');
			var decimalCheck = clean.split('.');
            if(!angular.isUndefined(negativeCheck[1])) {
                negativeCheck[1] = negativeCheck[1].slice(0, negativeCheck[1].length);
                clean =negativeCheck[0] + '-' + negativeCheck[1];
                if(negativeCheck[0].length > 0) {
                	clean =negativeCheck[0];
                }
                
            }
              
            if(!angular.isUndefined(decimalCheck[1])) {
                decimalCheck[1] = decimalCheck[1].slice(0,2);
                clean =decimalCheck[0] + '.' + decimalCheck[1];
            }

            if (val !== clean) {
              ngModelCtrl.$setViewValue(clean);
              ngModelCtrl.$render();
            }
            return clean;
          });

          element.bind('keypress', function(event) {
            if(event.keyCode === 32) {
              event.preventDefault();
            }
          });
        }
      };
    });
        app.directive('validPhone', function() {
      return {
        require: '?ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
          if(!ngModelCtrl) {
            return; 
          }

          ngModelCtrl.$parsers.push(function(val) {
            if (angular.isUndefined(val)) {
                var val = '';
            }
            
            /*var clean = val.replace(/[^\+0-9]/g, '');
            var decimalCheck = clean.split('+');
            
            if(!angular.isUndefined(decimalCheck[1])) 
            {
               clean = "+" + decimalCheck[1];
            }

            if(clean.length>11)
            {
              clean = clean.slice(0,11);
            }*/
            var clean = val.replace(/[^0-9]/g, '');
            if(clean.length>10)
            {
              clean = clean.slice(0,10);
            }
            if (val !== clean) {
              ngModelCtrl.$setViewValue(clean);
              ngModelCtrl.$render();
            }
            return clean;
          });

          element.bind('keypress', function(event) {
            if(event.keyCode === 32) {
              event.preventDefault();
            }
          });
        }
      };
    });
  app.directive('onlyNumber', function() {
      return {
        require: '?ngModel',
        link: function(scope, element, attrs, ngModelCtrl) {
          if(!ngModelCtrl) {
            return; 
          }

          ngModelCtrl.$parsers.push(function(val, length) {
            if (angular.isUndefined(val)) {
                var val = '';
            }
            
            var clean = val.replace(/[^0-9.]/g, '');
            var length = 10;
            if(attrs.onlyNumber != undefined && attrs.onlyNumber > 0)
            {
              length = attrs.onlyNumber;
            }
            if(clean.length>length)
            {
              clean = clean.slice(0,length);
            }
            if (val !== clean) {
              ngModelCtrl.$setViewValue(clean);
              ngModelCtrl.$render();
            }
            return clean;
          });

          element.bind('keypress', function(event) {
            if(event.keyCode === 32) {
              event.preventDefault();
            }
          });
        }
      };
    });

app.directive('dontFill', function() {

  return {

    restrict: 'A',

    link: function link(scope, el, attrs) {
      // password fields need one of the same type above it (firefox)
      var type = el.attr('type') || 'text';
      // chrome tries to act smart by guessing on the name.. so replicate a shadow name
      var name = el.attr('name') || '';
      var shadowName = name + '_shadow';
      // trick the browsers to fill this innocent silhouette
      var shadowEl = angular.element('<input type="' + type + '" name="' + shadowName + '" style="display: none">');

      // insert before
      el.parent()[0].insertBefore(shadowEl[0], el[0]);
    }

  };

});
