<!doctype html>
<html ng-app="fuse">
    <head>
        <base href="/stokkup/">
        <meta charset="utf-8">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>STOKKUP</title>

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico" />
        <link rel="stylesheet" href="styles/vendor.css">

        <link rel="stylesheet" href="styles/app.css">
      
        <link href='//fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700italic,700,900,900italic'
              rel='stylesheet' type='text/css'>
        <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"> -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.1/css/font-awesome.css">
    </head>

    <!--[if lt IE 10]>
    <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
        your browser</a> to improve your experience.</p>
    <![endif]-->

    <body md-theme="{{vm.themes.active.name}}" md-theme-watch ng-controller="IndexController as vm"
          class="{{state.current.bodyClass|| ''}}">

        <!-- SPLASH SCREEN -->
    <ms-splash-screen id="splash-screen">
        <div class="center">
            <div class="logo" style="width:15%">
                <span>Stokkup</span>
            </div>
            <!-- Material Design Spinner -->
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
            <!-- / Material Design Spinner -->
        </div>
    </ms-splash-screen>
    <!-- / SPLASH SCREEN -->

    <div id="main" class="animate-slide-up" ui-view="main" layout="column"></div>

    <!-- <ms-theme-options></ms-theme-options> -->

    <script src="scripts/vendor.js"></script>
    
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
    <script src="app/core/filters/tag.filter.js"></script>
    <script src="app/core/filters/basic.filter.js"></script>
    <script src="app/core/directives/highlight.directive.js"></script>
    <script src="app/core/config/fuse-config.provider.js"></script>

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
    <script src="app/main/order/views/spiltAffiliate/spiltAffiliate.controller.js"></script>
    <script src="app/main/order/views/order-info/order-info.controller.js"></script>
    <script src="app/main/order/views/distributionProduct/distributionProduct.controller.js"></script>
    <script src="app/main/order/views/distribution/distribution.controller.js"></script>
    <script src="app/main/order/views/design/design.controller.js"></script>
    <script src="app/main/order/dialogs/order/order-dialog.controller.js"></script>
    <script src="app/main/order/dialogs/searchProductView/searchProductView.controller.js"></script>
    <script src="app/main/order/dialogs/searchProduct/searchProduct.controller.js"></script>
    <script src="app/main/order/dialogs/order/order-dialog.controller.js"></script>
    <script src="app/main/order/dialogs/addSplitAffiliate/addSplitAffiliate.controller.js"></script>
    <script src="app/main/order/dialogs/addProduct/addProduct.controller.js"></script>
    <script src="app/main/order/dialogs/addDesign/addDesign.controller.js"></script>
    <script src="app/main/order/dialogs/addAddress/addAddress.controller.js"></script>
    <script src="app/main/order/order.controller.js"></script>

<!-- CLIENT FILES START -->
    <script src="app/main/client/client.module.js"></script>
    <script src="app/main/client/views/profile/profile-view.controller.js"></script>
    <script src="app/main/client/dialogs/client/client-dialog.controller.js"></script>
    <script src="app/main/client/client.controller.js"></script>
    <!--Purchase Order-->
<script src="app/main/purchaseOrder/purchaseOrder.module.js"></script>
<script src="app/main/purchaseOrder/purchaseOrder.controller.js"></script>
</body>
</html>