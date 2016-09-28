<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>STOKKUP Invoice Payment</title>
    
    <link rel="stylesheet" href="../../../../styles/linktopay.css">
<script language="JavaScript" type="text/javascript" src="../../../../scripts/jquery.min.js"></script>
<script>
    var timer;
    function loadscreen()
    {
        $("#ajax_loader").show();
        setTimeout(function () {
            $("#ajax_loader").hide();
        }, 3000);
    }

    var specialKeys = new Array();
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
        if((document.getElementById('creditCard').value.length < 12) || (document.getElementById('creditCard').value.length > 20) ) {
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
                var resultData = $.parseJSON(resultData);
                if (resultData.data.success==1) {
                    alert(resultData.data.message);
                    //alert("Payment made Successfully");
                    setTimeout("location.href = '../../../../index.php';",1000);
                }else{
                    alert("Payment could not be made. Please verify your card details with Authorized.net.");
                }
                
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
    
    <h4 class="stokkup-title-h4">Link to Pay - INV-<?php echo $orderArray->order_id ?> Balance Amount to pay : $<?php echo $orderArray->balance_due ?></h4>
    <?php
    if(isset($orderArray->payment_terms)){
    ?>
        <h4 class="stokkup-title-h4">Payment Terms: $<?php echo $orderArray->payment_terms ?></h4>
    <?php
    }
    ?>
    
    <div class="row linktopay_form">
        <div class="half-sections">
            <div class="title"><span class="basicInfoStyle">Credit Card Information</span></div>
            <div class="flex-50 inputfield-space pull-left m-r-5">
                 <input  type="text" placeholder="First Name On Card" name="creditFname" id="creditFname" value="">
            </div>
            <div class="flex-50 inputfield-space pull-left">
                 <input type="text" placeholder="Last Name On Card" name="creditLname" id="creditLname" value="">
            </div>
            <div class="flex-100 inputfield-space pull-left">
                <input type="text" placeholder="Credit Card Number" maxlength="20" name="creditCard" id="creditCard" onkeypress="return IsNumeric(event);" value="">
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
                    <input type="text" placeholder="CVV" name="cvv" id="cvv" value="" onkeypress="return IsNumeric(event);">
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
                    <select placeholder="State" name="state" id="state">
                        <option value="0">State</option>
                        <?php foreach ($stateArray as $state) {?>
                            <option value="<?php echo $state->code; ?>"><?php echo $state->name; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="flex-30 pull-left">
                    <input type="text" placeholder="Zip" name="zip" id="zip" value="" maxlength="15">
                </div>
            </div>
            <div class="flex-100 inputfield-space pull-left">&nbsp;</div>
            <div class="flex-100 inputfield-space pull-left">&nbsp;</div>
            <div class="flex-100 inputfield-space pull-left">&nbsp;</div>
        </div>
        <?php if ($orderArray->sales_name != '' || $orderArray->account_name != ''){ ?>
        <div class="half-sections">
            <div class="title"><span class="">Questions? Please contact:</span></div>
        </div>
        <div class="half-sections">&nbsp;</div>
        <?php } ?>
        <div class="half-sections">
            <?php if ($orderArray->sales_name != ''){ ?>
            <div class="title"><span class="basicInfoStyle">Sales Person</span></div>
            <?php if ($orderArray->sales_name != ''){ ?>
            <div class="flex-100 inputfield-space pull-left">Name: <?php echo $orderArray->sales_name ?></div>
            <?php } ?>
            <?php if ($orderArray->sales_email != ''){ ?>
            <div class="flex-100 inputfield-space pull-left">Email: <?php echo $orderArray->sales_email ?></div>
            <?php } ?>
            <?php if ($orderArray->sales_phone != ''){ ?>
            <div class="flex-100 inputfield-space pull-left">Phone: <?php echo $orderArray->sales_phone ?></div>
            <?php } ?>
            <?php if ($orderArray->sales_web != ''){ ?>
            <div class="flex-100 inputfield-space pull-left">Website: <?php echo $orderArray->sales_web ?></div>
            <?php } ?>
            <?php } ?>
        </div>
        <div class="half-sections">
            <?php if ($orderArray->account_name != ''){ ?>
            <div class="title"><span class="basicInfoStyle">Account Manager</span></div>
            <?php if ($orderArray->account_name != ''){ ?>
            <div class="flex-100 inputfield-space pull-left">Name: <?php echo $orderArray->account_name ?></div>
            <?php } ?>
            <?php if ($orderArray->account_email != ''){ ?>
            <div class="flex-100 inputfield-space pull-left">Email: <?php echo $orderArray->account_email ?></div>
            <?php } ?>
            <?php if ($orderArray->account_phone != ''){ ?>
            <div class="flex-100 inputfield-space pull-left">Phone: <?php echo $orderArray->account_phone ?></div>
            <?php } ?>
            <?php } ?>
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