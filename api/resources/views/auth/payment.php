
 <script language="JavaScript" type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.0/jquery.min.js"></script>
<script>
            var timer;
            function loadscreen()
            {
                $("#ajax_loader").show();
                setTimeout(function () {
                    $("#ajax_loader").hide();
                }, 3000);
            }


            function checkvalidations()
            {
                var combine_array_id = {};
                combine_array_id.creditFname = document.getElementById(creditFname);
                combine_array_id.creditLname = document.getElementById(creditLname);
                combine_array_id.creditCard = document.getElementById(creditCard);
                combine_array_id.amount = document.getElementById(amount);

                /*$http.post('http://<?php echo $_SERVER['SERVER_NAME']; ?>/api/public/payment/chargeCreditCard',combine_array_id).success(function(result)
                {

                }) ;*/


                var myKeyVals = { creditFname : 'bhargav', creditLname : 'pithva', creditCard : '4111111111111111', amount : '100' }

                var saveData = $.ajax({
                      type: 'POST',
                      url: "http://"+<?php echo $_SERVER['SERVER_NAME']; ?>+"/api/public/payment/chargeCreditCard?action=saveData",
                      data: myKeyVals,
                      dataType: "text",
                      success: function(resultData) { alert("Save Complete") }
                });
                saveData.error(function() { alert("Something went wrong"); });
            }



        </script>
