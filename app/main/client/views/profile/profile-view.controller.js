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
        vm.clientName="Live Nation"
        //Datatable data
        vm.contacts = [
            {"firstname": "joe", "lastname": "contact", "location": "Location Name", "phone": "555-555-5555", "email": "email@email.com"},
            {"firstname": "joe", "lastname": "contact", "location": "Location Name", "phone": "555-555-5555", "email": "email@email.com"}
        ];
        vm.locations = [
            {"streetAddress": "123 1st", "city": "chicago", "State": "IL", "zipcode": "60611", "locationType": "Shipping"},
            {"streetAddress": "123 1st", "city": "chicago", "State": "IL", "zipcode": "60611", "locationType": "Physical"},
            {"streetAddress": "123 1st", "city": "chicago", "State": "IL", "zipcode": "60611", "locationType": "Main"},
            {"streetAddress": "123 1st", "city": "chicago", "State": "IL", "zipcode": "60611", "locationType": "Billing"},
            {"streetAddress": "123 1st", "city": "chicago", "State": "IL", "zipcode": "60611", "locationType": "Physical"}

        ];
        vm.documents = [
            {"fileName": "doc.pdf", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"},
            {"fileName": "doc.pdf", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"},
            {"fileName": "doc.pdf", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"},
            {"fileName": "doc.pdf", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"},
            {"fileName": "doc.pdf", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"}
        ];
        vm.notes = [
            {"createdBy": "John Smith", "notes": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"},
            {"createdBy": "John Smith", "notes": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"},
            {"createdBy": "John Smith", "notes": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"},
            {"createdBy": "John Smith", "notes": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"},
            {"createdBy": "John Smith", "notes": "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor", "dateAdded": "2/2/2016"}
        ];
        vm.orders = [
            {"orderId": "31", "orderName": "Shirts", "dateCreated": "2/2/1016", "approvalStatus": "Order-estimate"},
            {"orderId": "32", "orderName": "Shirts", "dateCreated": "2/2/1016", "approvalStatus": "Sales Review"},
            {"orderId": "33", "orderName": "Shirts", "dateCreated": "2/2/1016", "approvalStatus": "Production Dept"},
            {"orderId": "34", "orderName": "Shirts", "dateCreated": "2/2/1016", "approvalStatus": "Ready to Ship"},
            {"orderId": "35", "orderName": "Shirts", "dateCreated": "2/2/1016", "approvalStatus": "DTG Department", }
        ];
        vm.distributedAddress = [
            {"description": "Lorem ipsum dolor sit amet, consectetur adipisci.", "streetAddress": "123 1st St", "city": "Chicago", "state": "IL", "zipcode": "60611"},
            {"description": "Lorem ipsum dolor sit amet, consectetur adipisci.", "streetAddress": "123 1st St", "city": "Chicago", "state": "IL", "zipcode": "60611"},
            {"description": "Lorem ipsum dolor sit amet, consectetur adipisci.", "streetAddress": "123 1st St", "city": "Chicago", "state": "IL", "zipcode": "60611"},
            {"description": "Lorem ipsum dolor sit amet, consectetur adipisci.", "streetAddress": "123 1st St", "city": "Chicago", "state": "IL", "zipcode": "60611"},
            {"description": "Lorem ipsum dolor sit amet, consectetur adipisci.", "streetAddress": "123 1st St", "city": "Chicago", "state": "IL", "zipcode": "60611"}
        ];
        vm.screenSets = [
            {"description": "1-25152", "graphicSize": "Oversized 25 x 36", "images": "chicago"},
            {"description": "1-25152", "graphicSize": "Oversized 25 x 36", "images": "chicago"},
            {"description": "1-25152", "graphicSize": "Oversized 25 x 36", "images": "chicago"},
            {"description": "1-25152", "graphicSize": "Oversized 25 x 36", "images": "chicago"}
        ];
        vm.arts = [
            {"fileName": "screen1.png", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis at hendrerit risus.", "dateAdded": "2/2/1016"},
            {"fileName": "screen1.png", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis at hendrerit risus.", "dateAdded": "2/2/1016"},
            {"fileName": "screen1.png", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis at hendrerit risus.", "dateAdded": "2/2/1016"},
            {"fileName": "screen1.png", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis at hendrerit risus.", "dateAdded": "2/2/1016"},
            {"fileName": "screen1.png", "description": "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis at hendrerit risus.", "dateAdded": "2/2/1016"}
        ];



        vm.dtOptions = {
            dom: '<"top"f>rt<"bottom"<"left"<"length"l>><"right"<"info"i><"pagination"p>>>',
            pagingType: 'simple',
            autoWidth: false,
            responsive: true
        };
        var originatorEv;
        vm.openMenu = function ($mdOpenMenu, ev) {
            originatorEv = ev;
            $mdOpenMenu(ev);
        };
        
         vm.dtInstanceCB = dtInstanceCB;
         
        //methods
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
        }       
    }
})();