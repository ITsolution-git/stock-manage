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
</style>
</head>
<body style="padding:0; margin:0">

    <table class="header">
      <tr>
          <td align="left" width="20%"><img src="{{$company->companyphoto}}" title="Culture Studio" height="100" width="100" style="padding:10px;" alt="Culture Studio"></td>
          <td align="left" width="40%" class="font-bold">
             Job# {{$company->order_id}}<br>
             Job Name: {{$company->order_name}}<br>
             Client: {{$company->client_company}}
          </td>
          <td width="40%"  style="vertical-align:middle; border-radius:20px; position:relative; height:100px;">
              <table width="100%" class="border-b" align="left" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td style="width:20%; text-align:left;"><img style="display:block; line-height:0px;" src="{{SITE_HOST}}/assets/images/etc/ship.png" title="" alt="" height="100"></td>
                  <td valign="middle" style="width:80%; height:100px; font-size:10px;">
                    <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td height="15">&nbsp;</td>
                      </tr>
                      <tr><td class="font-bold">{{$company->street}} {{$company->address}}<br>{{$company->city}}, {{$company->state_name}} {{$company->postal_code}}</td></tr>
                      <tr>
                        <td height="15">&nbsp;</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
          </td>
      </tr>
    </table><br><br>
    
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
              <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;{{$company->account_manager }}</td>
              <td height="20" class="align-left line-height border-b"></td>
              <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;</td>
              <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;{{$company->date_shipped}}</td>
              <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;{{$company->in_hands_by}}</td>
              <td height="20" class="align-left line-height border-b">&nbsp;&nbsp;{{$company->payment_due_date}}</td>
          </tr>
      </table>
  
  <br><br>

    <table width="100%" class="align-center" border="0" cellspacing="0" cellpadding="0" style="font-family: arial; font-size:10px; border-collapse:collapse;">
        <tr>
            <th width="35%" class="align-left font-bold" height="15">Garment/Item Description</th>
            <th width="15%" class="align-left font-bold" height="15">Color</th>
            <th width="33%" class="align-left font-bold" height="15">Size/Quantities</th>
            <th width="8%"  class="align-left font-bold" height="15">Qty</th>
            <th width="8%"  class="align-left font-bold" height="15">Unit Price</th>
            
        </tr>
        <?php
            $total  =0;
            $count=1;
            foreach($pdf_product as $key=>$value) 
            {
              if($count%2==0){$color_bg="#b7c2e0";} else {$color_bg="";}
        ?>
        <tr style="background-color:<?php echo $color_bg; ?>;" >
            <td class="align-left line-height border-b" >&nbsp;&nbsp;{{$value['product_name']}}</td>
            <td class="align-left line-height border-b">&nbsp;&nbsp;{{$value['product_color']}}</td>
            <td class="align-left line-height border-b">
              <?php foreach ($value['summary'] as $key_col=>$val_col) { ?>
                {{$key_col}}-{{$val_col}}&nbsp;&nbsp;  
              <?php } ?>  
            </td>
            <td class="align-left  line-height border-b" >&nbsp;&nbsp;{{$value['total_product']}}</td>
            <?php $total +=$value['total_product']; ?>
            <td class="align-left  line-height border-b" >&nbsp;&nbsp;{{$value['price']}}</td>
        </tr>
         <?php $count++; } // LOOP END?>
        <tr>
            <td class="align-right font-bold line-height" colspan="3" style=" border-right:1px solid #000;">Total Qty&nbsp;&nbsp;</td>
            <td class="align-left border-b line-height">&nbsp;&nbsp;<?php echo $total; ?></td>
            <td class="">&nbsp;&nbsp;</td>
            
        </tr>
    </table>
  <br><br>

