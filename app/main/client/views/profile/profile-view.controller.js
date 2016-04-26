(function ()
{
    'use strict';

    angular
            .module('app.client')
            .controller('ProfileViewController', ProfileViewController);

    /** @ngInject */
    function ProfileViewController($document, $window, $timeout, $mdDialog, $stateParams,$resource,sessionService)
    {
        var vm = this;
        //Dummy models data
        vm.client_id = $stateParams.id
        vm.company_id = sessionService.get('company_id');
        vm.clientName="Live Nation"        
        vm.compInfo={
          "logo":"",
          "compContact":{
              "address":"123 1st St. #500 Chicago IL, 60611",
              "phone":"555-555-5555",
              "email":"email@email.com",
              "website":"www.website.com",
          },
          "mainContact":{
              "contact":"Joe Contact",
              "email":"JoeContact@email.com",
              "phone":"555-555-5555",
          },
          "accountInfo":{
              "type":"Contract",
              "disposition":"Good",
          }
        };
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
        vm.salesDetail={
            "web":"www.website.com",
            "anniversaryDate":"2/20/2013",
            "salesPerson":"Salesperson Name",
            "defaultPriceGrid":"CS 2011 Supplied Garments Copy"
        };
        vm.tax={
            "id":123456789,
            "ratePercentage":"10.75%",
            "exempt":"No",
            "idDocument":"taxdoc.pdf"
        };
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
        vm.webPortal={
            "clientUrl":"www.url.com"
        };
        vm.logos={
            "blindApprovalText":"\" Please review and advise(action may be required): \" & \"¶¶\" & \"Please reply to this email with APPROVED, NAME, AND DATE. \"& \"¶¶\" & \"Dear  Valued Customer, \"& \"¶¶\" &\n"+
                    +"\"Please check the following carefully: size, layout, spelling, punctuation, colors, etc.  Changes if required should be noted on the proof when sent back.    Customer assumes full responsibility once proof is signed as approved.  Colors will be printed as close to proof as possible.  Graphics will be sized according to industry standard unless indicated by customer. \"  & \"¶¶\" &"
                    +"\n\"Please note that the JPEG proofs are for layout purposes. It is only to show how the artwork will be positioned on the garment and its size. It will not be legible to indicate on how colors, overprints, knock outs and/or transparency images will or will not appear on the prints. Any JPEG proofs we sent to you can be in low resolution to transfer the file(s) smoothly during the email process but please also check the original file(s) if they are in a good minimum of 300 dpi resolution or Vector images.\" & \"¶¶\" &",
            "colorLogo":"",
            "bwLogo":"",
            "shippingLogo":""
        }
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
        
        getClientProfile();
        function getClientProfile()
        {
            var combine_array_id = {};
            combine_array_id.client_id = vm.client_id;
            combine_array_id.company_id = vm.company_id;

            var checkSession = $resource('api/public/client/GetclientDetail',null,{
                post : {
                       method : 'post'
                       }
            });
            checkSession.post(combine_array_id,function(result) 
            {   
                if(result.data.success=='1')
                {   
                    vm.Response = result.data.records;
                    vm.mainaddress = vm.Response.clientDetail.address;
                    vm.salesDetails =vm.Response.clientDetail.sales;
                    vm.maincompcontact =vm.Response.clientDetail.contact;
                    vm.company_info =vm.Response.clientDetail.main;
                    vm.client_tax =vm.Response.clientDetail.tax;
                    vm.pl_imp =vm.Response.clientDetail.pl_imp;
                    vm.AddrTypeData =vm.Response.AddrTypeData;
                    vm.StaffList =vm.Response.StaffList;
                    vm.ArrCleintType =vm.Response.ArrCleintType;
                  //  vm.PriceGrid = vm.Response.PriceGrid;
                    vm.allContacts = vm.Response.allContacts;
                    vm.allclientnotes = vm.Response.allclientnotes;
                    vm.Arrdisposition = vm.Response.Arrdisposition;
                    vm.Client_orders = vm.Response.Client_orders;
                    vm.art_detail = vm.Response.art_detail;


                    //vm.currentProjectUrl = $sce.trustAsResourceUrl(vm.main.salesweb);
                }
            });
        }

         
        //methods
        function dtInstanceCB(dt) {
            var datatableObj = dt.DataTable;
            vm.tableInstance = datatableObj;
        }       
    }
})();