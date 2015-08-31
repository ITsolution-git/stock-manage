(function () {
    'use strict';
    angular.module('app.dashboard', [])

  .controller('dasboardCtrl', ['$scope','$http','$location','$state','AuthService','sessionService', function($scope,$http,$location,$state,AuthService,sessionService) {

  	AuthService.AccessService('ALL');
  	$scope.name = sessionService.get('name');
  }]);
}).call(this);