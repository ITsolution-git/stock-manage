<!doctype html>
<html ng-app="fuse">
    <head>
        <base href="/stokkup/">
        <meta charset="utf-8">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>STOKKUP</title>

        <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
        <link rel="shortcut icon" type="image/x-icon" href="/stokkup/favicon.ico" />
        <link rel="stylesheet" href="styles/vendor.css" type="text/css">

        <link rel="stylesheet" href="styles/app.css" type="text/css">
        <link rel="stylesheet" href="styles/stokkup-custom.css" type="text/css">
        <link rel="stylesheet" href="styles/mdPickers.min.css" type="text/css">
        <link rel="stylesheet" href="styles/stokkup-global-custom.css" type="text/css">
        <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"> -->
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">


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

    <body md-theme="{{vm.themes.active.name}}" md-theme-watch 
          class="{{state.current.bodyClass|| ''}}" data-custom-background data-off-canvas-nav style="background-color:#F5F5F5;">

<?php
//error_reporting(E_ALL);ini_set('display_errors', 'on');
$servername = "192.168.1.13";
$username = "csuser";
$password = "codal123";
//echo $_REQUEST['link'];


class TableRows extends RecursiveIteratorIterator { 
    function __construct($it) { 
        parent::__construct($it, self::LEAVES_ONLY); 
    }

    function current() {
        return parent::current();
    }

    /*function beginChildren() { 
        echo "<tr>"; 
    } 

    function endChildren() { 
        echo "</tr>" . "\n";
    }*/
} 

