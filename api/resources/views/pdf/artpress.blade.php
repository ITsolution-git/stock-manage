<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="description" content="">
  <title>Orders Print</title>
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
</head>
<body style="padding:0; margin:0;border:none;">
    <table class="header">
    	<tr>
          <td>
            <div style="width:100%;float:left;padding:15px 0;">
              <table>
                <tr>
                  <td align="left" width="20%"><img src="{{$color[0]->companyphoto}}" height="100" title="Culture Studio" alt="Culture Studio" /></td>
                  <td align="left" width="40%" style=" font-weight:bold">
                     Order Id: #{{$color[0]->order_id}}<br>
                     Job Name: {{$color[0]->order_name}}<br>
                     Client: {{$color[0]->client_company}}
                  </td>
                  <td width="40%" style="vertical-align:middle; height:100px;">
                    <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td width="21" style="border:1px solid #666;" bgcolor="#303440"><img src="{{SITE_HOST}}/assets/images/etc/pdf-ship.png"   title="" alt=""></td>
                        <td width="95%" valign="middle" style="border:1px solid #666;font-size:10px; height: 98px;">
                          <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                              <td height="5">&nbsp;</td>
                            </tr>
                            <tr><td>{{$color[0]->street}} {{$color[0]->address}}<br>{{$color[0]->city}}, {{$color[0]->state_name}} {{$color[0]->postal_code}}</td></tr>                                 
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </div>
            <div class="diff-border">&nbsp;</div>
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
              <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;{{$color[0]->custom_po}}</td>
              <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;{{$color[0]->account_manager}}</td>
              <td height="20" class="align-left line-height border-b"></td>
              <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;</td>
              <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;{{$color[0]->date_shipped}}</td>
              <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;{{$color[0]->in_hands_by}}</td>
              <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;{{$color[0]->payment_due_date}}</td>
          </tr>
      </table>
      <div style="height:10px;width:100%">&nbsp;</div>
      </td>
    </tr>
    
    <tr>
      <td>
        <table width="100%" class="align-center" border="0" cellspacing="0" cellpadding="0" style="font-family: arial; font-size:10px; border-collapse:collapse;">
                    <tr>
                        <th width="40%" class="align-left font-bold" height="15">Garment/Item Description</th>
                        <th width="15%" class="align-left font-bold" height="15">Color</th>
                        <th width="37%" class="align-left font-bold" height="15">Size/Quantities</th>
                        <th width="8%"  class="align-left font-bold" height="15">Qty</th>
                        
                    </tr>
                    <?php
                        $total  =0;
                        $count=1;
                        foreach($size as $key=>$value) 
                        {
                          if($count%2==0){$color_bg="#b7c2e0";} else {$color_bg="";}
                    ?>
                    <tr style="background-color:<?php echo $color_bg; ?>;" >
                        <td height="20" class="align-left line-height border-b" >&nbsp;&nbsp;{{$value['product_name']}}</td>
                        <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;{{$value['product_color']}}</td>
                        <td height="20" class="align-left line-height border-b">
                          <?php foreach ($value['summary'] as $key_col=>$val_col) { ?>
                            {{$key_col}}-{{$val_col}}&nbsp;&nbsp;  
                          <?php } ?>  
                        </td>
                        <td height="20" class="align-left  line-height border-b" >&nbsp;&nbsp;{{$value['total_product']}}</td>
                        <?php $total +=$value['total_product']; ?>
                    </tr>
                     <?php $count++; } // LOOP END?>
                    <tr>
                      <td class="align-right font-bold line-height" colspan="3" style=" border-right:1px solid #000;">Total Qty&nbsp;&nbsp;</td>
                      <td class="align-left border-b line-height">&nbsp;&nbsp;<?php echo $total; ?></td>
                    </tr>
        </table>
        <div style="height:10px;width:100%">&nbsp;</div>
      </td>
    </tr>

    <tr>
      <td>
        <table>
            <tr>
              <td width="10%" class="align-center border-b" style="height:20px;"></td>
              <td width="2%"></td>
              <td width="10%" class="align-center border-b" style="height:20px;"></td>
              <td width="2%"></td>
              <td width="10%" class="align-center border-b" style="height:20px;"></td>
              <td width="2%"></td>
              <td width="10%" class="align-center border-b" style="height:20px;"></td>
              <td width="2%"></td>
              <td width="10%" class="align-center border-b" style="height:20px;"></td>
            </tr>
            <tr>
              <td class="align-center font-bold border-w"  >Prod Mgr</td>
              <td ></td>
              <td  class="align-center font-bold border-w" >Press Lead</td>
              <td ></td>
              <td  class="align-center font-bold border-w" >Belt</td>
              <td ></td>
              <td  class="align-center font-bold border-w" >QC</td>
              <td ></td>
              <td class="align-center font-bold border-w" >Ship/Pack</td>
            </tr>
            <br>
              <hr style="border:1px solid #000;">
            <?php if(!empty($options) && count($options)>0) {
              ?>
              <tr>
                <?php foreach($options as $key_img=>$img_val)
                { ?>
                <td width="10%" height="20">
                    <img src="{{SITE_HOST}}/assets/images/etc/{{$key_img}}.png" title="Culture Studio"alt="Culture Studio">
                </td>
                <?php } ?>
              </tr>
              <?php } ?>


        </table>
        
      </td>
    </tr>

    <tr>
      <td>
          <div style="height:10px;width:100%">&nbsp;</div>
          <table>
    <tr>
      <td colspan="3" class="align-">&nbsp;&nbsp;<b><?php echo (!empty($color[0]->position_name))?$color[0]->position_name:''; ?> -
      <?php echo (!empty($color[0]->screen_width))?$color[0]->screen_width:'-'; ?>"W X <?php echo (!empty($color[0]->screen_height))?$color[0]->screen_height:'-'; ?>"H </b><br></td>
    </tr>
    <!-- <tr >
       <td width="30%" class="align-center"  style="border: 1px solid #000;">ART</td>
       <td width="70%" class="align-center" style="border: 1px solid #000;">PRESS SETUP</td>
    </tr> -->
    <tr> 
      <td width="30%"> 
        <table>
          
          <tr>
          <td class="align-center">
          <img src="{{$color[0]->mokup_logo}}" title="Culture Studio" style="height: 200px" alt="Culture Studio">
          </td></tr>

        </table>
      </td>
      <td width="70%" >
          <table>
           
            <tr style="font-weight: 12px">
              <td class="align-center font-bold" ><b>#HEAD</b></td>
              <td class="align-center font-bold" ><b>COLOR</b></td>
            <?php if($color[0]->placement_type!='45') { ?>
              <td class="align-center font-bold" ><b>PANTONE</b></td>
              <td class="align-center font-bold" ><b>INK TYPE</b></td>
              <td class="align-center font-bold" ><b>SQUEEGEE</b></td>
              <td class="align-center font-bold" ><b>STROKE</b></td>
            <?php } else { ?>
              <td class="align-center font-bold"><b>COLOR CODE</b></td>
            <?php } ?>
            </tr>
             
        <?php foreach($color as $key=>$value) {?>    
            <tr>
              <td class="align-center line-height" border="1">{{$key+1}}</td>
              <td class="align-center line-height" border="1">{{$value->color_name}}</td>
            <?php if($color[0]->placement_type!='45') { ?>
              <td class="align-center line-height" border="1">{{$value->thread_color}}</td>
              <td class="align-center line-height" border="1">{{$value->inq}}</td>
              <td class="align-center line-height" border="1">{{$value->squeegee}}</td>
              <td class="align-center line-height" border="1">{{$value->stroke}}</td>
            <?php } else { ?>
                <td class="align-center line-height" border="1">{{$value->color_code}}</td>
            <?php } ?>

            </tr>
        <?php } ?>
          </table>
      </td>
    </tr>
    

    <tr>
      <td><br />&nbsp;&nbsp;<b>Note:</b> {{$color[0]->note}}</td>
    </tr>
  
  </table>
      </td>
    </tr>
  </table>
</body>
</html>
