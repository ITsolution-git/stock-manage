<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Stockk Up Application</title>
  <meta name="description" content="material, material design, angular material, app, web app, responsive, responsive layout, admin, admin panel, admin dashboard, flat, flat ui, ui kit, AngularJS, ui route, charts, widgets, components" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

  <link rel="stylesheet" href="libs/assets/animate.css/animate.css" type="text/css" />
  <link rel="stylesheet" href="libs/assets/font-awesome/css/font-awesome.css" type="text/css" />

  <link rel="stylesheet" href="libs/angular/angular-loading-bar/build/loading-bar.css" type="text/css" />
  <link rel="stylesheet" href="libs/angular/angular-material/angular-material.css" type="text/css" />

  <link rel="stylesheet" href="libs/jquery/bootstrap/dist/css/bootstrap.css" type="text/css" />
  
  <link rel="stylesheet" href="styles/material-design-icons.css" type="text/css" />
  <link rel="stylesheet" href="styles/font.css" type="text/css" />
  <link rel="stylesheet" href="styles/app.css" type="text/css" />
  <link rel="stylesheet" href="libs/custom-scroll/scroller.css" type="text/css" />  
  <link rel="stylesheet" href="styles/stokkup-styles.css" type="text/css" /> 
  <link rel="stylesheet" href="styles/stokkup-styles-1024.css" type="text/css" />
  <link rel="stylesheet" href="styles/_toastr.scss" type="text/css" />
</head>
<body ng-app="app">
  <div class="app app-login" ui-view ng-controller="AppCtrl"></div>
<!-- jQuery -->
  <script src="libs/jquery/jquery/dist/jquery.js"></script>
  <script src="libs/jquery/bootstrap/dist/js/bootstrap.js"></script>
  <!-- Angular -->
  <script src="libs/angular/angular/angular.js"></script>
  <script src="libs/angular/angular-animate/angular-animate.js"></script>
  <script src="libs/angular/angular-aria/angular-aria.js"></script>
  <script src="libs/angular/angular-cookies/angular-cookies.js"></script>
  <script src="libs/angular/angular-messages/angular-messages.js"></script>
  <script src="libs/angular/angular-resource/angular-resource.js"></script>
  <script src="libs/angular/angular-sanitize/angular-sanitize.js"></script>
  <script src="libs/angular/angular-touch/angular-touch.js"></script>
  <script src="libs/angular/angular-material/angular-material.js"></script>

  <!-- Vendor -->
  <script src="libs/angular/angular-ui-router/release/angular-ui-router.js"></script>
  <script src="libs/angular/ngstorage/ngStorage.js"></script>
  <script src="libs/angular/angular-ui-utils/ui-utils.js"></script>
  
  <!-- bootstrap -->
  <script src="libs/angular/angular-bootstrap/ui-bootstrap-tpls.js"></script>

  <!-- lazyload -->
  <script src="libs/angular/oclazyload/dist/ocLazyLoad.js"></script>
  <!-- translate -->
  <script src="libs/angular/angular-translate/angular-translate.js"></script>
  <script src="libs/angular/angular-translate-loader-static-files/angular-translate-loader-static-files.js"></script>
  <script src="libs/angular/angular-translate-storage-cookie/angular-translate-storage-cookie.js"></script>
  <script src="libs/angular/angular-translate-storage-local/angular-translate-storage-local.js"></script>
  <!-- loading-bar -->
  <script src="libs/angular/angular-loading-bar/build/loading-bar.js"></script>

<!-- App -->

  <script src="scripts/app.js"></script>
  <script src="scripts/config.js"></script>
  <script src="scripts/config.lazyload.js"></script>
  <script src="scripts/config.router.js"></script>
  <script src="scripts/app.ctrl.js"></script>
  <script src="scripts/lodash.min.js"></script>

  <script src="scripts/directives/lazyload.js"></script>
  <script src="scripts/directives/ui.js"></script>
  <script src="scripts/directives/ui-jp.js"></script>
  <script src="scripts/directives/ui-nav.js"></script>
  <script src="scripts/directives/ui-fullscreen.js"></script>
  <script src="scripts/directives/ui-scroll.js"></script>
  <script src="scripts/directives/ui-toggle.js"></script>
  <script src="scripts/directives/filemodel.js"></script>
  <script src="scripts/directives/validation.js"></script>
  <script src="scripts/directives/checklist-model.js"></script>
  <script src="scripts/directives/angularjs-dropdown-multiselect.min.js"></script>

  <script src="scripts/filters/fromnow.js"></script>
  <script src="scripts/services/ngstore.js"></script>
  <script src="scripts/services/ui-load.js"></script>
  <script src="scripts/services/ui-load.js"></script>
  <script src="scripts/services/fileupload.js"></script>
  <script src="scripts/services/UICtrl.js"></script>
  <script src="scripts/services/UIDirective.js"></script>
  <script src="scripts/services/UIService.js"></script>
  <script src="scripts/controllers/material.js"></script>
  <script src="scripts/controllers/xeditable.js"></script>
    <!-- COMMON LOGIN FUNCTION AND CHECK -->
  <script src="scripts/common/services.js"></script>
  <script src="scripts/common/angular-flash.js"></script>
  <script src="scripts/common/date.js"></script>
  <script src="scripts/services/angular-base64-upload.js"></script>
  
  

  <!-- LOAD ALL CONTROLLER -->
  <script src="scripts/controllers/account.js"></script>
  <script src="scripts/controllers/dashboard.js"></script>
  <script src="scripts/controllers/product.js"></script>
  <script src="scripts/controllers/bootstrap.js"></script>
  <script src="scripts/controllers/timeoff.js"></script>
  <script src="scripts/controllers/vendor.js"></script>
  <script src="scripts/controllers/note.js"></script>
  <script src="scripts/controllers/staff.js"></script>
  <script src="scripts/controllers/logout.js"></script>
  <script src="scripts/controllers/login.js"></script>
  <script src="scripts/controllers/client.js"></script>
  <script src="scripts/controllers/price.js"></script>
  <script src="scripts/controllers/misc.js"></script>
  <script src="scripts/controllers/placement.js"></script>
  <script src="scripts/controllers/purchase.js"></script>
  <script src="scripts/controllers/order.js"></script>
  <script src="scripts/controllers/finishing.js"></script>
  <script src="scripts/controllers/art.js"></script>
  <script src="scripts/controllers/company.js"></script>
  <script src="scripts/controllers/shipping.js"></script>
  <script src="scripts/controllers/color.js"></script>
  <script src="scripts/controllers/home.js"></script>
  <!-- CUSTOM SCROLL -->
  <script src="libs/custom-scroll/scroller.js"></script>
  <script src="libs/custom-scroll/mwheelIntent.js"></script>
  <script src="libs/custom-scroll/mouseWheel.js"></script>

  <!-- CUSTOM SCRIPTS -->
  <script type="text/javascript" charset="utf-8" src="libs/custom-scripts.js"></script>


  <div id="ajax_loader" class="overlay-loader">
    <div class="loader-background"></div>
    <img class="loader-icon spinning-cog" src="images/loader/loder.png">
  </div>
  
</body>
</html>
