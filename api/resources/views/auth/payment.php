<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>STOKKUP Invoice Payment</title>
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
    .loaderouter{
    position:fixed;
    width:100%;
    height:100%;
    margin:0px;
    padding:0px;
    background-color:rgba(23, 16, 20, 0.7);
    filter:alpha(opacity=100);
    z-index:200;
    overflow-y:hidden;
    top:0;
    left:0;
    padding-top:10%;
    padding-left:10%;
    opacity: 1;
    }
    .loader,
    .loader:before,
    .loader:after {
        background: #31c0be;
        -webkit-animation: load1 1s infinite ease-in-out;
        animation: load1 1s infinite ease-in-out;
        width: 1em;
        height: 4em;
    }
    .loader:before,
    .loader:after {
        position: absolute;
        top: 0;
        content: '';
    }
    .loader:before {
        left: -1.5em;
        -webkit-animation-delay: -0.32s;
        animation-delay: -0.32s;
    }
    .loader {
        text-indent: -9999em;
        margin: 20em auto;
        position: relative;
        font-size: 11px;
        -webkit-transform: translateZ(0);
        -ms-transform: translateZ(0);
        transform: translateZ(0);
        -webkit-animation-delay: -0.16s;
        animation-delay: -0.16s;
    }
    .loader:after {
        left: 1.5em;
    }
    </style>
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

    var specialKeys = new Array('.');
    specialKeys.push(8); //Backspace
    function IsNumeric(e) {
        var keyCode = e.which ? e.which : e.keyCode
        var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
        return ret;
    }

    function validateFloatKeyPress(el) {
        var v = parseFloat(el.value);
        el.value = (isNaN(v)) ? '' : v.toFixed(2);
    }

    function checkvalidations()
    {
        if(document.getElementById('creditFname').value == '' ) {
            alert("Please enter First Name");
            return false;
        }
        if(document.getElementById('creditLname').value == '' ) {
            alert("Please enter Last Name");
            return false;
        }
        if(document.getElementById('creditCard').value == '' ) {
            alert("Please enter Credit Card");
            return false;
        }
        if(document.getElementById('amount').value == '' ) {
            alert("Please enter Amount");
            return false;
        }
        var m = document.getElementById("expMonth");
        if(m.options[m.selectedIndex].value == '0' ) {
            alert("Please select Month of Expiration");
            return false;
        }
        var e = document.getElementById("expYear");
        if(e.options[e.selectedIndex].value == '0' ) {
            alert("Please select Year of Expiration");
            return false;
        }
        if(document.getElementById('cvv').value == '' ) {
            alert("Please enter CVV");
            return false;
        }
        if(document.getElementById('street').value == '' ) {
            alert("Please enter Street Address");
            return false;
        }
        if(document.getElementById('city').value == '' ) {
            alert("Please enter City");
            return false;
        }
        var s = document.getElementById("state");
        if(s.options[s.selectedIndex].value == '0' ) {
            alert("Please select State");
            return false;
        }
        if(document.getElementById('zip').value == '' ) {
            alert("Please enter Zip");
            return false;
        }
        $("#ajax_loader").show();
        $("#creditCard").numeric();
        $("#cvv").numeric();
        $('#creditCard').keyup(function () { 
            this.value = this.value.replace(/[^0-9\.]/g,'');
        });

        

        var combine_array_id = {};
        combine_array_id.creditFname = document.getElementById('creditFname').value;
        combine_array_id.creditLname = document.getElementById('creditLname').value;
        combine_array_id.creditCard = document.getElementById('creditCard').value;
        combine_array_id.amount = document.getElementById('amount').value;
        combine_array_id.expMonth = document.getElementById('expMonth').value;
        combine_array_id.expYear = document.getElementById('expYear').value;
        combine_array_id.cvv = document.getElementById('cvv').value;
        combine_array_id.street = document.getElementById('street').value;
        combine_array_id.city = document.getElementById('city').value;
        combine_array_id.state = document.getElementById('state').value;
        combine_array_id.zip = document.getElementById('zip').value;
        combine_array_id.linkToPay = 1;
        combine_array_id.storeCard = 1;
        

        combine_array_id.invoice_id = document.getElementById('invoice_id').value;
        combine_array_id.company_id = document.getElementById('company_id').value;
        combine_array_id.ltp_id = document.getElementById('ltp_id').value;
        if(document.getElementById('suite').value != '' ) {
            combine_array_id.suite = document.getElementById('suite').value;
        }

        var saveData = $.ajax({
              type: 'POST',
              url: "/api/public/payment/chargeCreditCard?action=saveData",
              data: combine_array_id,
              dataType: "text",
              success: function(resultData) {
                $("#ajax_loader").hide();
                alert("Payment made Successfully");
                //setTimeout("location.href = '../../../../index.php';",2000);
            }
        });
        saveData.error(function() { alert("Payment could not be made. Please verify your card details with Authorized.net."); });
    }
