<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="description" content="">
  <title>Orders Print</title>
</head>
<style>
table{margin:0;padding:0;vertical-align: top;border-collapse: separate;border-spacing: 0;}
.pdf-main-box{width:500px;float:left;margin:0;padding:0px;}
.full-box{width:100%;float:left;margin:0;padding:0;}
.company-details{padding:0 10px;}
.company-details span{float:left;display:block;width:100%;margin:0 0 5px 0;padding-left:10px;font-size:10px;color:#000;line-height:15px;font-family: arial;}
.order-details{margin:0;padding:0;}
.order-details span{width:100%;float:left;font-weight:bold; text-transform:uppercase; font-size:10px;color:#000;}
.pull-left{float:left;}
.pull-right{float:right;}

.main-table{width:500px;border:0;border-collapse:collapse;border-spacing: 0;}
.main-table tr td{width:100%;}
.first-table{width:500px;border:0;font-size:11px;font-family: arial;border-spacing: 0;border-collapse:collapse;}
.first-table tr td{width:50%; vertical-align:bottom;}
html, body, div, span, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
table, caption, tbody, tfoot, thead, tr, th, td {
  margin: 0;
  padding: 0;
}
</style>
<body style="padding:0; margin:0">
    <table width="530" align="left" cellspacing="0" cellpadding="0" style="font-size:10px;">
        <tr>
            <td style="width:100%">
                <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" style="border-bottom:3px solid #000000; font-family: arial; font-size:11px;">
                    <tr>
                        <td width="50%" style="vertical-align:middle">
                            <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="25%" style="text-align:left;">
                                     <?php if(!empty($company_data[0]->photo))
                                        {?>
                                     <img src="{{$company_data[0]->photo}}" title="" alt="">
                                     <?php
                                      }
                                      else
                                      { ?>
                                       <img src="" title="" alt="">
                                      <?php
                                      }
                                      ?>
                                    </td>
                                    <td width="5%">&nbsp;</td>
                                    <td width="70%" style="vertical-align:middle; font-size:13px; color:#00000; font-family: arial; line-height:13px; color:#000; font-size:10px; text-align:left;">{{$company_data[0]->name}}<br>{{$company_data[0]->prime_address1}}<br>{{$company_data[0]->prime_address_city}}, {{$company_data[0]->prime_address_state}} {{$company_data[0]->prime_address_zip}}<br>{{$company_data[0]->phone}}<br><a href="{{$company_data[0]->url}}" target="_blank" style="text-decoration:none;color:#000;">{{$company_data[0]->url}}</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="50%" style="padding:10px 0; vertical-align:top; text-align:right; font-weight:bold; text-transform:uppercase; font-size:10px;">{{$order_data[0]->approval}} #{{$order_data[0]->display_number}}<br>Created On: <?php if(!empty($invoice_data)){?>{{$invoice_data[0]->created_date}}<?php } ?> <br>Job Name: {{$order_data[0]->order_name}}</td>
                    </tr>
                    <tr>
                     <td style="height:15px;">&nbsp;</td>
                    </tr>
                </table>
                <table width="100%" border="0" align="center" cellspacing="0" cellpadding="0" style="font-family: arial;">
                    <tr>
                        <td style="height:15px;">&nbsp;</td>
                    </tr>
                </table>
                <table border="0" width="100%" align="center" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="48%" style="vertical-align:middle;  border-radius:20px; position:relative; height:100px;">
                          <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td width="21" style="border:1px solid #666;" bgcolor="#303440"><img src="{{SITE_HOST}}/assets/images/etc/pdf-bill.png"  title="" alt="" ></td>
                              <td width="95%" valign="middle" style="border:1px solid #666; font-size:10px; height: 98px;">
                                <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td height="5">&nbsp;</td>
                                  </tr>
                                  <tr>
                                    <td><?php if(!empty($client_data)){?>{{$client_data[0]->first_name}} {{$client_data[0]->last_name}}<?php } ?><br>@foreach ($addresses['result'] as $address)<?php if($address->address_billing == '1') {?>{{$address->address}}<br>{{$address->street}}<br>{{$address->city}}<br>{{$address->state_name}}<br>{{$address->postal_code}}<?php } ?>@endforeach
                                    </td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                        <td width="4%"></td>
                        <td width="48%" style="vertical-align:middle; height:100px;">
                          <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td width="21" style="border:1px solid #666;" bgcolor="#303440"><img src="{{SITE_HOST}}/assets/images/etc/pdf-ship.png"   title="" alt=""></td>
                              <td width="95%" valign="middle" style="border:1px solid #666;font-size:10px; height: 98px;">
                                <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
                                  <tr>
                                    <td height="5">&nbsp;</td>
                                  </tr>
                                  <tr>
                                    <td><?php if(!empty($client_data)){?>{{$client_data[0]->first_name}} {{$client_data[0]->last_name}}<?php } ?><br>@foreach ($addresses['result'] as $address)<?php if($address->address_shipping == '1') {?>{{$address->address}}<br>{{$address->street}}<br>{{$address->city}}<br>{{$address->state_name}}<br>{{$address->postal_code}}<?php } ?>@endforeach</td>
                                  </tr>                                 
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                    </tr>
                </table>

                 <table width="100%" border="0" align="center" border="0" cellspacing="0" cellpadding="0" style="font-family: arial; font-size:10px; border-collapse:collapse;">
                  <tr>
                    <td height="15">&nbsp;</td>
                  </tr>
                 </table>
                <table width="100%" align="center" border="0" cellspacing="0" cellpadding="2" style="font-family: arial; font-size:10px; border-collapse:collapse;">
                    <tr>
                        <th width="10%" height="15" style="font-weight:bold; text-align:left;">Client PO</th>
                        <th width="15%" height="15" style="font-weight:bold; text-align:left;">Account Manager</th>
                        <th width="15%" height="15" style="font-weight:bold; text-align:left;">Terms</th>
                        <th width="15%" height="15" style="font-weight:bold; text-align:left;">Ship Via</th>
                        <th width="15%" height="15" style="font-weight:bold; text-align:left;">Ship Date</th>
                        <th width="15%" height="15" style="font-weight:bold; text-align:left;">In Hands Date</th>
                        <th width="15%" height="15" style="font-weight:bold; text-align:left;">Payment Due</th>
                    </tr>
                    <tr>
                        <td height="20" style="border:1px solid #000; text-align:left; line-height:13px; font-size: 10px;">{{$order_data[0]->custom_po}}</td>
                        <td height="20" style="border:1px solid #000; text-align:left; line-height:13px; font-size: 10px;">{{$order_data[0]->name}}</td>
                        <td height="20" style="border:1px solid #000; text-align:left; line-height:13px; font-size: 10px;"><?php if(!empty($invoice_data)){?>{{$invoice_data[0]->payment_terms}}<?php } ?></td>
                        <td height="20" style="border:1px solid #000; text-align:left; line-height:13px; font-size: 10px;">{{$order_data[0]->sns_shipping_name}}</td>
                        <td height="20" style="border:1px solid #000; text-align:left; line-height:13px; font-size: 10px;">{{$order_data[0]->date_shipped}}</td>
                        <td height="20" style="border:1px solid #000; text-align:left; line-height:13px; font-size: 10px;">{{$order_data[0]->in_hands_by}}</td>
                        <td height="20" style="border:1px solid #000; text-align:left; line-height:13px; font-size: 10px;"><?php if(!empty($invoice_data)){?>{{$invoice_data[0]->payment_due_date}}<?php } ?></td>
                    </tr>
                </table>
                <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" style="font-family: arial; font-size:10px; border-collapse:collapse;">
                  <tr>
                    <td height="15">&nbsp;</td>
                  </tr>
                 </table>


        <?php 

                if(!empty($all_design))
        {
            $len = count($all_design);
        ?>
          
          @foreach($all_design as $index => $design)
              
              

                      <table width="100%" align="center" border="0" cellspacing="0" cellpadding="2" style="font-family: arial; font-size:10px; border-collapse:collapse;">
                          
                        
                          <tr>
                              <th width="30%" height="15" style="font-weight:bold; text-align:left;">Garment/Item Description</th>
                              <th width="15%" height="15" style="font-weight:bold; text-align:left;">Color</th>
                              <th width="37%" height="15" style="font-weight:bold; text-align:left;">Size/Quantities</th>
                              <th width="8%" height="15" style="font-weight:bold; text-align:left;">Qty</th>
                              <th width="10%" height="15" style="font-weight:bold; text-align:left;">Unit Price</th>
                          </tr>
                         

                          <?php $count = 1;?>
                          @foreach($design->products as $product)

                          <?php if($count%2==0){$color_bg="#b7c2e0";} else {$color_bg="";} ?>
                          <tr style="background-color:<?php echo $color_bg?>;">
                              <td height="20" style="border:1px solid #000; text-align:left; font-size:9px; line-height:20px;">{{$product->id}} - {{$product->product_name}}</td>
                              <td height="20" style="border:1px solid #000; text-align:left; font-size:9px; line-height:20px;">{{$product->color_name}}</td>
                              <td height="20" style="font-weight:bold; border:1px solid #000; text-align:left; font-size:9px; line-height:20px;"><?php foreach ($product->sizeData as $colorData)
                              {
                               foreach ($colorData as $size)
                               {
                              ?>
                                {{$size->size}}:(<?php echo number_format($size->qnty); ?>)&nbsp;
                                <?php } }?>
                              </td>
                              <td height="20" style="border:1px solid #000; text-align:left; font-size:9px; line-height:20px;"><?php echo number_format($product->total_qnty) ?></td>
                              <td height="20" style="border:1px solid #000; text-align:left; font-size:9px;line-height:20px;">$<?php echo number_format($product->total_line_charge,2) ?></td>
                          </tr>
                           <?php $count++;?>
                          @endforeach
                          
                         
                          <tr>
                              <td height="20" colspan="3" style="border:1px solid #fff; border-right:1px solid #000; line-height:20px; font-size:9px; text-align:right; font-weight:bold">Total Qty&nbsp;&nbsp;</td>
                              <td height="20" style="border:1px solid #000; text-align:left; font-size:9px; line-height:20px;"><?php echo number_format($design->total_product_qnty) ?></td>
                              <td height="20" style="border:1px solid #fff; text-align:left; font-size:9px;">&nbsp;</td>
                          </tr>
                         
                      </table>
                           
                      
                      <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" style="font-family: arial; font-size:10px; border-collapse:collapse;">
                        <tr>
                          <td height="15">&nbsp;</td>
                        </tr>
                       </table>
                      <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0" style="font-family: arial; font-size:15px; border-collapse:collapse;">
                                <tr>
                                    <!-- <td width="60%" style="border-bottom:2px solid #000;"> -->
                                       <td width="63%">
                                        <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0" style="font-family: arial; font-size:9px; border-collapse:collapse;">
                                             @foreach($design->positions  as  $position)
                                            <tr>
                                                @foreach($position  as $positionData)
                                                <td width="50%">
                                                    <table width="100%" align="left" cellpadding="0" cellspacing="0">
                                                        <tr>
                                                            <td width="50%" valign="bottom" style="line-height:12px; font-size:9px;">Position:{{$positionData->position_name}}<br><?php if($positionData->color_stitch_count > 0) {?>Colors: {{$positionData->color_stitch_count}}<br><?php }?><?php if($positionData->placement_type_charge > 0) {?>{{$positionData->placement_type_name}} @ {{$positionData->placement_type_charge}}<br><?php }?><?php if($positionData->press_setup_qnty > 0){?>Press Setup @ {{$positionData->press_setup_qnty * $price_grid_data[0]->press_setup}} <br><?php }?><?php if($positionData->screen_fees_qnty > 0){?>Screen Fee @ {{$positionData->screen_fees_qnty * $price_grid_data[0]->screen_fees }}<br><?php }?><?php if($positionData->speciality_qnty > 0) {?>Speciality Ink @ {{$positionData->speciality_qnty * $price_grid_data[0]->specialty}}<?php }?><br><?php if($positionData->foil_qnty > 0) {?>Foil @ {{$positionData->foil_qnty * $price_grid_data[0]->foil}}<?php }?><br><?php if($positionData->number_on_dark_qnty > 0) {?>Number On Dark @ {{$positionData->number_on_dark_qnty * $price_grid_data[0]->number_on_dark}}<?php }?><br><?php if($positionData->number_on_light_qnty > 0) {?>Number On Light @ {{$positionData->number_on_light_qnty * $price_grid_data[0]->number_on_light}}<?php }?><br><?php if($positionData->oversize_screens_qnty > 0) {?>Oversize Screen @ {{$positionData->oversize_screens_qnty * $price_grid_data[0]->over_size_screens}}<?php }?><br><?php if($positionData->ink_charge_qnty > 0) {?>Ink Charge @ {{$positionData->ink_charge_qnty * $price_grid_data[0]->ink_changes}}<?php }?><br><?php if($positionData->discharge_qnty > 0) {?>Discharge @ {{$positionData->discharge_qnty * $price_grid_data[0]->discharge}}<?php }?><br>
                                                            </td>
                                                            <td width="50%" valign="bottom">
                                                                <?php if($positionData->position_image != '') {?>
                                                                <img src="{{$positionData->position_image}}" title="" alt="" width="60" height="60">
                                                                <?php }
                                                                else
                                                                {
                                                                ?>
                                                                    <img src="" height="60" width="60">
                                                                <?php
                                                                }
                                                                ?>
                                                            </td> 
                                                        </tr>
                                                        <tr>
                                                        <td width="100%" style="font-size:10px;">

                                                            <?php if($positionData->note != ''){?>
                                                            <dt><b>Notes</b></dt><dt><span style="font-size:10px;line-height:14px;float:none;">{{$positionData->note}}</span></dt>
                                                            <?php }?>

                                                        </td>
                                                        
                                                        </tr>
                                                        <tr>
                                                            <td width="100%" height="15">&nbsp;</td>
                                                        </tr>
                                                    </table>
                                                     
                                                     
                                                </td>
                                                <!-- <td width="1%"></td> -->
                                                @endforeach 
                                                
                                                </tr>              
                                                @endforeach
                                           
                                            </table>


                                            
                                         </td>

                    

                                        <td width="37%" style="vertical-align:top;">
                                            <?php if ($index === $len - 1) { ?>
                                            <table width="100%" align="center" border="0" cellspacing="0" cellpadding="1" style="font-family: arial; font-size:15px; border-collapse:collapse;">
                                                <tr>
                                                    <td height="20" width="60%" style="text-align:right; font-size:9px; font-weight:bold; line-height:20px;">Screens</td>
                                                    <td width="5%">&nbsp;</td>
                                                    <td height="20" width="35%" style="text-align:right; font-size:9px; line-height:20px; font-weight:bold; border:1px solid #000;">$<?php echo number_format($order_data[0]->screen_charge,2); ?>&nbsp;&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td height="20" style="text-align:right; font-size:9px; font-weight:bold; line-height:20px;">Press Setup</td>
                                                    <td  height="20" width="5%">&nbsp;</td>
                                                    <td height="20" style="text-align:right; font-size:9px; font-weight:bold; padding:6px; border:1px solid #000; line-height:20px;">$<?php echo number_format($order_data[0]->press_setup_charge,2); ?>&nbsp;&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td height="20" style="text-align:right; font-size:9px; font-weight:bold; padding:6px; line-height:20px;">Separations</td>
                                                    <td  width="5%">&nbsp;</td>
                                                    <td height="20" style="text-align:right; font-size:9px; font-weight:bold; padding:6px; border:1px solid #000; line-height:20px;">$<?php echo number_format($order_data[0]->separations_charge,2); ?>&nbsp;&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right; font-size:9px; font-weight:bold; padding:6px; line-height:20px;">Rush</td>
                                                    <td width="5%">&nbsp;</td>
                                                    <td style="text-align:right; font-size:9px; font-weight:bold; padding:6px; border:1px solid #000; line-height:20px;">$<?php echo number_format($order_data[0]->rush_charge,2); ?>&nbsp;&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right; font-size:9px; font-weight:bold; padding:6px;line-height:20px;">Discount</td>
                                                    <td width="5%">&nbsp;</td>
                                                    <td style="text-align:right; font-size:9px; font-weight:bold; padding:6px; border:1px solid #000;line-height:20px;">-$<?php echo number_format($order_data[0]->discount,2); ?>&nbsp;&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td height="20" style="text-align:right; font-size:9px; font-weight:bold; padding:6px; line-height:20px;">Order Total</td>
                                                    <td width="5%">&nbsp;</td>
                                                    <td height="20" style="text-align:right; font-size:9px; font-weight:bold; padding:6px; border:1px solid #000; line-height:20px;">$<?php echo number_format($order_data[0]->order_total,2); ?>&nbsp;&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td height="20" style="text-align:right; font-size:9px; font-weight:bold; padding:6px; line-height:20px;">Tax</td>
                                                    <td width="5%">&nbsp;</td>
                                                    <td height="20" style="text-align:right; font-size:9px; font-weight:bold; padding:6px; border:1px solid #000; line-height:20px;">$<?php echo number_format($order_data[0]->tax,2); ?>&nbsp;&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td height="20" style="text-align:right; font-size:9px; font-weight:bold; padding:6px; line-height:20px;">Grand Total</td>
                                                    <td width="5%">&nbsp;</td>
                                                    <td height="20" style="text-align:right; font-size:9px; font-weight:bold; padding:6px; border:1px solid #000; line-height:20px;">$<?php echo number_format($order_data[0]->grand_total,2); ?>&nbsp;&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td height="20" style="text-align:right; font-size:9px; font-weight:bold; padding:6px; line-height:20px;">Payments</td>
                                                    <td width="5%">&nbsp;</td>
                                                    <td height="20" style="text-align:right; font-size:9px; font-weight:bold; padding:6px; border:1px solid #000; line-height:20px;">$<?php echo number_format($order_data[0]->total_payments,2); ?>&nbsp;&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td height="20" style="text-align:right; font-size:9px; font-weight:bold; padding:6px; line-height:20px;">Balance Due</td>
                                                    <td width="5%">&nbsp;</td>
                                                    <td height="20" style="text-align:right; font-size:9px; font-weight:bold; padding:6px; border:1px solid #000; line-height:20px;">$<?php echo number_format($order_data[0]->balance_due,2); ?>&nbsp;&nbsp;</td>
                                                </tr>
                                            </table>
                                             <?php }?>
                                        </td>

                                    </tr>
                                </table>

                        <table width="100%" align="center" border="0" cellspacing="0" cellpadding="5" style="font-family: arial; font-size:10px; border-collapse:collapse;">
                          <tr>
                            <td height="15">&nbsp;</td>
                          </tr>
                         </table>

                         <?php if ($index != $len - 1) { ?>
                           <div style="page-break-before: always;"></div>
                         <?php } ?>
        
         
                 @endforeach
        <?php 
        }
        ?>

            </td>
        </tr>
    </table>
</body>
</html>