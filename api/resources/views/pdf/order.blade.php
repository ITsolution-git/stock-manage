<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="description" content="">
  <title>Orders Print</title>

  <style type="text/css">
  body,
  h1,h2,h3,h4,h5,h6,
  p,
  div,
  table,
  img {
    margin: 0;
    padding: 0;
  }   
  h1 {
    font-size: 24px;
    line-height: 24px;
  }
  p {
    font-size: 14px;
    line-height: 19px;
  }
  div {
    display: inline-block;
    float: left;
    vertical-align: top;
  }
  div.width100 {
    width: 100%;
    display: block;
    margin: 0;
    padding: 0;
  }
  div.width60 {
    width: 60%;
  } 
  div.width50 {
    width: 50%;
  }  
  div.width40 {
    width: 40%;
  }
  div.width35 {
    width: 35%;
  }
  div.width30 {
    width: 30%;   
  }
  div.width30l {
    float: left;
  }
  div.width40l {
    float: left;
  }
  div.width25 {
    width: 25%;
  }
  div.width15 {
    width: 15%;
  }

  div.prntBrdr {
    border:1px solid #000000;
    border-radius: 4px;
    padding-top:15px;
    padding-right:15px;
    padding-left:15px;
    padding-bottom:15px;
    text-align: left;  
    width: 100%;  
  }

  table, tr, td, th {
    margin: 0;
    padding: 0;
  }
  th {
    font-size: 12px;
  }
  td {
    font-size: 14px;        
  }
  .brdrBox {
    border:1px solid #000000;
    height: 14px;
    padding:2px; 
  }
  p.txtUpCash {
    font-size: 14px;
    text-transform: uppercase;
  }
  p.txtSmall {
    font-size: 10px;
  }
  div.prntBrdrBox {
      border:1px solid #000000;      
      height: 30px; 
      width: 45px;
    }
  </style>
