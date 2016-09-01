<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="description" content="">
  <title>Orders Print</title>

</head>
<body>
	<table id="header" style="width:100%">
		<tr>
			<td><img src="{{$company_data[0]->photo}}" title="Culture Studio" alt="Culture Studio" height="100px" width="100px"></td>
			<td>
				<span style="text-align:center;margin:0;font-weight:bold;">{{$company_data[0]->name}}</span>
				<span style="text-align:center;margin:0;">{{$company_data[0]->prime_address1}}</span>
				<span style="text-align:center;margin:0;">{{$company_data[0]->city}}, {{$company_data[0]->state}} {{$company_data[0]->zip}}</span>
				<span style="text-align:center;margin:0;">P: {{$company_data[0]->phone}}</span><br>
				<span style="text-align:center;margin:0;"><a href="{{$company_data[0]->url}}" style="text-decoration:none;color:#000;">{{$company_data[0]->url}}</a></span>
			</td>
			<td>
				<span style="font-weight:bold;text-align:right;">Order Acknowledgement # {{$order_data[0]->id}}</span><br>
				<span style="font-weight:bold;text-align:right;">Created on : {{$invoice_data[0]->created_date}}</span><br>
				<span style="font-weight:bold;text-align:right;">Due Date : {{$invoice_data[0]->payment_due_date}}</span><br>
				<span style="font-weight:bold;text-align:right;">Job Name: {{$order_data[0]->name}}</span>
			</td>
		</tr>
	</table><br><br>

	<table id="billtoShipto" cellspacing="0" cellpadding="0" style="width:100%;">
	  <tr>
	   <td style="height:100px;border:1px solid #000;border-radius:15px;width:50%;">
	    <table style="width:100%">
	     <tr>
	      <td style="width:30px;padding:0;border:none;"><img src="" alt="BILL TO" title="BILL TO"></td>
	      <td style="vertical-align: top;padding: 10px 0 0 20px;border:none;">
	       <label style="font-weight: bold;">{{$client_data[0]->first_name}} {{$client_data[0]->last_name}}</label><br>
	       <label style="">
	       @foreach ($addresses['result'] as $address)  
	        <?php if($address->address_billing == '1') {?>
	         {{$address->address}}<br>{{$address->street}}<br>{{$address->city}}<br>{{$address->state_name}}<br>{{$address->postal_code}}
	        <?php } ?>
	       @endforeach
	       </label>
	      </td>
	     </tr>
	    </table>
	   </td>
	   
	   <td style="height:100px;border:1px solid #000;border-radius:15px;width:50%;">
	    <table style="border-collapse:collapse;width:100%">
	     <tr>
	      <td style="width:30px;padding:0;border:none;"><img src="" alt="SHIP TO" title="SHIP TO"></td>
	      <td style="vertical-align: top;padding: 10px 0 0 20px;border:none;">
	       <label style="font-weight: bold">{{$client_data[0]->first_name}} {{$client_data[0]->last_name}}</label><br>
	       <label style="">
	       @foreach ($addresses['result'] as $address)  
	        <?php if($address->address_shipping == '1') {?>
	         {{$address->address}}<br>{{$address->street}}<br>{{$address->city}}<br>{{$address->state_name}}<br>{{$address->postal_code}}
	        <?php } ?>
	       @endforeach
	       </label>
	      </td>
	     </tr>
	    </table>
	   </td>
	  </tr>
 </table><br><br>

	<?php if(!empty($shipping_detail))
	{ ?>
		<table id="details" cellspacing="0" cellpadding="0" style="width:100%">
			<tr>
                <th width="15%" style="border:none;font-weight:bold;text-align:center;">Order<br>ID</th>
                <th width="25%" style="border:none;font-weight:bold;text-align:center;">Estimated Shipping Date</th>
                <th width="20%" style="border:none;font-weight:bold;text-align:center;">Payment<br>Status</th>
                <th width="20%" style="border:none;font-weight:bold;text-align:center;">Shipment<br>Method</th>
                <th width="20%" style="border:none;font-weight:bold;text-align:center;">In Hands<br>By</th>
            </tr>
			@foreach ($shipping_detail as $shipping)
			<tr>
				<td style="border:1px solid #000;padding-left:5px;"> {{$order_data[0]->id}}</td>
				<td style="border:1px solid #000;padding-left:5px;"> {{$shipping->shipping_by}}</td>
				<td style="border:1px solid #000;padding-left:5px;"> Pending</td>
				<td style="border:1px solid #000;padding-left:5px;">
					<?php if($shipping->shipping_type_id == 1) {?>
						 UPS
					<?php } ?>
					<?php if($shipping->shipping_type_id == 2) {?>
						 FEDEX
					<?php } ?>
					<?php if($shipping->shipping_type_id == 0) {?>
						
					<?php } ?>
				</td>
				<td style="border:1px solid #000;padding-left:5px;"> {{$shipping->in_hands_by}}</td>
			</tr>
			@endforeach
		</table><br><br>
	<?php 
	}
	if(!empty($all_design))
	{
	?>
		@foreach($all_design as $design)
		<table id="items-info" cellspacing="0" cellpadding="0" style="width:100%">
			<tr>
				<td colspan="5" style="border:none;text-align:center;">
					<div style="background-color: #d3e1e8;">
						<label style="font-weight:bold;font-size:25px;"> Design: </label>
						<label style="font-size:20px;">{{$design->design_name}}</label>
					</div>
				</td>
			</tr>
			<tr>
				<td style="border:none;background-color:#fff;">&nbsp;</td>
			</tr>
			<tr>
				<th style="border:none;text-align:center;font-weight:bold;">Position</th>
				<th style="border:none;text-align:center;font-weight:bold;">Type</th>
				<th style="border:none;text-align:center;font-weight:bold;">Qty</th>
				<th style="border:none;text-align:center;font-weight:bold;">Price</th>
				<th style="border:none;text-align:center;font-weight:bold;">Image</th>
			</tr>
			@foreach($design->positions['order_design_position'] as $position)
				<tr>
					<td style="border:1px solid #000"> {{$position->position_name}}</td>
					<td style="border:1px solid #000"> {{$position->placement_type_name}}</td>
					<td style="border:1px solid #000"> {{$position->qnty}}</td>
					<td style="border:1px solid #000"> <?php echo number_format($position->total_price,2) ?></td>
					<td style="text-align:center;height:50px;border:1px solid #000;">
					<?php if($position->position_image != '') {?>
						<img src="{{$position->position_image}}" height="40px" width="40px"></td>
					<?php }
					else
					{
					?>
						<img src="" height="40px" width="40px"></td>
					<?php
					}
					?>
				</tr>
			@endforeach
		</table><br><br>

		<table id="items-info" cellspacing="0" cellpadding="0" style="width:100%">
			<tr>
				<th style="border:none;text-align:center;font-weight:bold;">Garment/Item Description</th>
				<th style="border:none;text-align:center;font-weight:bold;">Color</th>
				<th style="border:none;text-align:center;font-weight:bold;">Size/<br>Quantities</th>
				<th style="border:none;text-align:center;font-weight:bold;">Qty</th>
				<th style="border:none;text-align:center;font-weight:bold;">Unit<br>Price</th>
			</tr>
			@foreach($design->products as $product)
			<tr>
				<td style="border:1px solid #000"> {{$product->product_name}}</td>
                <td style="border:1px solid #000"> {{$product->color_name}}</td>
                <td style="border:1px solid #000">
                	@foreach ($product->sizeData as $size)
                		 {{$size->size}}:({{$size->qnty}})&nbsp;
                	@endforeach
                </td>
                <td style="border:1px solid #000"> {{$product->total_qnty}}</td>
                <td style="border:1px solid #000"> $<?php echo number_format($product->total_price,2) ?></td>
			</tr>
			@endforeach
		</table><br><br>
		@endforeach
	<?php 
	}
	?>

	<table id="finalTable" cellspacing="0" cellpadding="0" style="width:100%">
		<tr>
			<td style="width:50%">
				@foreach($design->positions['order_design_position'] as $position)
					<br><span><b>Position: </b>{{$position->position_name}}</span><br>
					<?php if($position->discharge_qnty > 0) {?>
						<span>
							Discharge Ink @ $<?php echo number_format($price_grid_data[0]->discharge,2); ?>
						</span><br>
					<?php } ?>
					<?php if($position->foil_qnty > 0) {?>
						<span>
							Foil @ ${{$price_grid_data[0]->foil}}
						</span><br>
					<?php } ?>
					<?php if($position->ink_charge_qnty > 0) {?>
						<span>
							Ink Charge @ ${{$price_grid_data[0]->ink_changes}}
						</span><br>
					<?php } ?>
					<?php if($position->number_on_dark_qnty > 0) {?>
						<span>
							# on Dark @ ${{$price_grid_data[0]->number_on_dark}}
						</span><br>
					<?php } ?>
					<?php if($position->number_on_light_qnty > 0) {?>
						<span>
							# on Light @ ${{$price_grid_data[0]->number_on_light}}
						</span><br>
					<?php } ?>
				@endforeach
			</td>
			<td style="width:50%">
				@foreach($design->products as $product)
					<br><span><b>Product: </b>{{$product->product_name}}</span><br>
					@foreach($product->order_items as $oitem)
						<?php if($oitem->selected > 0) {?>
							<span>
								{{$oitem->item}} ${{$oitem->charge}}
							</span><br>
						<?php } ?>
					@endforeach
				@endforeach
			</td>
		</tr>
		<tr>
			<td style="width:50%">&nbsp;</td>
			<td style="width:50%">
				<table cellspacing="0" cellpadding="0">
					<tr>
						<td style="padding-right:5px;">Screens</td>
						<td style="border:1px solid #000;text-align:center;">$<?php echo number_format($order_data[0]->screen_charge,2); ?></td>
					</tr>
					<tr>
						<td style="padding-right:5px;">Press Setup</td>
						<td style="border:1px solid #000;text-align:center;">$<?php echo number_format($order_data[0]->press_setup_charge,2); ?></td>
					</tr>
					<tr>
						<td style="padding-right:5px;">Digitize</td>
						<td style="border:1px solid #000;text-align:center;">$<?php echo number_format($order_data[0]->digitize_charge,2); ?></td>
					</tr>
					<tr>
						<td style="padding-right:5px;">Art Work</td>
						<td style="border:1px solid #000;text-align:center;">$<?php echo number_format($order_data[0]->artwork_charge,2); ?></td>
					</tr>
					<tr>
						<td style="padding-right:5px;">Separations</td>
						<td style="border:1px solid #000;text-align:center;">$<?php echo number_format($order_data[0]->separations_charge,2); ?></td>
					</tr>
					<tr>
						<td style="padding-right:5px;">Rush</td>
						<td style="border:1px solid #000;text-align:center;">$<?php echo number_format($order_data[0]->rush_charge,2); ?></td>
					</tr>
					<tr>
						<td style="padding-right:5px;">Distribution</td>
						<td style="border:1px solid #000;text-align:center;">$<?php echo number_format($order_data[0]->distribution_charge,2); ?></td>
					</tr>
					<tr>
						<td style="padding-right:5px;">Shipping</td>
						<td style="border:1px solid #000;text-align:center;">$<?php echo number_format($order_data[0]->shipping_charge,2); ?></td>
					</tr>
					<tr>
						<td style="padding-right:5px;">Discount</td>
						<td style="border:1px solid #000;text-align:center;">$<?php echo number_format($order_data[0]->discount,2); ?></td>
					</tr>
					<tr>
						<td style="padding-right:5px;">Order Total</td>
						<td style="border:1px solid #000;text-align:center;">$<?php echo number_format($order_data[0]->order_total,2); ?></td>
					</tr>
					<tr>
						<td style="padding-right:5px;">Tax Rate</td>
						<td style="border:1px solid #000;text-align:center;"><?php echo number_format($order_data[0]->tax_rate,2); ?>%</td>
					</tr>
					<tr>
						<td style="padding-right:5px;">Tax</td>
						<td style="border:1px solid #000;text-align:center;"><?php echo number_format($order_data[0]->tax,2); ?></td>
					</tr>
					<tr>
						<td style="padding-right:5px;">Grand Total</td>
						<td style="border:1px solid #000;text-align:center;">$<?php echo number_format($order_data[0]->grand_total,2); ?></td>
					</tr>
					<tr>
						<td style="padding-right:5px;">Payments/Deposit</td>
						<td style="border:1px solid #000;text-align:center;">$0.00</td>
					</tr>
					<tr>
						<td style="padding-right:5px;">Balance Due</td>
						<td style="border:1px solid #000;text-align:center;">$<?php echo number_format($order_data[0]->grand_total,2); ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table><br><br>

	<table id="shipDetails" style="width:100%;border-collapse:collapse;">
		<tr style="border:none;">
			<th style="width:20%;text-align:center;font-weight:bold;">Print</th>
			<th style="width:20%;text-align:center;font-weight:bold;">Belt QC</th>
			<th style="width:20%;text-align:center;font-weight:bold;">Fold</th>
			<th style="width:20%;text-align:center;font-weight:bold;">Pack</th>
			<th style="width:20%;text-align:center;font-weight:bold;">Ship</th>
		</tr>
		<tr style="text-align:center;">
			<td style="border:1px solid #000;">1</td>
			<td style="border:1px solid #000;"></td>
			<td style="border:1px solid #000;"></td>
			<td style="border:1px solid #000;"></td>
			<td style="border:1px solid #000;"></td>
		</tr>
	</table><br><br>

	<table id="footer" style="width:100%">
		<tr>
			<td>
				<p style="text-align:center;">
					PLEASE RESPOND WITH "APPROVED, NAME AND DATE", FOR ORDER TO BE PLACED INTO PRODUCTION
				</p>
			</td>
		</tr>
		<tr>
			<td>
				<p style="text-align:center;">
					Unless documents states final invoice the amount listed may not be the total due. Shipping, tax and any additions during the art or press stages may result in a change of price.
				</p>
			</td>
		</tr>
	</table>
</body>
</html>