try {
    $conn = new PDO("mysql:host=$servername;dbname=stokkup", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully"; 

    $stmt = $conn->prepare("SELECT l.ltp_id, l.order_id, l.balance_amount,i.id FROM link_to_pay l left join invoice i on i.order_id=l.order_id where  l.payment_flag='0' and l.session_link='".$_REQUEST['link']."'"); 
    $stmt->execute();
    $i=0;
    $orderArray=array();

    $result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
    foreach(new TableRows(new RecursiveArrayIterator($stmt->fetchAll())) as $k=>$v) { 
        //echo $k." : ".$v;
        $orderArray[$k]=$v;
    }
    if(count($orderArray)<1){?>
        <md-content id="content" class="animate-slide-up md-background md-hue-1 ms-scroll ng-scope flex md-default-theme ps-container ps-theme-default ps-active-y" ms-scroll="" ui-view="content" flex="" data-ps-id="e93466b5-d722-6104-cb5f-6c812c6d8a5a">
        <div class="main-design-page m-r-15 m-l-20 m-b-20 ng-scope">
        <div class="header layout-column" layout="column">
        <div layout="column" flex="50" class="layout-column">
            <span class="font-20 mrg10-T">Link is either expired or no longer valid. Please contact Stokkup Team</span>
        </div>
    </div></div></md-content>
        </body>
</html>
    <?php
    exit;
    }

    $stmt_state = $conn->prepare("SELECT name, code FROM state"); 
    $stmt_state->execute();
    $stateArray=array();

    $resultState = $stmt_state->setFetchMode(PDO::FETCH_ASSOC); 
    foreach($stmt_state->fetchAll() as $k=>$v) { 
        //echo $k." : ".$v;
        $stateArray[$i]=$v;
        //echo $stmt_state['name']." : ".$stmt_state['code'];
        $i++;
    }
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
?>


        
  
    <md-content id="content" class="animate-slide-up md-background md-hue-1 ms-scroll ng-scope flex md-default-theme ps-container ps-theme-default ps-active-y" ms-scroll="" ui-view="content" flex="" data-ps-id="e93466b5-d722-6104-cb5f-6c812c6d8a5a"><div class="main-design-page m-r-15 m-l-20 m-b-20 ng-scope">
    <div class="header layout-column" layout="column">
        <div class="header-content layout-padding layout-wrap layout-align-start-stretch layout-row flex" layout="row" layout-align="start" layout-wrap="" flex="" layout-padding="">
        </div>
        <div class="order-information m-b-20 layout-wrap layout-align-start-stretch layout-row flex" layout="row" layout-align="start" layout-wrap="" flex="">
            <div class="info-order mrg-auto" flex="40">
                <md-card layout="row" class="layout-row md-default-theme">
                    <md-content class="md-default-theme" ng-controller="extLinktoPayController">
                        <div class="ms-responsive-table-wrapper">
                            <div class="header-typ2">
                                    <div layout="row" class="layout-row">
                                        <div layout="column" flex="50" class="layout-column">
                                            <span class="font-20 mrg10-T">Link to Pay - INV-<?php echo $orderArray['order_id'] ?></span>
                                        </div>
                                        <!--<div layout="column" flex="50" class="text-right layout-column">
                                            <md-dialog-actions layout-align="end center" layout="row" class="layout-align-end-center layout-row">
                                                <button type="button" class="bg-grey md-accent md-raised md-button md-ink-ripple" aria-label="Download"><span class="ng-scope">Download</span></button>
                                            </md-dialog-actions>
                                        </div>-->
                                    </div>
                                </div>
                               <div class="pd15">
                                   <div layout="row">
                                       <div layout="column" flex="45">
                                            <div class="title" layout-padding><span class="basicInfoStyle">Credit Card Information</span></div>
                                            <div layout="row" layout-wrap="nowrap" layout-align="space-between start">
<input name="invoice_id" ng-model="company.invoice_id" ng-init="7" type="hidden">
                                                <md-input-container flex="50">
                                                     <input placeholder="First Name On Card" name="First Name On Card" ng-model="company.creditFname">
                                                </md-input-container>
                                                <md-input-container flex="50">
                                                     <input placeholder="Last Name On Card" name="Last Name On Card" ng-model="company.creditLname">
                                                </md-input-container>
                                                <md-input-container flex="100">
                                                    <input placeholder="Credit Card Number" only-number="20" name="Credit Card Number" ng-model="company.creditCard">
                                                </md-input-container>
                                                <md-input-container flex="100">
                                                    <input placeholder="Amount" name="Amount" only-number="20" ng-model="company.amount">
                                                </md-input-container>
                                            </div>
                                            <div layout="row" layout-wrap="nowrap" layout-align="space-between start">
                                                <md-input-container flex="30">
                                                    <md-select ng-model="company.expMonth" placeholder="MM">
                                                        <md-option value="0">MM</md-option>
                                                        <md-option value="01">JAN</md-option>
                                                        <md-option value="02">FEB</md-option>
                                                        <md-option value="03">MAR</md-option>
                                                        <md-option value="04">APR</md-option>
                                                        <md-option value="05">MAY</md-option>
                                                        <md-option value="06">JUN</md-option>
                                                        <md-option value="07">JUL</md-option>
                                                        <md-option value="08">AUG</md-option>
                                                        <md-option value="09">SEP</md-option>
                                                        <md-option value="10">OCT</md-option>
                                                        <md-option value="11">NOV</md-option>
                                                        <md-option value="12">DEC</md-option>
                                                    </md-select>
                                                </md-input-container>
                                                <md-input-container flex=30 class="m-b-20">
                                                   <md-select ng-model="company.expYear" placeholder="YY">
                                                        <md-option value="0">YY</md-option>
                                                        <md-option value="16">16</md-option>
                                                        <md-option value="17">17</md-option>
                                                        <md-option value="18">18</md-option>
                                                        <md-option value="19">19</md-option>
                                                        <md-option value="20">20</md-option>
                                                        <md-option value="21">21</md-option>
                                                        <md-option value="22">22</md-option>
                                                        <md-option value="23">23</md-option>
                                                        <md-option value="24">24</md-option>
                                                        <md-option value="25">25</md-option>
                                                        <md-option value="26">26</md-option>
                                                        <md-option value="27">27</md-option>
                                                        <md-option value="28">28</md-option>
                                                        <md-option value="29">29</md-option>
                                                        <md-option value="30">30</md-option>
                                                    </md-select>
                                                </md-input-container>
                                                <md-input-container flex="30">
                                                    <input placeholder="CVV" name="CVV" ng-model="company.cvv">
                                                </md-input-container>
                                            </div>
                                       </div>
                                       <div flex="10">&nbsp;</div>
                                       <div layout="column" flex="45">
                                            <div class="title" layout-padding><span class="basicInfoStyle">Billing Address</span></div>
                                            <div layout="row" layout-wrap="nowrap" layout-align="space-between start">
                                                <md-input-container flex="75">
                                                    <input placeholder="Street Address" name="Street Address" ng-model="company.street">
                                                </md-input-container>
                                                <md-input-container flex="25">
                                                    <input placeholder="Suite" name="Suite" ng-model="company.suite">
                                                </md-input-container>
                                            </div>
                                            <div layout="row" layout-wrap="nowrap" layout-align="space-between start">
                                                <md-input-container flex="40">
                                                    <input placeholder="City" name="City" ng-model="company.city">
                                                </md-input-container>
                                                <md-input-container flex="30">
                                                    <md-select placeholder="State" name="State" aria-label="State" ng-model="company.state">
                                                        <md-option value="">State</md-option>
                                                        <?php for($s=0;$s<count($stateArray);$s++) {?>
                                                        <md-option value="<?php echo $stateArray[$s]['code']; ?>"><?php echo $stateArray[$s]['name']; ?></md-option>
                                                        <?php } ?>
                                                    </md-select>
                                                </md-input-container>
                                                <md-input-container flex="30">
                                                    <input placeholder="Zip" name="Zip" only-number="10" ng-model="company.zip">
                                                </md-input-container>
                                            </div>
                                            <div layout="row" layout-wrap="end center" layout-align="space-between start">&nbsp;</div>
                                            <md-dialog-actions layout-align="end center" layout="row" class="layout-align-end-center layout-row mrg75-T">
                                                <button type="button" class="md-primary md-hue-1 md-accent md-button md-ink-ripple" aria-label="Cancel"><span class="ng-scope">Cancel</span>
                                                </button>
                                                <button type="button" class="md-accent md-raised md-button md-ink-ripple" aria-label="Pay by Credit Card via Authorized.net" ng-click="pay_creditCard(company, <?php echo $orderArray['id'] ?>, <?php echo $orderArray['ltp_id'] ?> )"><span class="ng-scope">Pay</span></button>
                                            </md-dialog-actions>
                                       </div>
                                   </div>
                               </div>
                            </div>
                    </md-content>
                </md-card>
            </div>
        </div>
    </div>
</div><div class="ps-scrollbar-x-rail" style="left: 0px; bottom: 3px;"><div class="ps-scrollbar-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps-scrollbar-y-rail" style="top: 0px; right: 3px; height: 296px;"><div class="ps-scrollbar-y" tabindex="0" style="top: 0px; height: 204px;"></div></div></md-content>

    <!-- <ms-theme-options></ms-theme-options> -->
    <script src="scripts/vendor.js"></script>
    <!--<script src="scripts/mdPickers.min.js"></script>-->
    <!--<script src="scripts/multiselect/lodash.js"></script>-->
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.11.2/lodash.js"></script>-->
    <script src="scripts/other/lodash.js"></script>
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
    <script src="app/main/order/dialogs/approveorder/approveorder.controller.js"></script>
    <script src="app/main/order/dialogs/position/position-dialog.controller.js"></script>
    

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

    <script src="app/main/settings/dialogs/ssActivewear/ssActivewear-dialog.controller.js"></script>
    <script src="app/main/settings/dialogs/authorizeNet/authorizeNet-dialog.controller.js"></script>
    <script src="app/main/settings/dialogs/ups/ups-dialog.controller.js"></script>
    <script src="app/main/settings/dialogs/deletePriceGrid/deletePriceGrid-dialog.controller.js"></script>
    <script src="app/main/settings/views/integrations/integrations.controller.js"></script>

    <!-- Finishing -->
    <script src="app/main/finishing/finishing.module.js"></script>
    <script src="app/main/finishing/finishing.controller.js"></script>
    <script src="app/main/finishing/dialogs/editFinishing/editFinishing-dialog.controller.js"></script>

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
    <?php $conn = null; ?>
</body>
</html>