</head>
<body>
  <div class="mainContainer">

    <div class="width100">
      <div class="width15">
        <img src="<?php echo url().'/uploads/company/'.$data["company_detail"][0]->company_logo;?>" alt="Logo" style="display:block; max-width:100%;" width="80" />
      </div>
      <div class="width35" style="float:left; text-align:center;">
        <h1>{{$data['company_detail'][0]->name}}</h1>
        <p>{{$data['company_detail'][0]->address}},</p>
        <p>{{$data['company_detail'][0]->city}}, {{$data['company_detail'][0]->state}},</p>
        <p>{{$data['company_detail'][0]->country}} - {{$data['company_detail'][0]->zip}}</p>        
        <p>{{$data['company_detail'][0]->url}}</p>
      </div>
      <div  class="width50" style="float:left; text-align:right;">
        <p><strong>Estimate #{{$data['order']->id}}</strong></p>
        <p><strong>Created On: {{$data['order']->created_date}}</strong></p>
        <p><strong>Job Name:  {{$data['order']->job_name}}</strong></p>
      </div>
    </div>

    <div class="width100" style="clear:both;">
      <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <td align="left" valign="top" width="48%">
            <div class="prntBrdr">
              <h2>BILL TO</h2>
              <p><strong>Misano Salon</strong></p>
              <p>justina kowalzyk</p>
            </div>
          </td>
          <td align="left" valign="top" width="4%">&nbsp;</td>
          <td align="right" valign="top" width="48%">
            <div class="prntBrdr">
              <h2>SHIP TO</h2>
              <p><strong>Misano Salon</strong></p>
              <p>justina kowalzyk</p>
            </div>
          </td>
        </tr>
      </table>
    </div>

    <div class="width100" style="clear:both;">
      <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th colspan="7">&nbsp;</th>
          </tr>
          <tr>
            <th align="left" valign="top" width="20%">Customer PO</th>
            <th align="left" valign="top" width="20%">Rep/Brand Co.</th>
            <th align="left" valign="top" width="10%">Terms</th>
            <th align="left" valign="top" width="10%">Ship Via</th>
            <th align="left" valign="top" width="10%">Ship Date</th>
            <th align="left" valign="top" width="20%">In Hands Date</th>
            <th align="left" valign="top" width="20%">Payment Due</th>
          </tr>
          <tr>
            <th colspan="7">&nbsp;</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td align="left" valign="top" class="brdrBox" width="20%"></td>
            <td align="left" valign="top" class="brdrBox" width="20%">&nbsp;Dark Room</td>
            <td align="left" valign="top" class="brdrBox" width="10%"></td>
            <td align="left" valign="top" class="brdrBox" width="10%"></td>
            <td align="left" valign="top" class="brdrBox" width="10%"></td>
            <td align="left" valign="top" class="brdrBox" width="20%"></td>
            <td align="left" valign="top" class="brdrBox" width="20%"></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="width100" style="clear:both;">
      <table border="0" cellpadding="0" cellspacing="0" width="100%" class="garmentDetails">
        <thead>
          <tr>
            <th colspan="6">&nbsp;</th>
          </tr>
          <tr>
            <th align="left" valign="top" width="35%">Garment / Item Description</th>
            <th align="center" valign="top" width="10%">Color</th>
            <th align="left" valign="top" width="30%">Sizes / Quantities</th>
            <th align="center" valign="top" width="5%">OS</th>
            <th align="center" valign="top" width="10%">Qnty</th>
            <th align="right" valign="top" width="10%">Unit Price</th>                
          </tr>
          <tr>
            <th colspan="6">&nbsp;</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($data['order_line'] as $key => $orderline)
          <tr>
            <td align="left" valign="top" class="brdrBox" width="35%">{{$orderline->product_name}} / <br/> {{$orderline->product_description}}</td>
            <td align="center" valign="top" class="brdrBox" width="10%">{{$orderline->color_name}}</td>
            <td align="left" valign="top" class="brdrBox" width="30%">

              @foreach ($orderline->items as $key => $order_size_array)
              <?php if($order_size_array->qnty > 0){?>
              &nbsp;{{$order_size_array->size}} : {{($order_size_array->qnty)}}&nbsp;
              <?php }?>
              @endforeach

            </td>
            <td align="center" valign="top" class="brdrBox" width="5%">{{$orderline->os}}</td>
            <td align="center" valign="top" class="brdrBox" width="10%">{{$orderline->qnty}}</td>
            <td align="right" valign="top" class="brdrBox" width="10%">{{$orderline->peritem}}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="width100" style="overflow:hidden; position:relative; clear:both;">
                
        @foreach ($data['order_item'] as $orderitem)
        <?php if($orderitem->selected == '1'){?>        
          <p>{{$orderitem->item}} @ {{$orderitem->charge}} </p>        
        <?php }?>
        @endforeach
        
        <p style="padding-top:10px; padding-bottom:10px;">Order Item @ 500</p>

        <?php $count = 1; ?>
        @foreach ($data['order_position'] as $position)
        
        <?php if($count <= '6'){?>          
        <div style="float:left; width:16%; margin-left:1px;">
        <?php $pos_id = $position->position_id; 
        $placement_id = $position->placement_type;
        $display_label = '';

        if($data['order_misc']->placement_type->$placement_id->id == '43'){
        $display_label = 'Colors:';
        }elseif ($data['order_misc']->placement_type->$placement_id->id == '45') {
        $display_label = 'Stiches:';
        } 

        ?>
        <p>Position: {{$data['order_misc']->position->$pos_id->value}} &nbsp;<strong><?php echo $display_label?>{{$position->color_stitch_count}}</strong></p>

        <?php if($position->discharge_qnty > '0'){
        $discharge_price = $position->discharge_qnty * $data['price_grid']->discharge; ?>
        <p>Discharge Ink @ <?php echo $discharge_price;?></p>

        <?php }?>


        <?php if($position->speciality_qnty > '0'){
        $speciality_price = $position->speciality_qnty * $data['price_grid']->specialty; ?>
        <p>Speciality Ink @ <?php echo $speciality_price;?></p>

        <?php }?>


        <?php if($position->foil_qnty > '0'){
        $foil_price = $position->foil_qnty * $data['price_grid']->foil; ?>
        <p>Foil @ <?php echo $foil_price;?></p>

        <?php }?>


        <?php if($position->ink_charge_qnty > '0'){
        $ink_price = $position->ink_charge_qnty * $data['price_grid']->ink_changes; ?>
        <p>Ink Charge @ <?php echo $ink_price;?></p>

        <?php }?>


        <?php if($position->number_on_dark_qnty > '0'){
        $dark_price = $position->number_on_dark_qnty * $data['price_grid']->number_on_dark; ?>
        <p># on Dark @ <?php echo $dark_price;?></p>

        <?php }?>

        <?php if($position->number_on_light_qnty > '0'){
        $light_price = $position->number_on_light_qnty * $data['price_grid']->number_on_light; ?>
        <p># on Light @ <?php echo $light_price;?></p>

        <?php }?>


        <?php if($position->oversize_screens_qnty > '0'){
        $oversize_price = $position->oversize_screens_qnty * $data['price_grid']->over_size_screens; ?>
        <p>Oversize Screen @ <?php echo $oversize_price;?></p>

        <?php }?>


        <?php if($position->press_setup_qnty > '0'){
        $press_setup_price = $position->press_setup_qnty * $data['price_grid']->press_setup; ?>
        <p>Press Setup @ <?php echo $press_setup_price;?></p>

        <?php }?>


        <?php if($position->screen_fees_qnty > '0'){
        $screen_fees_price = $position->screen_fees_qnty * $data['price_grid']->screen_fees; ?>
        <p>Screen Fee @ <?php echo $screen_fees_price;?></p>

        <?php }?>

        <?php

        if($position->color_stitch_count > '0'){


        if($data['order_misc']->placement_type->$placement_id->id == '43'){

        foreach ($data['price_screen_primary'] as $price_screen_primary) {

        if($data['total_qty'] >= $price_screen_primary->range_low && $data['total_qty'] <= $price_screen_primary->range_high)
        {
        $price_field = 'pricing_'.$position->color_stitch_count.'c';
        $screen_price_calc =  $price_screen_primary->$price_field; ?>
        <p>Screen Print @ <?php echo $screen_price_calc;?></p>
        <?php }
        }
        }
        }
        ?>
        <p>&nbsp;</p>
        </div>
        <?php }
        $count++; ?>
        @endforeach
      </div>
    </div>
      
    <div class="width100" style="position:relative; clear:both; padding-top:20px; padding-bottom:20px; border:2px solid #ccc;">
      <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <td align="center" valign="top" width="49%">
            <img src="<?php echo url().'/images/c1.jpg';?>" alt="Logo" style="display:block;" width="150" />
          </td>
          <td align="left" valign="top" width="2%">&nbsp;</td>
          <td align="center" valign="top" width="49%">
            <img src="<?php echo url().'/images/c2.jpg';?>" alt="Logo" style="display:block;" width="150" />
          </td>
        </tr>
      </table>
    </div>

    <div class="width100" style="position:relative; clear:both;">
      <div class="width60" style="float:left">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>        
          <tr>
            <th align="left" valign="top" width="50%">Garment Link</th>
            <th align="left" valign="top" width="50%">NOTES</th>
          </tr>

          <tr>
            <td align="left" valign="top" width="50%">
              {{$data['order']->garment_link}}
            </td>
            <td align="left" valign="top" width="50%">
              <textarea class="brdrBox" rows="10">{{$data['order']->invoice_note}}</textarea>
            </td>
          </tr>          
        </table>
      </div>

      <div class="width40" style="float:right">
          <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
              <td align="right" valign="top" width="45%">Total Qnty</td>
              <td align="left" valign="top" width="5%">&nbsp;</td>
              <td align="right" valign="top" class="brdrBox" width="50%">{{$data['total_qty']}}&nbsp;</td>
            </tr>

            <tr>
              <td align="right" valign="top">Screens</td>
              <td align="left" valign="top">&nbsp;</td>
              <td align="right" valign="top" class="brdrBox">{{$data['order']->screen_charge}}&nbsp;</td>
            </tr>

            <tr>
              <td align="right" valign="top">Press Setup</td>
              <td align="left" valign="top">&nbsp;</td>
              <td align="right" valign="top" class="brdrBox">{{$data['order']->press_setup_charge}}&nbsp;</td>
            </tr>

            <tr>
              <td align="right" valign="top">Digitize</td>
              <td align="left" valign="top">&nbsp;</td>
              <td align="right" valign="top" class="brdrBox">{{$data['order']->digitize_charge}}&nbsp;</td>
            </tr>

            <tr>
              <td align="right" valign="top">Art Work</td>
              <td align="left" valign="top">&nbsp;</td>
              <td align="right" valign="top" class="brdrBox">{{$data['order']->artwork_charge}}&nbsp;</td>
            </tr>

            <tr>
              <td align="right" valign="top">Separations</td>
              <td align="left" valign="top">&nbsp;</td>
              <td align="right" valign="top" class="brdrBox">{{$data['order']->separations_charge}}&nbsp;</td>
            </tr>
            <tr>
              <td align="right" valign="top">Distribution</td>
              <td align="left" valign="top">&nbsp;</td>
              <td align="right" valign="top" class="brdrBox">{{$data['order']->distribution_charge}}&nbsp;</td>
            </tr>

            <tr>
              <td align="right" valign="top">Shipping</td>
              <td align="left" valign="top">&nbsp;</td>
              <td align="right" valign="top" class="brdrBox">{{$data['order']->shipping_charge}}&nbsp;</td>
            </tr>

            <tr>
              <td align="right" valign="top">new lab</td>
              <td align="left" valign="top">&nbsp;</td>
              <td align="right" valign="top" class="brdrBox">{{$data['order']->setup_charge}}&nbsp;</td>
            </tr>

            <tr>
              <td align="right" valign="top">Discount</td>
              <td align="left" valign="top">&nbsp;</td>
              <td align="right" valign="top" class="brdrBox">{{$data['order']->discount}}&nbsp;</td>
            </tr>

            <tr>
              <td align="right" valign="top">Order Total</td>
              <td align="left" valign="top">&nbsp;</td>
              <td align="right" valign="top" class="brdrBox">{{$data['order']->sales_order_total}}&nbsp;</td>
            </tr>

            <tr>
              <td align="right" valign="top">Tax</td>
              <td align="left" valign="top">&nbsp;</td>
              <td align="right" valign="top" class="brdrBox">{{$data['order']->tax}}&nbsp;</td>
            </tr>

            <tr>
              <td align="right" valign="top">Grand Total</td>
              <td align="left" valign="top">&nbsp;</td>
              <td align="right" valign="top" class="brdrBox">{{$data['order']->grand_total}}&nbsp;</td>
            </tr>

            <tr>
              <td align="right" valign="top">Payments/Deposit</td>
              <td align="left" valign="top">&nbsp;</td>
              <td align="right" valign="top" class="brdrBox">{{$data['order']->total_payments}}&nbsp;</td>
            </tr>

            <tr>
              <td align="right" valign="top">Balance Due</td>
              <td align="left" valign="top">&nbsp;</td>
              <td align="right" valign="top" class="brdrBox">{{$data['order']->balance_due}}&nbsp;</td>
            </tr>
          </table>
      </div>      
    </div>

    <div class="width100" style="position:relative; clear:both; text-align:center;">
      <p class="txtUpCash">Please respond with "Approved, Name and Date",  for order to be placed into production</p>
      <p class="txtSmall">Unless document states final invoice the amount listed may not be the total due. Shipping, tax and any additions during the art or press stages may result in a change of price.</p>
    </div>
  </div>
</body>
</html>