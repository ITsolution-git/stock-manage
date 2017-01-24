<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="description" content="">
  <title>Orders Print</title>

</head>
<body style="padding:0; margin:0">
    <table width="500" align="center" border="0" cellspacing="0" cellpadding="0" style="font-size:10px;">
        <tr>
            <td style="width:100%">
                <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" style="border-bottom:3px solid #000000; font-family: arial; font-size:11px;">
                    <tr>
                        <td width="50%" style="vertical-align:middle">
                            <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td width="25%" style="text-align:left;"><img src="{{$company_detail[0]->photo}}" title="" alt=""></td>
                                    <td width="5%">&nbsp;</td>
                                    <td width="70%" style="vertical-align:middle; font-size:13px; color:#00000; font-family: arial; line-height:13px; color:#000; font-size:10px; text-align:left;">
                                      <span style="font-size:15px; line-height:15px;">
                                        <strong>{{$company_detail[0]->name}}</strong>                
                                      </span>
                                      <br/>
                                      <span>{{$company_detail[0]->prime_address1}}, {{$company_detail[0]->prime_address_city}}, {{$company_detail[0]->prime_address_state}}, <br/>USA - {{$company_detail[0]->zip}}<br />{{$company_detail[0]->url}}</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="50%" style="padding:10px 0; vertical-align:top; text-align:right; font-weight:bold; text-transform:uppercase; font-size:10px;">JOB# {{$shipping->display_number}}<br>JOB NAME: {{$shipping->name}}<br>CLIENT PO: {{$shipping->custom_po}}</td>
                    </tr>
                    <tr>
                      <td style="height:15px;">&nbsp;</td>
                    </tr>
                </table>
                <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="48%" style="vertical-align:middle; border:1px solid #000; border-radius:20px; position:relative; height:100px;">
                            <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="width:20%; text-align:left;"><img style="display:block; line-height:0px;" src="{{SITE_HOST}}/assets/images/etc/ship.png" title="" alt="" height="100"></td>
                                    <td valign="middle" style="width:80%; height:100px; font-size:10px;">
                                        <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td height="15">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span><strong>{{$shipping->description}}</strong></span><br />
                                                    <span>{{$shipping->address}} {{$shipping->address2}}</span><br/>
                                                    <span>{{$shipping->city}} {{$shipping->state}}</span><br />
                                                    <span>{{$shipping->zipcode}} {{$shipping->country}}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="15">&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td height="15">
                            <table>
                                <tr>
                                    <th width="100%" height="15" style="font-weight:bold; text-align:left;">Tracking Number(s)</th>
                                </tr>
                                @foreach ($shipping_boxes as $box)
                                <?php if($box->tracking_number != '')
                                {
                                ?>
                                    <tr>
                                        <td>{{$box->tracking_number}}</td>
                                    </tr>
                                <?php
                                }
                                ?>
                                @endforeach
                            </table>
                        </td>
                        <td height="15">
                            <table>
                                <tr>
                                    <td width="100%" height="15" style="font-weight:bold; text-align:left;">Ship Via: {{$shipping->shipping_type_name}}</td>
                                </tr>
                                <tr>
                                    <td width="100%" height="15" style="font-weight:bold; text-align:left;">Shipped On:</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                 <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" style="font-family: arial; font-size:10px; border-collapse:collapse;">
                  <tr>
                    <td height="15">&nbsp;</td>
                  </tr>
                 </table>
                <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0" style="font-family: arial; font-size:12px; border-collapse:collapse; font-weight:bold;">
                  <tr>
                    <td height="15">Total Shipped</td>
                  </tr>
                 </table>
                <table width="100%" align="center" border="0" cellspacing="0" cellpadding="2" style="font-family: arial; font-size:10px; border-collapse:collapse;">
                    <tr>
                        <th width="30%" height="15" style="font-weight:bold; text-align:left;">Garment/Item Description</th>
                        <th width="10%" height="15" style="font-weight:bold; text-align:left;">Color</th>
                        <th width="50%" height="15" style="font-weight:bold; text-align:left;">Size/Quantities</th>
                        <th width="10%" height="15" style="font-weight:bold; text-align:center;">Qty</th>
                    </tr>
                    <?php $count = 1; ?>
                    @foreach ($shipping_items as $items)
                        <?php
                        if($count % 2 != 0)
                        {
                        ?>
                            <tr>
                                <td height="20" style="border:1px solid #000; text-align:left; font-size:9px; line-height:20px;"><?php echo (!empty($items->brand_name))?$items->brand_name.' - ':''; ?>{{$items->name}}</td>
                                <td height="20" style="border:1px solid #000; text-align:left; font-size:9px; line-height:20px;">{{$items->color_name}}</td>
                                <td height="20" style="border:1px solid #000; text-align:left; font-size:9px; line-height:20px;">
                                    @foreach ($items->sizes as $sizedata)
                                        {{$sizedata->size}}-{{$sizedata->qnty}}&nbsp;&nbsp;
                                    @endforeach
                                </td>
                                <td height="20" style="border:1px solid #000; text-align:center; font-size:9px; line-height:20px;">{{$items->total_size_qnty}}</td>
                            </tr>
                        <?php 
                        }
                        else
                        {
                        ?>
                            <tr style="background-color:#b7c2e0;">
                                <td height="20" style="border:1px solid #000; text-align:left; font-size:9px; line-height:20px;"><?php echo (!empty($items->brand_name))?$items->brand_name.' - ':''; ?>{{$items->name}}</td>
                                <td height="20" style="border:1px solid #000; text-align:left; font-size:9px; line-height:20px;">{{$items->color_name}}</td>
                                <td height="20" style="border:1px solid #000; text-align:left; font-size:9px; line-height:20px;">
                                    @foreach ($items->sizes as $sizedata)
                                        {{$sizedata->size}}-{{$sizedata->qnty}}&nbsp;&nbsp;
                                    @endforeach
                                </td>
                                <td height="20" style="border:1px solid #000; text-align:center; font-size:9px; line-height:20px;">{{$items->total_size_qnty}}</td>
                            </tr>
                        <?php 
                        }
                        $count++; 
                        ?>
                    @endforeach
                    <tr>
                        <td height="20" colspan="3" style="border:1px solid #fff; border-right:1px solid #000; line-height:20px; font-size:9px; text-align:right; font-weight:bold">Total Qty&nbsp;&nbsp;</td>
                        <td height="20" style="border:1px solid #000; text-align:center; font-size:9px; line-height:20px;">&nbsp;&nbsp;{{$other_data['total_product_qnty']}}</td>
                    </tr>
                </table>
                <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" style="font-family: arial; font-size:10px; border-collapse:collapse;">
                  <tr>
                    <td height="15">&nbsp;</td>
                  </tr>
                 </table>
                 <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0" style="font-family: arial; font-size:12px; border-collapse:collapse; font-weight:bold;">
                  <tr>
                    <td height="15">Breakdown</td>
                  </tr>
                 </table>
                <table width="100%" align="center" border="0" cellspacing="0" cellpadding="2" style="font-family: arial; font-size:10px; border-collapse:collapse;">
                    <tr>
                        <th width="7%" height="15" style="font-weight:bold; text-align:center;">Box</th>
                        <th width="8%" height="15" style="font-weight:bold; text-align:left;">Size</th>
                        <th width="10%" height="15" style="font-weight:bold; text-align:left;">Color</th>
                        <th width="50%" height="15" style="font-weight:bold; text-align:left;">Description</th>
                        <th width="15%" height="15" style="font-weight:bold; text-align:left;">Defect/Spoil</th>
                        <th width="10%" height="15" style="font-weight:bold; text-align:center;">Qty</th>
                    </tr>
                    <?php $count = 1; ?>
                    @foreach ($shipping_boxes as $box)
                    <?php
                        if($count % 2 != 0)
                        {
                        ?>
                            <tr>
                                <td height="20" style="border:1px solid #000; text-align:center; font-size:9px; line-height:20px;">{{$count}}</td>
                                <td height="20" style="border:1px solid #000; text-align:left; font-size:9px; line-height:20px;">{{$box->size}}</td>
                                <td height="20" style="border:1px solid #000; text-align:left; font-size:9px; line-height:20px;">{{$box->color_name}}</td>
                                <td height="20" style="border:1px solid #000; text-align:left; font-size:9px; line-height:20px;">{{$box->product_name}}</td>
                                <td height="20" style="border:1px solid #000; text-align:center; font-size:9px; line-height:20px;">{{$box->md}}/{{$box->spoil}}</td>
                                <td height="20" style="border:1px solid #000; text-align:center; font-size:9px; line-height:20px;">{{$box->boxed_qnty}}</td>
                            </tr>
                        <?php 
                        }
                        else
                        {
                        ?>
                            <tr style="background-color:#b7c2e0;">
                                <td height="20" style="border:1px solid #000; text-align:center; font-size:9px; line-height:20px;">{{$count}}</td>
                                <td height="20" style="border:1px solid #000; text-align:left; font-size:9px; line-height:20px;">{{$box->size}}</td>
                                <td height="20" style="border:1px solid #000; text-align:left; font-size:9px; line-height:20px;">{{$box->color_name}}</td>
                                <td height="20" style="border:1px solid #000; text-align:left; font-size:9px; line-height:20px;">{{$box->product_name}}</td>
                                <td height="20" style="border:1px solid #000; text-align:center; font-size:9px; line-height:20px;">{{$box->md}}/{{$box->spoil}}</td>
                                <td height="20" style="border:1px solid #000; text-align:center; font-size:9px; line-height:20px;">{{$box->boxed_qnty}}</td>
                            </tr>
                        <?php 
                        }
                        $count++; 
                        ?>
                    @endforeach
                    <tr>
                        <td height="20" colspan="5" style="border:1px solid #fff; border-right:1px solid #000; line-height:20px; font-size:9px; text-align:right; font-weight:bold">Total Qty&nbsp;&nbsp;</td>
                        <td height="20" style="border:1px solid #000; text-align:center; font-size:9px; line-height:20px;">&nbsp;&nbsp;{{$other_data['total_qnty']}}</td>
                    </tr>
                </table>
                <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" style="font-family: arial; font-size:10px; border-collapse:collapse;">
                  <tr>
                    <td height="15" style="line-height:20px; font-size:9px; text-align:left; font-weight:bold">Total Boxes:{{$other_data['total_box']}}</td>
                  </tr>
                </table>
                <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" style="font-family: arial; font-size:10px; border-collapse:collapse;">
                  <tr>
                    <td height="15" style="line-height:20px; font-size:9px; text-align:left; font-weight:bold">Notes:{{$shipping->shipping_note}}</td>
                  </tr>
                </table>
                <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" style="font-family: arial; font-size:10px; border-collapse:collapse;">
                  <tr>
                    <td height="15">&nbsp;</td>
                  </tr>
                </table>
            </td>
        </tr>
    </table>
    <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" style="font-family: arial; font-size:15px; border-collapse:collapse;">
        <tr>
            <td><img src="{{SITE_HOST}}/assets/images/etc/footer-1.png" title="" alt=""></td>
        </tr>
    </table>
</body>
</html>