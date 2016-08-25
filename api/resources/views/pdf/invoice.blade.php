<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="description" content="">
  <title>Orders Print</title>
<body>
	<table id="header">
		<tr>
			<td><img title="Culture Studio" alt="Culture Studio"></td>
			<td>
				<span class="text-bold">{{$company_data[0]->name}}</span>
				<span>{{$company_data[0]->prime_address1}}</span>
				<span>{{$company_data[0]->city}}, {{$company_data[0]->state}} {{$company_data[0]->zip}}</span>
				<span>P: 312 243-8304  F: 312 243-9015</span>
				<span><a href="#">{{$company_data[0]->url}}</a></span>
			</td>
			<td>
				<span class="order-info">Order Acknoledgement # {{$order_data[0]->id}}</span>
				<span class="order-info">Created on : {{$invoice_data[0]->created_date}}</span>
				<span class="order-info">Job Name: {{$order_data[0]->name}}</span>
			</td>
		</tr>
	</table>

	<table id="billtoShipto" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				<table>
					<tr>
						<td class="no-border" ><img alt="BILL TO" title="BILL TO"></td>
						<td class="no-border" style="vertical-align: top;padding: 10px 0 0 20px;">
							<label style="font-weight: bold;font-size:20px;">{{$client_data[0]->first_name}} {{$client_data[0]->last_name}}</label><br>
							<label style="font-size:20px;">
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
			<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
			<td>
				<table style="border-collapse:collapse;">
					<tr>
						<td class="no-border" style="width:30px;padding:0;"><img alt="SHIP TO" title="SHIP TO"></td>
						<td class="no-border" style="vertical-align: top;padding: 10px 0 0 20px;">
							<label style="font-weight: bold;font-size:20px;">{{$client_data[0]->first_name}} {{$client_data[0]->last_name}}</label><br>
							<label style="font-size:20px;">
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
	</table>

	<?php if(!empty($shipping_detail))
	{ ?>
		<table id="details" cellspacing="0" cellpadding="0">
			<tr>
                <th width="15%">Order ID</th>
                <th width="20%">Estimated Shipping Date</th>
                <th width="15%">Payment Status</th>
                <th width="15%">Shipment Method</th>
                <th width="20%">In Hands By</th>
            </tr>
			@foreach ($shipping_detail as $shipping)
			<tr>
				<td>{{$order_data[0]->id}}</td>
				<td>{{$shipping->shipping_by}}</td>
				<td>Pending</td>
				<td>
					<?php if($shipping->shipping_type_id == 1) {?>
						UPS
					<?php } ?>
					<?php if($shipping->shipping_type_id == 2) {?>
						FEDEX
					<?php } ?>
					<?php if($shipping->shipping_type_id == 0) {?>
						
					<?php } ?>
				</td>
				<td>{{$shipping->in_hands_by}}</td>
			</tr>
			@endforeach
		</table>
	<?php 
	}
	if(!empty($all_design))
	{
	?>
		@foreach($all_design as $design)
		<table id="items-info" cellspacing="0" cellpadding="0">
			<tr>
				<td colspan="5" style="border:0 text-align:center;">
					<div style="background-color: #d3e1e8;">
						<label style="font-weight:900;font-size:25px;"> Design: </label>
						<label style="font-size:20px;">{{$design->design_name}}</label>
					</div>
				</td>
			</tr>
			<tr>
				<td style="border: 0 ;background-color:#fff;">&nbsp;</td>
			</tr>
			<tr>
				<th>Position</th>
				<th>Type</th>
				<th>Qty</th>
				<th>Price</th>
				<th>Image</th>
			</tr>
			@foreach($design->positions['order_design_position'] as $position)
				<tr>
					<td>{{$position->position_name}}</td>
					<td>{{$position->placement_type_name}}</td>
					<td>{{$position->qnty}}</td>
					<td>1.5</td>
					<td style="text-align:center;height:50px;">
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
		</table>

		<table id="items-info" cellspacing="0" cellpadding="0">
			<tr>
				<th>Garment/Item Description</th>
				<th>Color</th>
				<th>Size/Quantities</th>
				<th>Qty</th>
				<th>Unit Price</th>
			</tr>
			@foreach($design->products as $product)
			<tr>
				<td>{{$product->product_name}}</td>
                <td>{{$product->color_name}}</td>
                <td>
                	@foreach ($product->sizeData as $size)
                		{{$size->size}}:({{$size->qnty}})&nbsp;
                	@endforeach
                </td>
                <td>{{$product->total_qnty}}</td>
                <td>{{$product->total_price}}</td>
			</tr>
			@endforeach
		</table>
		@endforeach
	<?php 
	}
	?>

	<table id="finalTable" cellspacing="0" cellpadding="0">
		<tr>
			<td id="finalTableTd1" style="width:20%">
				@foreach($design->positions['order_design_position'] as $position)
					<span><b>Position:</b>{{$position->position_name}}</span><br>
					<?php if($position->discharge_qnty > 0) {?>
						<span>
							Discharge Ink @ ${{$price_grid_data[0]->discharge}}
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
			<td></td>
			<td id="finalTableTd1" style="width:20%">
				@foreach($design->products as $product)
					<span><b>Product:</b>{{$product->product_name}}</span>
					@foreach($product->order_items as $oitem)
						<?php if($oitem->selected > 0) {?>
							<span>
								{{$oitem->item}} ${{$oitem->charge}}
							</span>
						<?php } ?>
					@endforeach
				@endforeach
			</td>
		</tr>
		<tr>
			<td style="width:35%">
				<table cellspacing="0" cellpadding="0">
					<tr>
						<td>Screens</td>
						<td>${{$order_data[0]->screen_charge}}</td>
					</tr>
					<tr>
						<td>Press Setup</td>
						<td>${{$order_data[0]->press_setup_charge}}</td>
					</tr>
					<tr>
						<td>Digitize</td>
						<td>${{$order_data[0]->digitize_charge}}</td>
					</tr>
					<tr>
						<td>Art Work</td>
						<td>${{$order_data[0]->artwork_charge}}</td>
					</tr>
					<tr>
						<td>Separations</td>
						<td>${{$order_data[0]->separations_charge}}</td>
					</tr>
					<tr>
						<td>Rush</td>
						<td>${{$order_data[0]->rush_charge}}</td>
					</tr>
					<tr>
						<td>Distribution</td>
						<td>${{$order_data[0]->distribution_charge}}</td>
					</tr>
					<tr>
						<td>Shipping</td>
						<td>${{$order_data[0]->shipping_charge}}</td>
					</tr>
					<tr>
						<td>Discount</td>
						<td>%{{$order_data[0]->discount}}</td>
					</tr>
					<tr>
						<td>Order Total</td>
						<td>${{$order_data[0]->order_total}}</td>
					</tr>
					<tr>
						<td>Tax Rate</td>
						<td>${{$order_data[0]->tax_rate}}</td>
					</tr>
					<tr>
						<td>Tax</td>
						<td>{{$order_data[0]->tax}}(%)</td>
					</tr>
					<tr>
						<td>Grand Total</td>
						<td>${{$order_data[0]->grand_total}}</td>
					</tr>
					<tr>
						<td>Payments/Deposit</td>
						<td>$0</td>
					</tr>
					<tr>
						<td>Balance Due</td>
						<td>${{$order_data[0]->grand_total}}</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<table id="shipDetails">
		<tr>
			<th>Print</th>
			<th>Belt QC</th>
			<th>Fold</th>
			<th>Pack</th>
			<th>Ship</th>
		</tr>
		<tr>
			<td class="temp">1</td>
			<td class="temp"></td>
			<td class="temp"></td>
			<td class="temp"></td>
			<td class="temp"></td>
		</tr>
	</table>

	<table id="footer">
		<tr>
			<td>
				<p>
					PLEASE RESPOND WITH "APPROVED, NAME AND DATE", FOR ORDER TO BE PLACED INTO PRODUCTION
				</p>
			</td>
		</tr>
		<tr>
			<td>
				<p>
					Unless documents states final invoice the amount listed may not be the total due. Shipping, tax and any additions during the art or press stages may result in a change of price.
				</p>
			</td>
		</tr>
	</table>
</body>
</html>