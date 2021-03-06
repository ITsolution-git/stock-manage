<!doctype html>
<html ng-app="fuse">
    <head>
        <base href="/">
        <meta charset="utf-8">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

        <script src="scripts/drangula/angular-dragula.js"></script>
        <link rel="stylesheet" href="scripts/drangula/dragula.css" />

        <!-- <meta property="og:site_name" content="app.stokkup.com"/>
        <meta property="og:description" content="Stokkup" />
        <meta property="og:type" content="website" />
        <meta property="og:locale" content="en_US" />
        <meta property="og:image" content="https://app.stokkup.com/assets/images/logos/og_stokkup_logo.png" />

 -->
        <meta property="og:locale" content="en_US" />
        <meta property="og:type" content="profile">
        <meta property="og:title" content="Stokkup">
        <meta property="og:url" content="https://app.stokkup.com">
        <meta property="og:site_name" content="app.stokkup.com" />
        <meta property="og:image" content="https://app.stokkup.com/assets/images/logos/stokkup-fb-logo.jpg" />
        <meta property="og:first_name" content="Stokkup">
        <meta property="og:last_name" content="Software">
<!--

        <meta name="twitter:card" content="summary" />
        <meta name="twitter:title" content="app.stokkup.com" />
        <meta name="twitter:description" content="app.stokkup.com" />
        <meta name="twitter:image" content="https://app.stokkup.com/assets/images/logos/og_logo.png" />
 -->



        <title>STOKKUP</title>

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
        <link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
        <link rel="stylesheet" href="styles/font-awesome.min.css">
        <link rel="stylesheet" href="styles/mdPickers.min.css">
        <link rel="stylesheet" href="styles/vendor.css">
        <link rel="stylesheet" href="styles/app.css">
        <link rel="stylesheet" href="styles/stokkup-custom.css">
        <link rel="stylesheet" href="styles/stokkup-custom-global.css">
        <link rel="stylesheet" href="styles/codepen.min.css">

        <link rel="stylesheet" href="styles/custom.css">
        <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"> -->

        <script src="https://maps.googleapis.com/maps/api/js?components=country:USA&key=AIzaSyBvpANF446OIBFdLaqozAf-lheEZ__oVVg&libraries=places"
        async defer></script>
        <script src="//fast.appcues.com/21130.js"></script>
        <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>

        <script>
            var timer;
            function loadscreen()
            {
                $("#ajax_loader").show();
                setTimeout(function () {
                    $("#ajax_loader").hide();
                }, 3000);
            }
        </script>
    </head>

    <!--[if lt IE 10]>
    <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
        your browser</a> to improve your experience.</p>
    <![endif]-->

    <body md-theme="{{vm.themes.active.name}}" md-theme-watch ng-controller="IndexController as vm"
          class="{{state.current.bodyClass|| ''}}" data-custom-background data-off-canvas-nav onload="loadscreen();">

<!--   <div id="ajax_loader" class="overlay-loader">
    <div class="loader-background"></div>
    <img class="loader-icon spinning-cog" src="assets/images/loader/load2.gif">
  </div> -->

<div class="loaderouter" id="ajax_loader" style="display: hide;">
    <div class="loader">Loading...</div>
