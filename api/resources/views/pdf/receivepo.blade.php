
<html>
<head>
<style type="text/css">
  body{font-size:9px;}
  .align-left{ text-align:left;}
  .align-right{ text-align:right;}
  .align-center{ text-align:center;}
  .line-height{line-height:20px;}
  .font-bold{font-weight:bold;}
  .border-w{border:1px solid #fff; }
  .border-b{border:1px solid #000; }
  .diff-border{width:100%;float:left;height:1px;border-top:solid 1px #000;}
</style>
  <title>Receive Info</title>
</head>

<body>
  <table >
    <tr >
      <td width="30%">
          <table>
              <tr><td><b>{{$company->companyname}}</b></td></tr>
              <tr><td>{{$company->prime_address1}} {{$company->prime_address_street}}
                      {{$company->prime_address_city}}, {{$company->prime_address_state}} {{$company->prime_address_zip}}
                  </td>
              </tr>
              <tr>
                  <td>P: {{$company->prime_phone_main}}</td>
              </tr>
              <!-- <tr>
                  <td><a href="http://www.culturestdio.net">www.culturestdio.net</a></td>
              </tr> -->
          </table>
      </td>
        <td width="40%" align="center"><img id="header_logo" style="height: 100px; width: 100px;" src="{{$company->companyphoto}}" title="Culture Studio" alt="Culture Studio"></td>
      <td width="30%">
          <table>
              <tr><td align="right"><b>Receive PO #{{$company->po_display}}</b></td></tr>
              <tr><td align="right">{{$company->name_company}}</td></tr>
              <tr><td align="right">Attn:{{$company->first_name}} {{$company->last_name}}</td></tr>
          </table>
      </td>
    </tr>
  </table>

  <table style="font-weight:400;margin-top:20px;">
    <tr>
      <td>Company Contact : {{$company->f_name}} {{$company->l_name}}</td>
      <td align="right">Job Name : {{$company->order_name}}</td>
    </tr>
    <tr>
      <td>Brand Coordinator : {{Session::get('name')}}</td>
      <td  align="right">Client PO# : {{$company->custom_po}}</td>
    </tr>
    
  </table>
  <hr style="border:1px solid #000;">



   <table width="100%" class="align-center" border="0" cellspacing="0" cellpadding="0" style="font-family: arial; font-size:10px; border-collapse:collapse;">
        <tr>
            <th width="30%" class="align-center font-bold" height="15">Garment/Item Description</th>
            <th width="10%" class="align-center font-bold" height="15">Size</th>
            <th width="10%" class="align-center font-bold" height="15">Color</th>
            <th width="10%" class="align-center font-bold" height="15">Orderd</th>
            <th width="10%" class="align-center font-bold" height="15">Received</th>
            <th width="10%" class="align-center font-bold" height="15">Defective</th>
            <th width="10%"  class="align-center font-bold" height="15">Short</th>
            <th width="10%"  class="align-center font-bold" height="15">Over</th>
            
        </tr>
        <?php foreach($receive_data as $key_main=>$value_main)
          {$count=1; ?>
          <hr style="border:1px solid #000;">
            <?php foreach($value_main['data'] as $key=>$value)
            { 
              if($count%2==0){$color_bg="#b7c2e0";} else {$color_bg="";} 
              ?>
              <tr style="background-color:<?php echo $color_bg; ?>;" >
                  <td height="20" class="align-center line-height border-b" >&nbsp;<?php echo (!empty($value->product_name))?$value->product_name:''; ?></td>
                  <td height="20" class="align-center line-height border-b">&nbsp;<?php echo (!empty($value->size))?$value->size:''; ?></td>
                  <td height="20" class="align-center line-height border-b">&nbsp;<?php echo (!empty($value->product_color))?$value->product_color:''; ?></td>
                  <td height="20" class="align-center  line-height border-b" >&nbsp;&nbsp;<?php echo (!empty($value->qnty_ordered))?$value->qnty_ordered:0; ?></td>
                  <td height="20" class="align-center  line-height border-b" ><?php echo (!empty($value->qnty_purchased))?$value->qnty_purchased:0; ?></td>
                  <td height="20" class="align-center  line-height border-b" ><?php echo (!empty($value->short))?$value->short:0; ?></td>
                  <td height="20" class="align-center  line-height border-b" >&nbsp;&nbsp;<?php echo (!empty($value->short_unit))?$value->short_unit:0; ?></td>
                  <td height="20" class="align-center  line-height border-b" >&nbsp;&nbsp;<?php echo (!empty($value->over_unit))?$value->over_unit:0; ?></td>
                  
              </tr>
            <?php $count++; } ?>
            <hr style="border:1px solid #000;">
              <tr>
                <td><b>Order Total: <?php echo (!empty($value_main['total_product']))?$value_main['total_product']:0; ?></b></td>
                <td colspan="3"><b>Order Received: <?php echo (!empty($value_main['total_received']))?$value_main['total_received']:0; ?></b></td>
                <td colspan="2"><b>Order Defectives: <?php echo (!empty($value_main['total_defective']))?$value_main['total_defective']:0; ?></b></td>
                <td colspan="2"><b>Summary: <?php echo (!empty($value_main['total_remains']))?$value_main['total_remains']:0; ?></b></td>
              </tr>

          <?php 
          }
          ?>

            <hr style="border:1px solid #000;">
        <tr><td colspan="8"></td></tr>
        <tr>
          <td class="align-right font-bold line-height" colspan="7" style=" border-right:1px solid #000;">Total&nbsp;&nbsp;</td>
          <td class="align-left border-b line-height">&nbsp;&nbsp;<?php if(!empty($company->total_invoice)){ echo $company->total_invoice; } ?></td>
        </tr>
       
  </table>


</body>
</html>
