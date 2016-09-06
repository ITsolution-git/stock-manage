<md-content id="content" class="animate-slide-up md-background md-hue-1 ms-scroll ng-scope flex md-default-theme ps-container ps-theme-default ps-active-y" ms-scroll="" ui-view="content" flex="" data-ps-id="e93466b5-d722-6104-cb5f-6c812c6d8a5a"><div class="main-design-page m-r-15 m-l-20 m-b-20 ng-scope">
    <div class="header layout-column" layout="column">
        <div class="header-content layout-padding layout-wrap layout-align-start-stretch layout-row flex" layout="row" layout-align="start" layout-wrap="" flex="" layout-padding=""></div>
        <div class="order-information m-b-20 layout-wrap layout-align-start-stretch layout-row flex" layout="row" layout-align="start" layout-wrap="" flex="">
            <div class="info-order mrg-auto" flex="40">
                <md-card layout="row" class="layout-row md-default-theme">
                    <md-content class="md-default-theme" ng-controller="extLinktoPayController">
                        <div class="ms-responsive-table-wrapper">
                            <div class="header-typ2">
                                <div layout="row" class="layout-row">
                                    <div layout="column" flex="50" class="layout-column">
                                        <span class="font-20 mrg10-T">Link to Pay - INV-<?php echo $orderArray->order_id ?></span>
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
                                                <input name="invoice_id" value="" ng-init="7" type="hidden">
                                                <md-input-container flex="50">
                                                     <input placeholder="First Name On Card" name="First Name On Card" value="">
                                                </md-input-container>
                                                <md-input-container flex="50">
                                                     <input placeholder="Last Name On Card" name="Last Name On Card" value="">
                                                </md-input-container>
                                                <md-input-container flex="100">
                                                    <input placeholder="Credit Card Number" only-number="20" name="Credit Card Number" value="">
                                                </md-input-container>
                                                <md-input-container flex="100">
                                                    <input placeholder="Amount" name="Amount" only-number="20" value="">
                                                </md-input-container>
                                            </div>
                                            <div layout="row" layout-wrap="nowrap" layout-align="space-between start">
                                                <md-input-container flex="30">
                                                    <md-select placeholder="MM" name="month">
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
                                                   <md-select placeholder="YY" name="year">
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
                                                    <input placeholder="CVV" name="CVV" value="">
                                                </md-input-container>
                                            </div>
                                        </div>
                                        <div flex="10">&nbsp;</div>
                                        <div layout="column" flex="45">
                                            <div class="title" layout-padding><span class="basicInfoStyle">Billing Address</span></div>
                                            <div layout="row" layout-wrap="nowrap" layout-align="space-between start">
                                                <md-input-container flex="75">
                                                    <input placeholder="Street Address" name="Street Address" value="">
                                                </md-input-container>
                                                <md-input-container flex="25">
                                                    <input placeholder="Suite" name="Suite" value="">
                                                </md-input-container>
                                            </div>
                                            <div layout="row" layout-wrap="nowrap" layout-align="space-between start">
                                                <md-input-container flex="40">
                                                    <input placeholder="City" name="City" value="">
                                                </md-input-container>
                                                <md-input-container flex="30">
                                                    <md-select placeholder="State" name="State" aria-label="State" value="">
                                                        <md-option value="">State</md-option>
                                                        <?php foreach ($stateArray as $state) {?>
                                                            <md-option value="<?php echo $state->code; ?>"><?php echo $state->name; ?></md-option>
                                                        <?php } ?>
                                                    </md-select>
                                                </md-input-container>
                                                <md-input-container flex="30">
                                                    <input placeholder="Zip" name="Zip" only-number="10" value="">
                                                </md-input-container>
                                            </div>
                                            <div layout="row" layout-wrap="end center" layout-align="space-between start">&nbsp;</div>
                                            <md-dialog-actions layout-align="end center" layout="row" class="layout-align-end-center layout-row mrg75-T">
                                                <button type="button" class="md-primary md-hue-1 md-accent md-button md-ink-ripple" aria-label="Cancel"><span class="ng-scope">Cancel</span>
                                                </button>
                                                <button type="button" class="md-accent md-raised md-button md-ink-ripple" aria-label="Pay by Credit Card via Authorized.net"><span class="ng-scope">Pay</span></button>
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
    </div>
</md-content>