<md-content id="content" class="animate-slide-up md-background md-hue-1 ms-scroll ng-scope flex md-default-theme ps-container ps-theme-default ps-active-y" ms-scroll="" ui-view="content" flex="" data-ps-id="e93466b5-d722-6104-cb5f-6c812c6d8a5a"><div class="main-design-page m-r-15 m-l-20 m-b-20 ng-scope">
    <div class="header layout-column" layout="column">
        <div class="header-content layout-padding layout-wrap layout-align-start-stretch layout-row flex" layout="row" layout-align="start" layout-wrap="" flex="" layout-padding=""></div>
        <div class="order-information m-b-20 layout-wrap layout-align-start-stretch layout-row flex" layout="row" layout-align="start" layout-wrap="" flex="">
            <div class="info-order mrg-auto" flex="40">
                <md-card layout="row" class="layout-row md-default-theme">
                    <md-content class="md-default-theme" ng-controller="extLinktoPayController">
                        <div class="ms-responsive-table-wrapper">
                            <?php
                            if(isset($orderArray->link_status) && $orderArray->link_status==1){
                            ?>
                            <div class="header-typ2">
                                <div layout="row" class="layout-row">
                                    <div layout="column" flex="50" class="layout-column">
                                        <span class="font-20 mrg10-T">Link is either expired or no longer valid. Please contact Stokkup Team</span>
                                    </div>
                                    <!--<div layout="column" flex="50" class="text-right layout-column">
                                        <md-dialog-actions layout-align="end center" layout="row" class="layout-align-end-center layout-row">
                                            <button type="button" class="bg-grey md-accent md-raised md-button md-ink-ripple" aria-label="Download"><span class="ng-scope">Download</span></button>
                                        </md-dialog-actions>
                                    </div>-->
                                </div>
                            </div>
                            <?php
                            }else{
                            ?>
                            <div class="header-typ2">
                                <div layout="row" class="layout-row">
                                    <div layout="column" flex="50" class="layout-column">
                                        <?php
                                        //print_r($orderArray);
                                        ?>
                                        <span class="font-20 mrg10-T">Link to Pay - INV-<?php echo $orderArray->order_id ?> Balance Amount to pay : $<?php echo $orderArray->balance_amount ?></span>
                                    </div>
                                    <!--<div layout="column" flex="50" class="text-right layout-column">
                                        <md-dialog-actions layout-align="end center" layout="row" class="layout-align-end-center layout-row">
                                            <button type="button" class="bg-grey md-accent md-raised md-button md-ink-ripple" aria-label="Download"><span class="ng-scope">Download</span></button>
                                        </md-dialog-actions>
                                    </div>-->
                                </div>
                            </div>
                            <form action="" onsubmit="">
                            <div class="pd15">
                                <div layout="row">
                                    <div layout="column" flex="45">
                                        <div class="title" layout-padding><span class="basicInfoStyle">Credit Card Information</span></div>
                                            <div layout="row" layout-wrap="nowrap" layout-align="space-between start">
                                                <input name="invoice_id" value="" ng-init="7" type="hidden">
                                                <md-input-container flex="50">
                                                     <input placeholder="First Name On Card" name="creditFname" id="creditFname" value="">
                                                </md-input-container>
                                                <md-input-container flex="50">
                                                     <input placeholder="Last Name On Card" name="creditLname" id="creditLname" value="">
                                                </md-input-container>
                                                <md-input-container flex="100">
                                                    <input placeholder="Credit Card Number" only-number="20" id="creditCard" name="creditCard" value="">
                                                </md-input-container>
                                                <md-input-container flex="100">
                                                    <input placeholder="Amount" name="amount" id="amount" only-number="20" value="">
                                                </md-input-container>
                                            </div>
                                            <div layout="row" layout-wrap="nowrap" layout-align="space-between start">
                                                <md-input-container flex="30">
                                                    <md-select placeholder="MM" name="expMonth">
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
                                                   <md-select placeholder="YY" name="expYear">
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
                                                    <input placeholder="CVV" name="cvv" value="">
                                                </md-input-container>
                                            </div>
                                        </div>
                                        <div flex="10">&nbsp;</div>
                                        <div layout="column" flex="45">
                                            <div class="title" layout-padding><span class="basicInfoStyle">Billing Address</span></div>
                                            <div layout="row" layout-wrap="nowrap" layout-align="space-between start">
                                                <md-input-container flex="75">
                                                    <input placeholder="Street Address" name="street" value="">
                                                </md-input-container>
                                                <md-input-container flex="25">
                                                    <input placeholder="Suite" name="Suite" value="">
                                                </md-input-container>
                                            </div>
                                            <div layout="row" layout-wrap="nowrap" layout-align="space-between start">
                                                <md-input-container flex="40">
                                                    <input placeholder="City" name="city" value="">
                                                </md-input-container>
                                                <md-input-container flex="30">
                                                    <md-select placeholder="State" name="state" aria-label="State" value="">
                                                        <md-option value="">State</md-option>
                                                        <?php foreach ($stateArray as $state) {?>
                                                            <md-option value="<?php echo $state->code; ?>"><?php echo $state->name; ?></md-option>
                                                        <?php } ?>
                                                    </md-select>
                                                </md-input-container>
                                                <md-input-container flex="30">
                                                    <input placeholder="Zip" name="zip" only-number="10" value="">
                                                </md-input-container>
                                            </div>
                                            <div layout="row" layout-wrap="end center" layout-align="space-between start">&nbsp;</div>
                                            <md-dialog-actions layout-align="end center" layout="row" class="layout-align-end-center layout-row mrg75-T">
                                                <button type="button" class="md-primary md-hue-1 md-accent md-button md-ink-ripple" aria-label="Cancel"><span class="ng-scope">Cancel</span>
                                                </button>
                                                <button onclick="checkvalidations()" type="button" class="md-accent md-raised md-button md-ink-ripple" aria-label="Pay by Credit Card via Authorized.net"><span class="ng-scope">Pay</span></button>
                                            </md-dialog-actions>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <?php
                            }
                            ?>
                            
                            
                            </div>
                        </md-content>
                    </md-card>
                </div>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Login</title>
    <style type="text/css">
    html,body{
        margin:0;
        padding:0;
    }
    body{
        font-family:"Open Sans",sans-serif;
        color:rgba(110, 117, 132, 1);
        background-color:rgba(245, 245, 245,1);
        padding:20px 0;
    }
    #content{
    position: relative;
    max-width:800px;
    width:100%;
    margin:0 auto;
    float:none;
    display:table;
    background-color:#fff;
    border:solid 1px #e5e5e5;
    }
    .basicInfoStyle {
    font-size: 13px;
    font-style: normal;
    font-weight: 700;
    text-decoration: underline;
    color:rgba(110, 117, 132, 1);
    }
    .stokkup-title-h4{
    color: #252932;
    font-size: 18px;
    line-height: 18px;
    margin: 0;
    padding: 15px;
    border-bottom:solid 1px #e5e5e5;
    width:auto;
    text-align:left;
    text-transform: capitalize;
    }
    .linktopay_form{
        max-width:700px;
        width:100%;
        float:none;
        margin:0 auto;
        display:table;
        padding:15px;
    }
    .half-sections{
        width:46%;
        margin-right:20px;
        float:left;
    }
    .linktopay_form input[type="text"]{
    background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
    border-radius: 0;
    border-style: solid;
    border-width: 0 0 1px;
    border-color:rgba(110, 117, 132, 1);
    box-sizing: border-box;
    display: block;
    float: left;
    height: 30px;
    line-height: 26px;
    margin-top: 0;
    padding: 2px 2px 1px;
    width: 100%;
    color:rgba(110, 117, 132, 1);
    }
    .linktopay_form select{
        appearance:none;
        -moz-appearance:none;
        -webkit-appearance:none;
        border:none;
        border-bottom:solid 1px rgba(110, 117, 132, 1);
        background-color:transparent;
        box-sizing:content-box;
        min-height:26px;
        min-width:64px;
        position:relative;
        color:rgba(110, 117, 132, 1);
        width:100%;
        float:left;
        z-index:1;
    }
    .linktopay_form .selectouter:after{
        position:absolute;
        content:'';
        border-right:solid 6px transparent;
        border-top:solid 6px #6e7584;
        border-left:solid 6px transparent;
        width:0;
        height:0;
        margin-left:-10px;
        margin-top:15px;
        z-index:0;

    }
    .linktopay_form option{
        font-size:16px;
        height:27px;
        line-height:16px;
        cursor:pointer;
        padding:0 16px;
        color:rgba(110, 117, 132, 1);
        width:100%;
        float:left;

    }
    .linktopay_form select option{
        background-color:#fff;
        -moz-appearance:none;
        -webkit-appearance:none;
        -ms-appearance:none;
        appearance:none;
        border:none;
    }
    .flex-30{
    box-sizing: border-box;
    flex: 1 1 30%;
    max-height: 100%;
    width: 30%;
    }
    .flex-50{
    box-sizing: border-box;
    flex: 1 1 50%;
    max-height: 100%;
    width: 49%;
    }
    .m-r-5{
        margin-right:5px !important;
    }
    .flex-100{
    box-sizing: border-box;
    flex: 1 1 100%;
    max-height: 100%;
    width: 100%;
    }
    .flex-75{
    box-sizing: border-box;
    flex: 1 1 75%;
    max-height: 100%;
    width: 73%;
    }
    .flex-40{
    box-sizing: border-box;
    flex: 1 1 40%;
    max-height: 100%;
    width: 40%;
    }
    .flex-25{
    box-sizing: border-box;
    flex: 1 1 25%;
    max-height: 100%;
    width: 25%;
    }
    .inputfield-space{
        margin:18px 0;
    }
    .inputfield-space2{
        margin:18px 0 0;
    }
    .m-b-20{
        margin-bottom:20px;
    }
    .m-r-15{
        margin-right:15px;
    }
    .m-r-20{
        margin-right:20px;
    }
    .pull-left{
        float:left;
    }
    .pay-section{
        width:95%;
        float:none;
        padding-right:20px;
        display:table;
    }
    .linktopay_form button{
        float:right;
        margin-top:30px;
        max-width:140px;
        width:100%;
         color: #fff;
        font-size: 14px;
        font-weight: 700;
        line-height: 41px;
        padding: 0 28px;
        text-align: center;
        background-color:#6187db;
        border:none;
        -moz-border-radius:50px;
        -webkit-border-radius:50px;
        -ms-border-radius:50px;
        -o-border-radius:50px;
        border-radius:50px;
        cursor:pointer;
    }
    </style>
