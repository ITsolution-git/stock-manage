(function ()
{
    'use strict';

    angular
        .module('app.client')
        .controller('ProfileViewController', ProfileViewController);

    /** @ngInject */
    function ProfileViewController($document, $window, $timeout, $mdDialog)
    {
        var vm = this;
         vm.dtOptions = {
            dom: '<"top"f>rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };        
    }
})();