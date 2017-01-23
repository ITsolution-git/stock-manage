
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
  <title>Purchase order Info</title>
</head>

<body style="padding:0; margin:0;border:none;">
    <table class="header">
      <tr>
          <td>
            <div style="width:100%;float:left;padding:15px 0;">
              <table>
                <tr>
                  <td align="left" width="20%"><img src="{{$company->companyphoto}}" title="Culture Studio" alt="Culture Studio" /></td>
                  <td align="left" width="40%" style=" font-weight:bold">
                     Job# {{$company->ord_display}}<br>
                     Job Name: {{$company->order_name}}<br>
                     Client: {{$company->name_company}}
                  </td>
                  <!-- <td align="left" width="20%"></td> -->
                  <td width="40%"  style="vertical-align:middle; position:relative; height:80px;">
                      <table width="100%" class="border-b" align="left" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td style="width:20%; text-align:left;"><img style="display:block; line-height:0px;" src="{{SITE_HOST}}/assets/images/etc/ship.png" title="" alt="" height="80"></td>
                          <td valign="middle" style="width:80%; height:80px; font-size:10px;">
                            <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
                              <tr>
                                <td height="15">&nbsp;</td>
                              </tr>
                              <tr><td>{{$company->street}} {{$company->address}}<br>{{$company->city}}, {{$company->state_name}} {{$company->postal_code}}</td></tr>
                              <tr>
                                <td height="15">&nbsp;</td>
                              </tr>
                            </table>
                          </td>
                        </tr>
                      </table>
                  </td>
                </tr>
              </table>
            </div>
            
          </td>
      </tr> 

       <tr>
          <td>
            <table width="100%" class="align-center" border="0" cellspacing="0" cellpadding="0" style="font-family: arial; font-size:10px; border-collapse:collapse;">
              <tr>
                  <th width="10%" class="align-left font-bold" height="15">Client PO</th>
                  <th width="20%" class="align-left font-bold" height="15">Account Manager</th>
                  <th width="10%" class="align-left font-bold" height="15">Terms</th>
                  <th width="15%" class="align-left font-bold" height="15">Ship Via</th>
                  <th width="15%" class="align-left font-bold" height="15">Ship Date</th>
                  <th width="15%" class="align-left font-bold" height="15">In Hands Date</th>
                  <th width="15%" class="align-left font-bold" height="15">Payment Due</th>
              </tr>
              <tr>
                  <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;{{$company->custom_po}}</td>
                  <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;{{$company->account_manager}}</td>
                  <td height="20" class="align-left line-height border-b"></td>
                  <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;</td>
                  <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;{{$company->date_shipped}}</td>
                  <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;{{$company->in_hands_by}}</td>
                  <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;{{$company->payment_due_date}}</td>
              </tr>
          </table>
          <div style="height:10px;width:100%">&nbsp;</div>
          </td>
        </tr>

        <tr>
            <th width="40%" class="align-left font-bold" height="15">Garment/Item Description</th>
            <th width="30%" class="align-left font-bold" height="15">Color</th>
            <th width="10%" class="align-left font-bold" height="15">Size</th>
            <th width="10%" class="align-left font-bold" height="15">Qnty</th>
            <th width="10%"  class="align-left font-bold" height="15">Received</th>
        </tr>
        <?php 
        $count=1;
        foreach($po_data as $key=>$value)
        { if($count%2==0){$color_bg="#b7c2e0";} else {$color_bg="";} ?>
        <tr style="background-color:<?php echo $color_bg; ?>;" >
            <td height="20" class="align-left line-height border-b"><?php echo (!empty($value->brand_name))?$value->brand_name.' - ':''; ?><?php echo (!empty($value->product_name))?$value->product_name:''; ?></td>
            <td height="20" class="align-left line-height border-b">&nbsp;<?php echo (!empty($value->product_color))?$value->product_color:''; ?></td>
            <td height="20" class="align-left line-height border-b">&nbsp;<?php echo (!empty($value->size))?$value->size:''; ?></td>
            <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;<?php echo (!empty($value->qnty_ordered))?$value->qnty_ordered:0; ?></td>
            <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;<?php echo (!empty($value->qnty_purchased))?$value->qnty_purchased:0; ?></td>
        </tr>
        <?php 
        $count++;
        }
        ?>
        
        <tr>
          <td class="align-right font-bold line-height" colspan="3" style=" border-right:1px solid #000;">Total Qty&nbsp;&nbsp;</td>
          <td class="align-left border-b line-height">&nbsp;&nbsp;<?php if(!empty($order_total[0]->ordered)){ echo $order_total[0]->ordered; } ?></td>
          <!-- <td class="align-right font-bold line-height" style=" border-right:1px solid #000;">PO Total &nbsp;&nbsp;</td>
          <td class="align-left border-b line-height">&nbsp;&nbsp;<?php //if(!empty($order_total[0]->total_amount)){ echo '$'.$order_total[0]->total_amount; } ?></td> -->
        </tr>
        <tr><td>&nbsp;</td></tr>
        <?php if(count($positions)>0)  { $p_count = 1; ?>
        <tr>
              <?php foreach($positions as $key=>$value)  { 

                if($p_count%5==0)
                {
                ?>  </tr> <tr>
                <?php   }  ?>

                 <td width="20%"><b>{{$value->position_name}}</b> <br><img src="{{$value->image_1}}" title="Position" width="200" height="200" alt="Culture Studio"></td> 
                  <td width="4%"></td>
              <?php $p_count++; } ?>
        </tr>
        <?php } ?>
        <br><br>

        <tr><th><b>RECEIVED BY:</b></th></tr><br>

        <tr> <th width="99%"><b>Note:</b></th></tr>

        <?php if(count($positions)>0)  
              {
               foreach($positions as $key=>$value)  
                  { 
                    if(!empty($value->note)) 
                      {?>
                     <tr> <td width="99%"><?php echo "- ".$value->note; ?> </td></tr>
              <?php   }
                  }
              } 
        ?>
  </table>



</body>
</html>