<body>
<section id="content" class="link-pay-page" data-ps-id="e93466b5-d722-6104-cb5f-6c812c6d8a5a">
    <h4 class="stokkup-title-h4">Link to Pay - INV-<?php echo $orderArray->order_id ?></h4>
        <div class="row linktopay_form">
            <div class="half-sections">
                <div class="title"><span class="basicInfoStyle">Credit Card Information</span></div>
                        <input name="invoice_id" value="" ng-init="7" type="hidden">
                        <div class="flex-50 inputfield-space pull-left m-r-5">
                             <input  type="text" placeholder="First Name On Card" name="First Name On Card" value="">
                        </div>
                        <div class="flex-50 inputfield-space pull-left">
                             <input type="text" placeholder="Last Name On Card" name="Last Name On Card" value="">
                        </div>
                        <div class="flex-100 inputfield-space pull-left">
                            <input type="text" placeholder="Credit Card Number" only-number="20" name="Credit Card Number" value="">
                        </div>
                        <div class="flex-100 inputfield-space pull-left">
                            <input type="text" placeholder="Amount" name="Amount" only-number="20" value="">
                        </div>
                        <div class="flex-100 inputfield-space pull-left">
                            <div class="flex-30 m-r-15 pull-left selectouter">
                            <select placeholder="MM" name="month">
                                <option value="0">MM</option>
                                <option value="01">JAN</option>
                                <option value="02">FEB</option>
                                <option value="03">MAR</option>
                                <option value="04">APR</option>
                                <option value="05">MAY</option>
                                <option value="06">JUN</option>
                                <option value="07">JUL</option>
                                <option value="08">AUG</option>
                                <option value="09">SEP</option>
                                <option value="10">OCT</option>
                                <option value="11">NOV</option>
                                <option value="12">DEC</option>
                            </select>
                        </div>
                        <div class="flex-30 m-r-15 pull-left selectouter">
                            <select placeholder="YY" name="year">
                                    <option value="0">YY</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                            </select>
                        </div>
                         <div class="flex-30 pull-left">
                            <input type="text" placeholder="CVV" name="CVV" value="">
                        </div>
                        </div>
                    </div>
            <div class="half-sections">
                 <div class="title"><span class="basicInfoStyle">Billing Address</span></div>
                    <div class="flex-75 inputfield-space pull-left m-r-5">
                        <input type="text" placeholder="Street Address" name="Street Address" value="">
                    </div>
                    <div class="flex-25 inputfield-space pull-left">
                        <input type="text" placeholder="Suite" name="Suite" value="">
                    </div>
                    <div class="flex-100 inputfield-space pull-left">
                        <div class="flex-30 m-r-15 pull-left">
                        <input type="text" placeholder="City" name="City" value="">
                        </div>
                        <div class="flex-30  m-r-15 pull-left selectouter">
                            <select placeholder="State" name="State" aria-label="State" value="">
                                <option value="">State</option>
                                <?php foreach ($stateArray as $state) {?>
                                    <option value="<?php echo $state->code; ?>"><?php echo $state->name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="flex-30 pull-left">
                            <input type="text" placeholder="Zip" name="Zip" only-number="10" value="">
                        </div>
                    </div>
            </div>
            <div class="pay-section">
                <button type="button" aria-label="Pay by Credit Card via Authorized.net">PAY</button>
            </div>
        </div>    
</section>
</body>
</html>