<?php foreach($data as $key_main=>$value_main)
{ 
  ?>
  <table>
     <tr>
      <td colspan="2" class="font-bold"><?php echo (!empty($value_main[0][0]->position_name))?$value_main[0][0]->position_name:''; ?></td>
      <!-- <td>w:<?php //echo $value_main[0][0]->screen_width; ?> X h:<?php //echo $value_main[0][0]->screen_height; ?></td> -->
    </tr> 
    <tr>
      <td width="40%">
        <img src="<?php echo $value_main[0][0]->mokup_logo; ?>" style="height:120px; width:130px;" >
      </td>
      <td width="60%">
          <table border="1">
            <tr>
              <td class="line-height" align="center"><b>Color</b></td>
              <?php if($value_main[0][0]->placement_type!='45') { ?>
              <td class="line-height" align="center"><b>Pantone</b></td>
              <td class="line-height" align="center"><b>Ink Type</b></td>
              <?php } else { ?>
              <td class="line-height" align="center"><b>COLOR CODE</b></td>
              <?php } ?>
            </tr>
            <?php foreach($value_main[0] as $key=>$value){ ?>
            <tr>
              <td class="line-height" align="center"><?php echo $value->color_name; ?></td>
              <?php if($value_main[0][0]->placement_type!='45') { ?>
              <td class="line-height" align="center"><?php echo $value->thread_color; ?></td>
              <td class="line-height" align="center"><?php echo $value->inq; ?></td>
              <?php } else { ?>
              <td class="line-height" align="center"><?php echo $value->inq; ?></td>
              <?php } ?>
            </tr>
            <?php } ?>
          </table>
      </td>
    </tr>
  </table>
<br><br>
  
  <!-- <hr style="border:1px solid #000;"> -->

<?php  if(($key_main+1)%3==0 && ($key_main+1)!=count($data))
{ ?>
<div style="page-break-before: always;"></div>
      <table class="header">
      <tr>
          <td align="left" width="20%"><img src="{{$company->companyphoto}}" title="Culture Studio" height="100" width="100" alt="Culture Studio"></td>
          <td align="left" width="40%" class="font-bold">
             Job# {{$company->order_id}}<br>
             Job Name: {{$company->order_name}}<br>
             Client: {{$company->client_company}}
          </td>
          <td width="40%"  style="vertical-align:middle; border-radius:20px; position:relative; height:100px;">
              <table width="100%" class="border-b" align="left" border="0" cellspacing="0" cellpadding="0">
                <tr>
                  <td style="width:20%; text-align:left;"><img style="display:block; line-height:0px;" src="{{SITE_HOST}}/assets/images/etc/ship.png" title="" alt="" height="100"></td>
                  <td valign="middle" style="width:80%; height:100px; font-size:10px;">
                    <table width="100%" align="left" border="0" cellspacing="0" cellpadding="0">
                      <tr>
                        <td height="15">&nbsp;</td>
                      </tr>
                      <tr><td class="font-bold">{{$company->street}} {{$company->address}}<br>{{$company->city}}, {{$company->state_name}} {{$company->postal_code}}</td></tr>
                      <tr>
                        <td height="15">&nbsp;</td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
          </td>
      </tr>
    </table><br><br>
<?php 
}

}
?>
<br><br>
<hr style="border:1px solid #000;">
<table>
    <tr>
        <td align="left"><b>Notes</b></td>
    </tr>
<?php  
foreach($data as $key_main=>$value_main)
{ ?>



<?php  if(!empty($value_main[1])) 
  {
?>
    
          
        <?php foreach($value_main[1] as $note_key=>$not_value){ ?>
          <tr>
              <td align="left"><?php echo $not_value ?></td>
          </tr>
        <?php } ?>
    
<?php 
  }  
    }
?>

</table>
<div style="page-break-before: always;"></div>

<table>
    <tr>
      <td width="50%" class="font-bold align-left">PLACEMENT</td>
      <td width="50%" class="font-bold align-right">ARTIST:</td>
    </tr>
  </table>
<table>
  <tr>
    <?php 
    $t_count = 1;
    foreach($data as $key_main=>$value_main)
    { 
    ?>
        <td width="22%" class="font-bold line-height"> <?php echo $value_main[0][0]->position_name; ?> : <?php echo $value_main[0][0]->line_per_inch; ?> </td>
        <td width="1%"></td>  
         <?php if($t_count%4==0){ ?>
            </tr></table>
            <table><tr><td width="1%"></td>
        <?php } ?>

    <?php $t_count++;  } ?>
  </tr>
</table>
<br>
<hr style="border:1px solid #000;">
<br>
<span class="font-bold line-height">  FRAME SIZE </span><br>
<table>
  <tr>
    <?php 
    $t_count = 1;
    foreach($data as $key_main=>$value_main)
    { 
    ?>
        <td width="22%" class="font-bold line-height"> <?php echo $value_main[0][0]->position_name." Frame"; ?> : <?php echo $value_main[0][0]->frame_size; ?> </td>
        <td width="1%"></td>  
         <?php if($t_count%4==0){ ?>
            </tr></table>
            <table><tr><td width="1%"></td>
        <?php } ?>

    <?php $t_count++;  } ?>
  </tr>
</table>
<br>
<hr style="border:1px solid #000;">
<br>
<?php if(!empty($options) && count($options)>0) {
?>
<table>
  <tr>
    <?php foreach($options as $key_img=>$img_val)
    { ?>
    <td width="10%" height="20">
        <img src="{{SITE_HOST}}/assets/images/etc/{{$key_img}}.png" title="Culture Studio"alt="Culture Studio">
    </td>
    <?php } ?>
  </tr>
</table>
<?php } ?>

<table>
  <tr>
    <td>
        <img src="{{$company->mokup_image}}" title="Culture Studio" height="525" width="535" alt="Culture Studio">
    </td>
  </tr>
</table>