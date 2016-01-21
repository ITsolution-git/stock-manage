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
    font-size: 13px;
    line-height: 16px;
    width: 100%;
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
    padding-top:8px;
    padding-right:8px;
    padding-left:8px;
    padding-bottom:8px;
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
    height: 40px; 
    width: 100%;
    margin-left: auto;
    margin-right: auto;
  }
  p.chkboxes {
    height: 40px;
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

              <?php $company_name='';;?>
             @foreach ($data['all_company'] as $key => $companydata)
             <?php 
             if($companydata->client_id == $data['order']->client_id){
                 $company_name = $companydata->client_company;
             }?>
             @endforeach


             <?php $main_contact_name='';?>
             @foreach ($data['client_main_data'] as $key => $maindata)
             <?php 
             if($maindata->contact_main == '1'){
                 $main_contact_name = $maindata->first_name.' '.$maindata->last_name;
             }?>
             @endforeach



              <h2>BILL TO</h2>
              <p><strong>{{$company_name}}</strong></p>
              <p>{{$main_contact_name}}</p>
            </div>
          </td>
          <td align="left" valign="top" width="4%">&nbsp;</td>
          <td align="right" valign="top" width="48%">
            <div class="prntBrdr">
              <h2>SHIP TO</h2>
              <p><strong>{{$company_name}}</strong></p>
              <p>{{$main_contact_name}}</p>
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
             <?php $staff_name='';;?>
             @foreach ($data['staff_list'] as $key => $staffdata)
             <?php 
             if($staffdata->id == $data['order']->sales_id){
                 $staff_name = $staffdata->first_name.' '.$staffdata->last_name;
             }?>
             @endforeach
            <td align="left" valign="top" class="brdrBox" width="20%"></td>
            <td align="left" valign="top" class="brdrBox" width="20%"><?php echo $staff_name;?></td>
            <td align="left" valign="top" class="brdrBox" width="10%"></td>
            <td align="left" valign="top" class="brdrBox" width="10%"></td>
            <td align="left" valign="top" class="brdrBox" width="10%">{{$data['order']->shipping_by}}</td>
            <td align="left" valign="top" class="brdrBox" width="20%">{{$data['order']->in_hands_by}}</td>
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
            <td align="left" valign="top" class="brdrBox" width="35%" style="font-size:12px;">{{$orderline->product_name}} / {{$orderline->product_description}}</td>
            <td align="center" valign="top" class="brdrBox" width="10%" style="font-size:12px;">{{$orderline->color_name}}</td>
            <td align="left" valign="top" class="brdrBox" width="30%" style="font-size:12px;">

              @foreach ($orderline->items as $key => $order_size_array)
              <?php if($order_size_array->qnty > 0){?>
              &nbsp;{{$order_size_array->size}} : {{($order_size_array->qnty)}}&nbsp;
              <?php }?>
              @endforeach

            </td>
            <td align="center" valign="top" class="brdrBox" width="5%" style="font-size:12px;">{{$orderline->os}}</td>
            <td align="center" valign="top" class="brdrBox" width="10%" style="font-size:12px;">{{$orderline->qnty}}</td>
            <td align="right" valign="top" class="brdrBox" width="10%" style="font-size:12px;">{{$orderline->peritem}}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="width100" style="overflow:hidden; position:relative; clear:both; margin-top:15px;">
         
        <p style="padding-top:10px; padding-bottom:10px;">       
        @foreach ($data['order_item'] as $orderitem)
        <?php if($orderitem->selected == '1'){?>        
          <p>{{$orderitem->item}} @ {{$orderitem->charge}}</p>
        <?php }?>
        @endforeach
        </p>

        <?php $count = 1; ?>
        @foreach ($data['order_position'] as $position)
        
        <?php if($count <= '6'){?>          
        <div style="float:left; width:16%; margin-left:1px; background:#cccccc;">
        <?php $pos_id = $position->position_id; 
        $placement_id = $position->placement_type;
        $display_label = '';

        if($data['order_misc']->placement_type->$placement_id->id == '43'){
        $display_label = 'Colors:';
        }elseif ($data['order_misc']->placement_type->$placement_id->id == '45') {
        $display_label = 'Stiches:';
        } 

        ?>
        <p style="border-bottom:1px solid #e5e5e5; padding:1px 3px 3px 3px;">
          <strong>Position:</strong><br/>
          {{$data['order_misc']->position->$pos_id->value}}<br/>
        </p>

        <p style="border-bottom:1px solid #e5e5e5; padding:1px 3px 3px 3px;">
          <strong><?php echo $display_label?>&nbsp;</strong>
          {{$position->color_stitch_count}}
        </p>

        <p style="border-bottom:1px solid #e5e5e5; padding:1px 3px 3px 3px;">
          <strong>Discharge Ink:</strong><br/>
          <?php if($position->discharge_qnty > '0'){
          $discharge_price = $position->discharge_qnty * $data['price_grid']->discharge; ?>
           <?php echo $discharge_price;?>
          <?php } else { echo "NA";  }?>
        </p>

        <p style="border-bottom:1px solid #e5e5e5; padding:1px 3px 3px 3px;">
          <strong>Speciality Ink:</strong><br/>
          <?php if($position->speciality_qnty > '0'){
          $speciality_price = $position->speciality_qnty * $data['price_grid']->specialty; ?>
          <?php echo $speciality_price;?>
          <?php } else { echo "NA";  }?>
        </p>

        <p style="border-bottom:1px solid #e5e5e5; padding:1px 3px 3px 3px;">
          <strong>Foil:&nbsp;</strong>
          <?php if($position->foil_qnty > '0'){
          $foil_price = $position->foil_qnty * $data['price_grid']->foil; ?>
          <?php echo $foil_price;?>
          <?php } else { echo "NA";  }?>
        </p>

        <p style="border-bottom:1px solid #e5e5e5; padding:1px 3px 3px 3px;">
          <strong>Ink Charge:&nbsp;</strong>
          <?php if($position->ink_charge_qnty > '0'){
          $ink_price = $position->ink_charge_qnty * $data['price_grid']->ink_changes; ?>
          <?php echo $ink_price;?>
          <?php } else { echo "NA";  }?>
        </p>

        <p style="border-bottom:1px solid #e5e5e5; padding:1px 3px 3px 3px;">
          <strong># on Dark:&nbsp;</strong>
          <?php if($position->number_on_dark_qnty > '0'){
          $dark_price = $position->number_on_dark_qnty * $data['price_grid']->number_on_dark; ?>
          <?php echo $dark_price;?>
          <?php } else { echo "NA";  }?>
        </p>

        <p style="border-bottom:1px solid #e5e5e5; padding:1px 3px 3px 3px;">
          <strong># on Light:&nbsp;</strong>
          <?php if($position->number_on_light_qnty > '0'){
          $light_price = $position->number_on_light_qnty * $data['price_grid']->number_on_light; ?>
          <?php echo $light_price;?>
          <?php } else { echo "NA";  }?>
        </p>

        <p style="border-bottom:1px solid #e5e5e5; padding:1px 3px 3px 3px;">
          <strong>Oversize Screen:</strong><br/>
          <?php if($position->oversize_screens_qnty > '0'){
          $oversize_price = $position->oversize_screens_qnty * $data['price_grid']->over_size_screens; ?>
          <?php echo $oversize_price;?>
          <?php } else { echo "NA";  }?>
        </p>

        <p style="border-bottom:1px solid #e5e5e5; padding:1px 3px 3px 3px;">
          <strong>Press Setup:&nbsp;</strong>
          <?php if($position->press_setup_qnty > '0'){
          $press_setup_price = $position->press_setup_qnty * $data['price_grid']->press_setup; ?>
          <?php echo $press_setup_price;?>
          <?php } else { echo "NA";  }?>
        </p>

        <p style="border-bottom:1px solid #e5e5e5; padding:1px 3px 3px 3px;">
          <strong>Screen Fee:&nbsp;</strong>
          <?php if($position->screen_fees_qnty > '0'){
          $screen_fees_price = $position->screen_fees_qnty * $data['price_grid']->screen_fees; ?>
          <?php echo $screen_fees_price;?>
          <?php } else { echo "NA";  }?>
        </p>

        <p style="border-bottom:1px solid #e5e5e5; padding:1px 3px 3px 3px;">
        <strong>Screen Print:</strong><br/>
        <?php
        if($position->color_stitch_count > '0') {
          if($data['order_misc']->placement_type->$placement_id->id == '43') {
            foreach ($data['price_screen_primary'] as $price_screen_primary) {
            if($data['total_qty'] >= $price_screen_primary->range_low && $data['total_qty'] <= $price_screen_primary->range_high) {
              $price_field = 'pricing_'.$position->color_stitch_count.'c';
              $screen_price_calc =  $price_screen_primary->$price_field;
              echo $screen_price_calc;
              }
            }
          } else { echo "NA"; }
        }      
        ?>
        </p>
        </div>
        <?php }
        $count++; ?>
        @endforeach
      </div>
    
      
    <div class="width100" style="position:relative; clear:both; padding-top:20px; padding-bottom:20px;">
      <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
        <tr>
          <td align="center" valign="top" width="49%">
            <img src="<?php echo url().'/images/c1.jpg';?>" alt="Logo" style="display:block; width:150px; height:150px;" width="150" height="150" />
          </td>
          <td align="left" valign="top" width="2%">&nbsp;</td>
          <td align="center" valign="top" width="49%">
            <img src="<?php echo url().'/images/c2.jpg';?>" alt="Logo" style="display:block; width:150px; height:150px;" width="150" height="150" />
          </td>
        </tr>
      </table>
    </div>

    <div class="width100" style="position:relative; clear:both;">
      <div class="width60" style="float:left">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
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
          <table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
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
      <div class="width60" style="margin-left:0; margin-right:auto;">
        <div style="float:left; width:23%; text-align:center;"><p class="chkboxes">Production QC</p></div>
        <div style="float:left; width:2%;">&nbsp;</div>
        <div style="float:left; width:23%; text-align:center;"><p class="chkboxes">QC</p></div>
        <div style="float:left; width:2%;">&nbsp;</div>
        <div style="float:left; width:23%; text-align:center;"><p class="chkboxes">Pack</p></div>
        <div style="float:left; width:2%;">&nbsp;</div>
        <div style="float:left; width:23%; text-align:center;"><p class="chkboxes">Ship</p></div>
        <div style="float:left; width:2%;">&nbsp;</div>
      </div>
    </div>
    <div class="width100" style="position:relative; clear:both; text-align:center;">
      <div class="width60" style="margin-left:0; margin-right:auto;">
        <div style="float:left; width:23%; text-align:center;"><div class="prntBrdrBox" style="margin-bottom:10px;"></div></div>
        <div style="float:left; width:2%;">&nbsp;</div>
        <div style="float:left; width:23%; text-align:center;"><div class="prntBrdrBox" style="margin-bottom:10px;"></div></div>
        <div style="float:left; width:2%;">&nbsp;</div>
        <div style="float:left; width:23%; text-align:center;"><div class="prntBrdrBox" style="margin-bottom:10px;"></div></div>
        <div style="float:left; width:2%;">&nbsp;</div>
        <div style="float:left; width:23%; text-align:center;"><div class="prntBrdrBox" style="margin-bottom:10px;"></div></div>
        <div style="float:left; width:2%;">&nbsp;</div>
      </div>
    </div>

    <div class="width100" style="position:relative; clear:both; text-align:center;">
      <div class="width60" style="margin-left:0; margin-right:auto;">
        @foreach ($data['order_item'] as $orderitem)
        <?php if($orderitem->selected == '1'){?>
          <div style="float:left; width:23%; text-align:center;">
            <p class="chkboxes">{{$orderitem->item}}</p>
            <div class="prntBrdrBox"></div>
          </div>
          <div style="float:left; width:2%;">&nbsp;</div>
        <?php }?>
        @endforeach

        <?php $count = 1; 
          $position_array = array();
        ?>
        @foreach ($data['order_position'] as $position)
          <?php if($count <= '6'){?>
          <?php $pos_id = $position->position_id; 
          $placement_id = $position->placement_type;
          if(!in_array($data['order_misc']->position->$pos_id->value,$position_array)) {
          array_push($position_array, $data['order_misc']->position->$pos_id->value); 
          ?>
          <div style="float:left; width:23%; text-align:center;">
            <p class="chkboxes">Print {{$data['order_misc']->position->$pos_id->value}}</p>
            <div class="prntBrdrBox" style="margin-bottom:10px;"></div>
          </div>
          <div style="float:left; width:2%;">&nbsp;</div>
          <?php }}
          $count++; ?>
        @endforeach
      </div>
    </div>

    <div class="width100" style="position:relative; clear:both; text-align:center;">
      <p class="txtUpCash">Please respond with "Approved, Name and Date",  for order to be placed into production</p>
      <p class="txtSmall">Unless document states final invoice the amount listed may not be the total due. Shipping, tax and any additions during the art or press stages may result in a change of price.</p>
    </div>
  </div>
</body>
</html>