</div>
        <!-- SPLASH SCREEN
    <ms-splash-screen id="splash-screen">
        <div class="center">
            <div class="logo" style="width:15%">
                <span>Stokkup</span>
            </div>
            <!-- Material Design Spinner
            <div class="spinner-wrapper">
                <div class="spinner">
                    <div class="inner">
                        <div class="gap"></div>
                        <div class="left">
                            <div class="half-circle"></div>
                        </div>
                        <div class="right">
                            <div class="half-circle"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ms-splash-screen>
    <!-- / SPLASH SCREEN -->

    <div id="main" class="animate-slide-up" ui-view="main" layout="column"></div>



    <!-- <ms-theme-options></ms-theme-options> -->
    <script src="scripts/vendor.js"></script>

    <!--<script src="scripts/mdPickers.min.js"></script>-->
    <!--<script src="scripts/multiselect/lodash.js"></script>-->
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.11.2/lodash.js"></script>-->
    <script src="scripts/other/lodash.js"></script>
    <!--<script src="//cdn.tinymce.com/4/tinymce.min.js"></script>-->

   <script src="scripts/other/tinymce.js"></script>
    <script>
     _.contains = _.includes;
    </script>
    <script src="scripts/multiselect/angularjs-dropdown-multiselect.min.js"></script>
    <script src="scripts/other/ngDraggable.js"></script>
    <script src="scripts/angular-xeditable/xeditable.min.js"></script>


    <!-- inject:js -->

    <script src="app/quick-panel/quick-panel.module.js"></script>
    <script src="app/quick-panel/tabs/chat/chat-tab.controller.js"></script>
    <script src="app/core/core.module.js"></script>
    <script src="app/core/directives/ms-widget/ms-widget.directive.js"></script>
    <script src="app/core/directives/ms-timeline/ms-timeline.js"></script>
    <script src="app/core/directives/ms-stepper/ms-stepper.directive.js"></script>
    <script src="app/core/directives/ms-splash-screen/ms-splash-screen.directive.js"></script>
    <script src="app/core/directives/ms-sidenav-helper/ms-sidenav-helper.directive.js"></script>
    <script src="app/core/directives/ms-search-bar/ms-search-bar.directive.js"></script>
    <script src="app/core/directives/ms-scroll/ms-scroll.directive.js"></script>
    <script src="app/core/directives/ms-responsive-table/ms-responsive-table.directive.js"></script>
    <script src="app/core/directives/ms-random-class/ms-random-class.directive.js"></script>
    <script src="app/core/directives/ms-navigation/ms-navigation.directive.js"></script>
    <script src="app/core/directives/ms-nav/ms-nav.directive.js"></script>
    <script src="app/core/directives/ms-material-color-picker/ms-material-color-picker.directive.js"></script>
    <script src="app/core/directives/ms-form-wizard/ms-form-wizard.directive.js"></script>
    <script src="app/core/directives/ms-datepicker-fix/ms-datepicker-fix.directive.js"></script>
    <script src="app/core/directives/ms-card/ms-card.directive.js"></script>
     <script src="app/core/directives/validation.js"></script>
     <script src="app/core/directives/ConnectToQuickBooksDirective.js"></script>



    <script src="app/core/theming/fuse-theming.provider.js"></script>
    <script src="app/core/theming/fuse-theming.config.js"></script>
    <script src="app/core/theming/fuse-themes.constant.js"></script>
    <script src="app/core/theming/fuse-palettes.constant.js"></script>
    <script src="app/core/theming/fuse-generator.factory.js"></script>
    <script src="app/core/theme-options/theme-options.directive.js"></script>
    <script src="app/core/services/ms-utils.service.js"></script>
    <script src="app/core/services/ms-api.provider.js"></script>
    <script src="app/core/services/api-resolver.service.js"></script>
    <script src="app/core/services/ms-session.service.js"></script>
    <script src="app/core/services/ng-tasty-tpls.min.js"></script>
    <script src="app/core/services/ui-event.js"></script>
    <script src="app/core/services/angular-base64-upload.js"></script>
    <script src="app/core/filters/tag.filter.js"></script>
    <script src="app/core/filters/basic.filter.js"></script>
    <script src="app/core/directives/highlight.directive.js"></script>
    <script src="app/core/config/fuse-config.provider.js"></script>
    <script src="app/core/services/xeditable.js"></script>

    <script src="app/quick-panel/quick-panel.controller.js"></script>
    <script src="app/navigation/navigation.module.js"></script>
    <script src="app/navigation/navigation.controller.js"></script>
    <script src="app/index.module.js"></script>
    <script src="app/main/main.controller.js"></script>
    <script src="app/core/core.run.js"></script>
    <script src="app/core/core.config.js"></script>
    <script src="app/index.run.js"></script>
    <script src="app/index.route.js"></script>
    <script src="app/index.controller.js"></script>
    <script src="app/index.constants.js"></script>
    <script src="app/index.config.js"></script>
    <script src="app/index.api.js"></script>
    <script src="app/toolbar/toolbar.module.js"></script>
    <script src="app/toolbar/toolbar.controller.js"></script>
    <!-- endinject -->

    <!-- LOGIN FILES START-->
    <script src="app/main/login/login.module.js"></script>
    <script src="app/main/login/login.controller.js"></script>

    <!-- ORDER FILES START -->
    <script src="app/main/order/order.module.js"></script>
    <script src="app/main/order/order.controller.js"></script>
    <script src="app/main/order/views/spiltAffiliate/spiltAffiliate.controller.js"></script>
    <script src="app/main/order/views/spiltAffiliate/affiliate-info.controller.js"></script>
    <script src="app/main/order/views/spiltAffiliate/affiliate-view.controller.js"></script>
    <script src="app/main/order/views/order-info/order-info.controller.js"></script>
    <script src="app/main/order/views/order-info/send-email.controller.js"></script>
    <script src="app/main/order/views/distributionProduct/distributionProduct.controller.js"></script>
    <script src="app/main/order/views/distribution/distribution.controller.js"></script>
    <script src="app/main/order/views/design/design.controller.js"></script>
    <script src="app/main/order/dialogs/order/order-dialog.controller.js"></script>
    <script src="app/main/order/dialogs/searchProductView/searchProductView.controller.js"></script>
    <script src="app/main/order/dialogs/searchProduct/searchProduct.controller.js"></script>
    <script src="app/main/order/dialogs/addSplitAffiliate/addSplitAffiliate.controller.js"></script>
    <script src="app/main/order/dialogs/addProduct/addProduct.controller.js"></script>
    <script src="app/main/order/dialogs/addDesign/addDesign.controller.js"></script>
    <script src="app/main/order/dialogs/addAddress/addAddress.controller.js"></script>
    <script src="app/main/order/dialogs/information/information.controller.js"></script>
    <script src="app/main/order/dialogs/information/affiliateInformation.controller.js"></script>
    <script src="app/main/order/dialogs/approveorder/approveorder.controller.js"></script>
    <script src="app/main/order/dialogs/position/position-dialog.controller.js"></script>
    <script src="app/main/order/views/notes/notes.controller.js"></script>
    <script src="app/main/order/dialogs/editOrderNote/editOrderNote.controller.js"></script>
    <script src="app/main/order/dialogs/distribution/existingLocation.controller.js"></script>


    <!-- CLIENT FILES START -->
    <script src="app/main/client/client.module.js"></script>
    <script src="app/main/client/views/profile/profile-view.controller.js"></script>
    <script src="app/main/client/dialogs/client/client-dialog.controller.js"></script>
    <script src="app/main/client/client.controller.js"></script>
    <!--Purchase Order-->
    <script src="app/main/purchaseOrder/purchaseOrder.module.js"></script>
    <script src="app/main/purchaseOrder/purchaseOrder.controller.js"></script>
    <script src="app/main/purchaseOrder/views/companyPO/companyPO.controller.js"></script>
    <script src="app/main/purchaseOrder/views/viewNote/viewNote.controller.js"></script>
    <script src="app/main/purchaseOrder/dialogs/editNote/editNote.controller.js"></script>
    <script src="app/main/purchaseOrder/dialogs/addNote/addNote.controller.js"></script>
    <script src="app/main/purchaseOrder/views/affiliatePO/affiliatePO.controller.js"></script>

    <!--Custom Product-->
    <script src="app/main/customProduct/customProduct.module.js"></script>
    <script src="app/main/customProduct/customProduct.controller.js"></script>
    <script src="app/main/customProduct/dialogs/customProduct/customProduct-dialog.controller.js"></script>

    <!--Receiving-->
    <script src="app/main/receiving/receiving.module.js"></script>
    <script src="app/main/receiving/receiving.controller.js"></script>
    <script src="app/main/receiving/views/receivingInfo/receivingInfo.controller.js"></script>

    <!--Production-->
    <script src="app/main/production/production.module.js"></script>
    <script src="app/main/production/production.controller.js"></script>
    <script src="app/main/production/production.directive.js"></script>

    <!-- Settings -->
    <script src="app/main/settings/settings.module.js"></script>
    <script src="app/main/settings/settings.controller.js"></script>
    <script src="app/main/settings/views/userProfile/userProfile.controller.js"></script>
    <script src="app/main/settings/views/priceGrid/priceGrid.controller.js"></script>
    <script src="app/main/settings/views/companyProfile/companyProfile.controller.js"></script>
    <script src="app/main/settings/views/userManagement/userManagement.controller.js"></script>
    <script src="app/main/settings/views/affiliate/affiliate.controller.js"></script>
    <script src="app/main/settings/dialogs/changePassword/changePassword-dialog.controller.js"></script>
    <script src="app/main/settings/views/priceGrid/createPriceGrid.controller.js"></script>
    <script src="app/main/settings/dialogs/uploadCSV/uploadCSV-dialog.controller.js"></script>
    <script src="app/main/settings/views/companyDetails/companyDetails.controller.js"></script>
    <script src="app/main/settings/dialogs/addEmployee/addEmployee-dialog.controller.js"></script>
    <script src="app/main/settings/dialogs/editEmployee/editEmployee-dialog.controller.js"></script>
    <script src="app/main/settings/dialogs/resetUserPassword/resetUserPassword-dialog.controller.js"></script>
    <script src="app/main/settings/dialogs/deleteEmployee/deleteEmployee-dialog.controller.js"></script>
    <script src="app/main/settings/dialogs/deleteAffiliate/deleteAffiliate-dialog.controller.js"></script>
    <script src="app/main/settings/dialogs/addAffiliate/addAffiliate-dialog.controller.js"></script>
    <script src="app/main/settings/dialogs/editAffiliate/editAffiliate-dialog.controller.js"></script>
    <script src="app/main/settings/views/vendor/vendor.controller.js"></script>
    <script src="app/main/settings/views/vendor/viewContact/viewContact.controller.js"></script>
    <script src="app/main/settings/views/sales/sales.controller.js"></script>
    <script src="app/main/settings/views/approvals/approval.controller.js"></script>
    <script src="app/main/settings/views/labor/labor.controller.js"></script>
    <script src="app/main/settings/dialogs/labor/addLabor.controller.js"></script>
    <script src="app/main/settings/views/machine/machine.controller.js"></script>
    <script src="app/main/settings/views/box/box.controller.js"></script>
    <script src="app/main/settings/views/emailTemplate/emailTemplate.controller.js"></script>
    <script src="app/main/settings/views/emailTemplate/emailTemplateInfoController.js"></script>


    <script src="app/main/settings/dialogs/ssActivewear/ssActivewear-dialog.controller.js"></script>
    <script src="app/main/settings/dialogs/authorizeNet/authorizeNet-dialog.controller.js"></script>
    <script src="app/main/settings/dialogs/ups/ups-dialog.controller.js"></script>
    <script src="app/main/settings/dialogs/deletePriceGrid/deletePriceGrid-dialog.controller.js"></script>
    <script src="app/main/settings/views/integrations/integrations.controller.js"></script>
    <script src="app/main/settings/views/productionSetting/productionSetting.controller.js"></script>

    <!-- Finishing -->
    <script src="app/main/finishing/finishing.module.js"></script>
    <script src="app/main/finishing/finishing.controller.js"></script>
    <script src="app/main/finishing/dialogs/editFinishing/editFinishing-dialog.controller.js"></script>

    <!-- Finishing Queue-->
    <script src="app/main/finishingQueue/finishingQueue.module.js"></script>
    <script src="app/main/finishingQueue/finishingQueue.controller.js"></script>
    <script src="app/main/finishingQueue/dialogs/ScheduleFinishing.controller.js"></script>

    <!-- Art -->
    <script src="app/main/art/art.module.js"></script>
    <script src="app/main/art/art.controller.js"></script>
    <script src="app/main/art/views/orderview/orderView.controller.js"></script>
