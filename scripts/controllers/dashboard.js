(function () {
    'use strict';
    angular.module('app.dashboard', [])

  .controller('dasboardCtrl', ['$scope','$http','$location','$state','AuthService','sessionService', function($scope,$http,$location,$state,AuthService,sessionService) {

  	AuthService.AccessService('ALL');
  	email = sessionService.get('useremail');

  }]);
}).call(this);