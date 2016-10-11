
<html>
<head>
<style>
    @page { margin: 180px 50px; }
    #header { position: fixed; left: 0px; top: -180px; right: 0px; height: 150px; background-color: orange; text-align: center; }
    #footer { position: fixed; left: 0px; bottom: -180px; right: 0px; height: 150px; background-color: lightblue; }
    #footer .page:after { content: counter(page, upper-roman); }
    img{width: 250px; height:220px;}
  </style>
  <title>Order Info</title>
</head>

<body>
    <table class="header">
    	<tr>
      		<td align="left" width="20%"><img src="{{$color[0]->companyphoto}}" title="Culture Studio" height="100" width="100" alt="Culture Studio"></td>
          <td align="left" width="40%">
             Job# {{$color[0]->order_id}}<br>
             Job Name: {{$color[0]->order_name}}<br>
             Client: {{$color[0]->client_company}}
          </td>
          <td align="left"  width="40%" style="height:100px;border:1px solid #000;border-radius:15px;">
              <table >
              <tr><td></td></tr>
                 <tr>
                      <td align="left">
                          <b>SHIP TO :</b>
                      </td>
                 </tr>              
                 <tr>
                  <td align="left">
                      {{$color[0]->street}} {{$color[0]->address}}
                     
                  </td>
                  </tr>
                  <tr>
                    <td align="left">
                        
                        {{$color[0]->city}}, {{$color[0]->state_name}} {{$color[0]->postal_code}}
                    </td>
                 </tr>
              </table>
          </td>
    	</tr>
    </table><br>
    <hr style="border:1px solid #000;">
    <br>
  <table style="margin-top:15px;">
  
    <tr>
      <td align="center"><b>Client PO</b></td>
      <td align="center"><b>Account Manager</b></td>
      <td align="center"><b>Term</b></td>
      <td align="center"><b>Ship Via</b></td>
      <td align="center"><b>Ship Date</b></td>
      <td align="center"><b>Hands Date</b></td>
      <td align="center"><b>Payment Due</b></td>
    </tr>
     <tr>
      <td align="center" border="1">{{$color[0]->custom_po}}</td>
      <td align="center" border="1">{{$color[0]->account_manager}}</td>
      <td align="center" border="1"> </td>
      <td align="center" border="1"></td>
      <td align="center" border="1"> {{$color[0]->date_shipped}}</td>
      <td align="center" border="1"> {{$color[0]->in_hands_by}}</td>
      <td align="center" border="1"> $<?php echo round($color[0]->balance_due, 2); ?></td>
    </tr>
 
  </table><br><br>
  <table style="margin-top:15px;">
  
    <tr>
      <td align="center" width="40%"><b>Garments / Item description</b></td>
      <td align="center" width="10%"><b>color</b></td>
      <td align="center" width="40%"><b>Sizes/ Quantities</b></td>
      <td align="center" width="10%"><b>Qty</b></td>
    </tr>

 <?php
  $total  =0;
  foreach($size as $key=>$value) {?>
  <tr>
    <td width="40%" border="1">{{$value['product_name']}}</td>
    <td align="center" width="10%" border="1">{{$value['product_color']}}</td>
    <td align="left" width="40%" border="1">
    <?php foreach ($value['summary'] as $key_col=>$val_col) { ?>
      {{$key_col}}-{{$val_col}}  
    <?php } ?>  
    </td>
    <td align="center" width="10%" border="1">{{$value['total_product']}}</td> 
    <?php $total +=$value['total_product']; ?>
  </tr>
  <?php } ?>
  <tr>
      <td colspan="3" ></td>
      <td align="center" border="1"> <?php echo $total; ?></td>
  </tr>
  </table>
<br><br>
<table style="margin-top:15px;">
    <tr>
      <td width="10%" align="center" style="height:20px;border:1px solid #000;border-radius:15px;"></td>
      <td width="10%" align="center" style="height:20px;border:1px solid #000;border-radius:15px;"></td>
      <td width="10%" align="center" style="height:20px;border:1px solid #000;border-radius:15px;"></td>
      <td width="10%" align="center" style="height:20px;border:1px solid #000;border-radius:15px;"></td>
      <td width="10%" align="center" style="height:20px;border:1px solid #000;border-radius:15px;"></td>
    </tr>
    <tr>
      <td width="10%" align="center" >Prod Mgr</td>
      <td width="10%" align="center">Press Lead</td>
      <td width="10%" align="center">Belt</td>
      <td width="10%" align="center">QC</td>
      <td width="10%" align="center">Ship/Pack</td>
    </tr>
</table>
<br><br>
  <table style="margin-top:15px;">
  	<!-- <tr>
      <td colspan="3" align="center">PRESS DETAILS</td>
    </tr>
    <tr >
       <td width="30%" align="center"  style="border: 1px solid #000;">ART</td>
       <td width="70%" align="center" style="border: 1px solid #000;">PRESS SETUP</td>
    </tr> -->
    <tr style="margin-top: 20px;"> 
    	<td width="30%" style="border: 1px solid #000;"> 
    		<table>
    			
    			<tr><td align="center"><img src="{{$color[0]->mokup_logo}}" title="Culture Studio" height="300" width="300" alt="Culture Studio"></td></tr>

    		</table>
    	</td>
    	<td width="70%" >
          <table>
           
            <tr style="font-weight: 12px">
              <td align="center"><b>#POS</b></td>
              <td align="center"><b>COLOR</b></td>
            <?php if($color[0]->placement_type!='45') { ?>
              <td align="center"><b>PANTONE</b></td>
              <td align="center"><b>INK TYPE</b></td>
              <td align="center"><b>SQUEEGEE</b></td>
              <td align="center"><b>STROKE</b></td>
            <?php } else { ?>
              <td align="center"><b>COLOR CODE</b></td>
            <?php } ?>
            </tr>
             
        <?php foreach($color as $key=>$value) {?>    
            <tr>
              <td align="center" border="1">{{$key+1}}</td>
              <td align="center" border="1">{{$value->color_name}}</td>
            <?php if($color[0]->placement_type!='45') { ?>
              <td align="center" border="1">{{$value->thread_color}}</td>
              <td align="center" border="1">{{$value->inq}}</td>
              <td align="center" border="1">{{$value->squeegee}}</td>
              <td align="center" border="1">{{$value->stroke}}</td>
            <?php } else { ?>
                <td align="center" border="1">{{$value->color_code}}</td>
            <?php } ?>

            </tr>
        <?php } ?>
          </table>
      </td>
    </tr>
    <tr>
    <br><br>
      <td colspan="8"><b>Note:</b> {{$color[0]->note}}</td>
    </tr>
  
  </table>
 
</body>
</html>