<!--     <script src="app/main/art/dialogs/createScreen/createScreen-dialog.controller.js"></script> -->
<!--     <script src="app/main/art/dialogs/generateArtForm/generateArtForm-dialog.controller.js"></script> -->
    <script src="app/main/art/views/screensetview/screensetView.controller.js"></script>
    <script src="app/main/art/dialogs/createScreenDetail/createScreenDetail.controller.js"></script>
    <script src="app/main/art/views/viewNote/viewNote.controller.js"></script>

    <!-- Invoices -->
    <script src="app/main/invoices/invoices.module.js"></script>
    <script src="app/main/invoices/invoices.controller.js"></script>
    <script src="app/main/invoices/views/singleInvoice/singleInvoice.controller.js"></script>
    <script src="app/main/invoices/dialogs/linktopay/linktopay-dialog.controller.js"></script>
    <script src="app/main/invoices/views/linktopay/linktopay.controller.js"></script>

    <!-- Shipping -->
    <script src="app/main/shipping/shipping.module.js"></script>
    <script src="app/main/shipping/shipping.controller.js"></script>
    <script src="app/main/shipping/views/orderwaitship/orderwaitship.controller.js"></script>
    <script src="app/main/shipping/dialogs/orderwaitship/editshipping.controller.js"></script>
    <script src="app/main/shipping/views/shipmentdetails/shipmentdetails.controller.js"></script>
    <script src="app/main/shipping/views/boxingdetail/boxingdetail.controller.js"></script>
    <script src="app/main/shipping/views/shipmentoverview/shipmentoverview.controller.js"></script>


    <!-- COMPANY ADMIN -->
    <script src="app/main/admin/admin.module.js"></script>
    <script src="app/main/admin/admin.controller.js"></script>

    <!-- Shipping -->
    <script src="app/main/misc/misc.module.js"></script>
    <script src="app/main/misc/misc.controller.js"></script>

    <script src="scripts/timeoff.js"></script>
    <script src="scripts/date.js"></script>



    <!--Datatable Scrolling-->

    <!--<script src="https://cdn.datatables.net/scroller/1.4.1/js/dataTables.scroller.min.js"></script>
    <link href="https://cdn.datatables.net/scroller/1.4.1/css/scroller.dataTables.min.css" rel="stylesheet">-->
    <script src="scripts/other/dataTables.scroller.min.js"></script>
    <link rel="stylesheet" href="styles/other/scroller.dataTables.min.css">
    <!--Kindo DatePicker-->
    <!--<script src="//cdn.kendostatic.com/2013.1.319/js/kendo.all.min.js"></script>
    <script src="//cdn.kendostatic.com/2014.2.716/js/kendo.angular.min.js"></script>
    <link href = "//cdn.kendostatic.com/2013.1.319/styles/kendo.common.min.css" rel = "stylesheet" / >
    <link href = "//cdn.kendostatic.com/2013.1.319/styles/kendo.default.min.css" rel = "stylesheet" / >-->
    <script src="scripts/other/kendo.all.min.js"></script>
    <script src="scripts/other/kendo.angular.min.js"></script>
    <link rel="stylesheet" href="styles/other/kendo.common.min.css">
    <link rel="stylesheet" href="styles/other/kendo.default.min.css">
    <link rel="stylesheet" href="scripts/angular-xeditable/xeditable.css">
    <script src="scripts/drag/angular-dragdrop.js"></script>
    <script src="scripts/clipboard.js"></script>
    <script src="scripts/other/vs-autocomplete-validator.js"></script>




</body>
</html>