</script>
<body>
<div class="loaderouter" id="ajax_loader" style="display: none;">
    <div class="loader">Loading...</div>
</div>
<section id="content" class="link-pay-page" data-ps-id="e93466b5-d722-6104-cb5f-6c812c6d8a5a">
    <?php
    if(isset($orderArray->link_status) && $orderArray->link_status==1){
    ?>
    <h4 class="stokkup-title-h4">Link is either expired or no longer valid. Please contact Stokkup Team.</h4>
    <?php
    }else{
    ?>
    <input type="hidden" name="invoice_id" id="invoice_id" value="<?php echo $orderArray->invoice_id ?>">
    <input type="hidden" name="company_id" id="company_id" value="<?php echo $orderArray->company_id ?>">
    <input type="hidden" name="ltp_id" id="ltp_id" value="<?php echo $orderArray->ltp_id ?>">
    
    <h4 class="stokkup-title-h4">Link to Pay - INV-<?php echo $orderArray->order_id ?> Balance Amount to pay : $<?php echo $orderArray->balance_amount ?></h4>
        <div class="row linktopay_form">
            <div class="half-sections">
                <div class="title"><span class="basicInfoStyle">Credit Card Information</span></div>
                        <input name="invoice_id" value="" ng-init="7" type="hidden">
                        <div class="flex-50 inputfield-space pull-left m-r-5">
                             <input  type="text" placeholder="First Name On Card" name="creditFname" id="creditFname" value="">
                        </div>
                        <div class="flex-50 inputfield-space pull-left">
                             <input type="text" placeholder="Last Name On Card" name="creditLname" id="creditLname" value="">
                        </div>
                        <div class="flex-100 inputfield-space pull-left">
                            <input type="text" placeholder="Credit Card Number" maxlength="20" name="creditCard" id="creditCard" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" value="">
                        </div>
                        <div class="flex-100 inputfield-space pull-left">
                            <input type="text" placeholder="Amount" name="amount" id="amount" value="" onchange="validateFloatKeyPress(this);">
                        </div>
                        <div class="flex-100 inputfield-space pull-left">
                            <div class="flex-30 m-r-15 pull-left selectouter">
                            <select id="expMonth" placeholder="MM" name="expMonth">
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
                            <select id="expYear" placeholder="YY" name="expYear">
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
                            <input type="text" placeholder="CVV" name="cvv" id="cvv" value="" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;">
                        </div>
                        </div>
                    </div>
            <div class="half-sections">
                 <div class="title"><span class="basicInfoStyle">Billing Address</span></div>
                    <div class="flex-75 inputfield-space pull-left m-r-5">
                        <input type="text" placeholder="Street Address" id="street" name="street" value="">
                    </div>
                    <div class="flex-25 inputfield-space pull-left">
                        <input type="text" placeholder="Suite" name="suite" id="suite" value="">
                    </div>
                    <div class="flex-100 inputfield-space pull-left">
                        <div class="flex-30 m-r-15 pull-left">
                        <input type="text" placeholder="City" name="city" id="city" value="">
                        </div>
                        <div class="flex-30  m-r-15 pull-left selectouter">
                            <select placeholder="State" name="state" aria-label="State" id="state" value="">
                                <option value="">State</option>
                                <?php foreach ($stateArray as $state) {?>
                                    <option value="<?php echo $state->code; ?>"><?php echo $state->name; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="flex-30 pull-left">
                            <input type="text" placeholder="Zip" name="zip" id="zip" value="" maxlength="15">
                        </div>
                    </div>
            </div>
            <div class="pay-section">
                <button type="button" onclick="checkvalidations()" aria-label="Pay by Credit Card via Authorized.net">PAY</button>
            </div>
        </div>
<?php
}
?>
</section>
</body>
</html>