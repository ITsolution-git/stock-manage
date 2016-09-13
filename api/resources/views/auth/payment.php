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