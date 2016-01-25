<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="description" content="">
  <title>Orders Print</title>
  <style type="text/css">
   	@media screen, print {
   		h1, h2, h3, h4, h5, h6, div, p, table, tr, td, th, tbody, thead { margin:0; padding:0; }
	 	div { display:inline-block; vertical-align: top; }
		div.width100 { width: 100%; display: block; clear: both; }
		div.width60 { width: 60%; } 
		div.width50 { width: 50%; }  
		div.width45 { width: 45%; }
		div.width40 { width: 40%; }
		div.width35 { width: 35%; }
		div.width30 { width: 30%; }		
		div.width25 { width: 25%; }
		div.width20 { width: 20%; }
		div.width15 { width: 15%; }
		div.width10 { width: 10%; }
		div.width5 { width: 5%; }
		p { font-size: 14px; line-height: 18px; }

		div.brdrbox { 
			border:1px solid #000;
			border-radius: 4px;
			display:inline-block;
			padding-top: 10px;
			padding-right: 15px;
			padding-bottom: 10px;
			padding-left: 15px;
		}
		th { font-size: 13px; line-height: 17px; }
		.brdrItem { border:1px solid #000; padding: 5px; font-size: 13px; line-height: 17px; }
		p.box { height: 40px; width: 40px; margin: 0 auto; }
		p.txtUpCash { font-size: 13px; text-transform: uppercase; }
		p.txtSmall { font-size: 10px; }
   	}
  </style>
</head>
<body style="margin:0; padding:0;">
<div class="width100">
	<div class="width100">			
		<div class="width25" style="float:left; text-align:left;">
			<img src="<?php echo url().'/images/c1.jpg';?>" alt="Logo" style="display:block; width:150px; height:150px;" width="150" height="150" /> 
		</div>
		<div class="width25" style="float:left; text-align:center;">
			<h1 style="font-size:22px; line-height:25px;">CODAL Systems</h1>
			<p>401, Sachet-4,</p>
			<p>Opp. Balaji restaurent,</p>
			<p>Prernatirth Derasar road,</p>
			<p>Ahmedabad, Gujarat,</p>
			<p>India - 380009</p>
		</div>
		<div class="width50" style="float:right; text-align:right;">
			<p style="font-weight:bold;">Estimate # 33674</p>
			<p style="font-weight:bold;">Created on : 01/06/2016</p>
			<p style="font-weight:bold;">Job Name : Testing PDF</p>
		</div>
	</div>
</div>

<div class="width100">
	<div class="width100">			
		<div class="width45 brdrbox" style="float:left; text-align:left;">
			<h2 style="font-size:18px; line-height:23px;"><strong>BILL TO</strong></h2>
			<p>Codal Systems</p>
		</div>
		<div class="width45 brdrbox" style="float:right; text-align:left;">
			<h2 style="font-size:18px; line-height:23px;"><strong>SHIP TO</strong></h2>
			<p>Codal Systems</p>
		</div>
	</div>
</div>

<div class="width100">
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
        <td align="left" valign="top" class="brdrItem" width="20%"></td>
        <td align="left" valign="top" class="brdrItem" width="20%">Dark Room</td>
        <td align="left" valign="top" class="brdrItem" width="10%"></td>
        <td align="left" valign="top" class="brdrItem" width="10%"></td>
        <td align="left" valign="top" class="brdrItem" width="10%"></td>
        <td align="left" valign="top" class="brdrItem" width="20%">01/20/2016</td>
        <td align="left" valign="top" class="brdrItem" width="20%"></td>
      </tr>
    </tbody>
  </table>
</div>

<div class="width100">
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
      <tr>
        <td align="left" valign="top" class="brdrItem" width="35%">Garment Item 1</td>
        <td align="center" valign="top" class="brdrItem" width="10%">White</td>
        <td align="left" valign="top" class="brdrItem" width="30%">XL:2</td>
        <td align="center" valign="top" class="brdrItem" width="5%">4</td>
        <td align="center" valign="top" class="brdrItem" width="10%">100</td>
        <td align="right" valign="top" class="brdrItem" width="10%">10.00</td>
      </tr>
      <tr>
        <td align="left" valign="top" class="brdrItem" width="35%">Garment Item 1</td>
        <td align="center" valign="top" class="brdrItem" width="10%">White</td>
        <td align="left" valign="top" class="brdrItem" width="30%">XL:2</td>
        <td align="center" valign="top" class="brdrItem" width="5%">4</td>
        <td align="center" valign="top" class="brdrItem" width="10%">100</td>
        <td align="right" valign="top" class="brdrItem" width="10%">10.00</td>
      </tr>
      <tr>
        <td align="left" valign="top" class="brdrItem" width="35%">Garment Item 1</td>
        <td align="center" valign="top" class="brdrItem" width="10%">White</td>
        <td align="left" valign="top" class="brdrItem" width="30%">XL:2</td>
        <td align="center" valign="top" class="brdrItem" width="5%">4</td>
        <td align="center" valign="top" class="brdrItem" width="10%">100</td>
        <td align="right" valign="top" class="brdrItem" width="10%">10.00</td>
      </tr>
      <tr>
        <td align="left" valign="top" class="brdrItem" width="35%">Garment Item 1</td>
        <td align="center" valign="top" class="brdrItem" width="10%">White</td>
        <td align="left" valign="top" class="brdrItem" width="30%">XL:2</td>
        <td align="center" valign="top" class="brdrItem" width="5%">4</td>
        <td align="center" valign="top" class="brdrItem" width="10%">100</td>
        <td align="right" valign="top" class="brdrItem" width="10%">10.00</td>
      </tr>
      <tr>
        <td colspan="6">&nbsp;</td>
      </tr>
    </tbody>
  </table>
</div>

<div class="width100">
	<div class="width25" style="float:left; text-align:left;"> 
		<div style="display:block;">   		
			<p>Position: Back</p>
			<p>Discharge Ink:</p>
			<p>Speciality Ink:</p>
			<p>Foil:</p>
			<p>Ink Charge:</p>
			<p># on Dark:</p>
			<p># on Light:</p>
			<p>Oversize Screen:</p>
			<p>Press Setup:</p>
			<p>Screen Fee:</p>
			<p>Screen Print:</p>
			<p>&nbsp;</p>
		</div>

		<div style="display:block;">   		
			<p>Position: Back</p>
			<p>Discharge Ink:</p>
			<p>Speciality Ink:</p>
			<p>Foil:</p>
			<p>Ink Charge:</p>
			<p># on Dark:</p>
			<p># on Light:</p>
			<p>Oversize Screen:</p>
			<p>Press Setup:</p>
			<p>Screen Fee:</p>
			<p>Screen Print:</p>
			<p>&nbsp;</p>
		</div>
	</div>
	<div class="width50" style="float:left; text-align:center;">
		<div class="width50" style="float:left; text-align:center;">
			<img src="<?php echo url().'/images/c1.jpg';?>" alt="Logo" style="display:block; width:150px; height:150px;" width="150" height="150" /> 
		</div>
		<div class="width50" style="float:left; text-align:center;">
			<img src="<?php echo url().'/images/c1.jpg';?>" alt="Logo" style="display:block; width:150px; height:150px;" width="150" height="150" /> 
		</div>
	</div>
	<div class="width25" style="float:right; text-align:right;">
		<p>
			<span style="display:inline-block; float:left; width:40%; margin-right:2px; padding-top:5px;">Total Qnty</span>
			<span class="brdrItem" style="display:inline-block; float:right; width:50%;">308</span>
		</p>

		<p>
			<span style="display:inline-block; float:left; width:40%; margin-right:2px; padding-top:5px;">Total Qnty</span>
			<span class="brdrItem" style="display:inline-block; float:right; width:50%; border-top:0 none;">308</span>
		</p>
		<p>
			<span style="display:inline-block; float:left; width:40%; margin-right:2px; padding-top:5px;">Total Qnty</span>
			<span class="brdrItem" style="display:inline-block; float:right; width:50%; border-top:0 none;">308</span>
		</p>
		<p>
			<span style="display:inline-block; float:left; width:40%; margin-right:2px; padding-top:5px;">Total Qnty</span>
			<span class="brdrItem" style="display:inline-block; float:right; width:50%; border-top:0 none;">308</span>
		</p>
		<p>
			<span style="display:inline-block; float:left; width:40%; margin-right:2px; padding-top:5px;">Total Qnty</span>
			<span class="brdrItem" style="display:inline-block; float:right; width:50%; border-top:0 none;">308</span>
		</p>
		<p>
			<span style="display:inline-block; float:left; width:40%; margin-right:2px; padding-top:5px;">Total Qnty</span>
			<span class="brdrItem" style="display:inline-block; float:right; width:50%; border-top:0 none;">308</span>
		</p>
		<p>
			<span style="display:inline-block; float:left; width:40%; margin-right:2px; padding-top:5px;">Total Qnty</span>
			<span class="brdrItem" style="display:inline-block; float:right; width:50%; border-top:0 none;">308</span>
		</p>
		<p>
			<span style="display:inline-block; float:left; width:40%; margin-right:2px; padding-top:5px;">Total Qnty</span>
			<span class="brdrItem" style="display:inline-block; float:right; width:50%; border-top:0 none;">308</span>
		</p>
		<p>
			<span style="display:inline-block; float:left; width:40%; margin-right:2px; padding-top:5px;">Total Qnty</span>
			<span class="brdrItem" style="display:inline-block; float:right; width:50%; border-top:0 none;">308</span>
		</p>
		<p>
			<span style="display:inline-block; float:left; width:40%; margin-right:2px; padding-top:5px;">Total Qnty</span>
			<span class="brdrItem" style="display:inline-block; float:right; width:50%; border-top:0 none;">308</span>
		</p>
		<p>
			<span style="display:inline-block; float:left; width:40%; margin-right:2px; padding-top:5px;">Total Qnty</span>
			<span class="brdrItem" style="display:inline-block; float:right; width:50%; border-top:0 none;">308</span>
		</p>
		<p>
			<span style="display:inline-block; float:left; width:40%; margin-right:2px; padding-top:5px;">Total Qnty</span>
			<span class="brdrItem" style="display:inline-block; float:right; width:50%; border-top:0 none;">308</span>
		</p>
		<p>
			<span style="display:inline-block; float:left; width:40%; margin-right:2px; padding-top:5px;">Total Qnty</span>
			<span class="brdrItem" style="display:inline-block; float:right; width:50%; border-top:0 none;">308</span>
		</p>
		<p>
			<span style="display:inline-block; float:left; width:40%; margin-right:2px; padding-top:5px;">Total Qnty</span>
			<span class="brdrItem" style="display:inline-block; float:right; width:50%; border-top:0 none;">308</span>
		</p>
		<p>
			<span style="display:inline-block; float:left; width:40%; margin-right:2px; padding-top:5px;">Total Qnty</span>
			<span class="brdrItem" style="display:inline-block; float:right; width:50%; border-top:0 none;">308</span>
		</p>
		<p>
			<span style="display:inline-block; float:left; width:40%; margin-right:2px; padding-top:5px;">Total Qnty</span>
			<span class="brdrItem" style="display:inline-block; float:right; width:50%; border-top:0 none;">308</span>
		</p>    		
	</div>	
</div>

<div class="width100" style="clear:both; margin-top:20px;">
	<div class="width50" style="float:left; text-align:left;">
		Garment LinkL: 
		<p>www.codal.com</p>
	</div>
	<div class="width50" style="float:left; text-align:left;">
		<p><strong>NOTES</strong></p>
		<p>
			<textarea class="brdrItem">This is a Invoice Note.</textarea>
		</p>
	</div>
</div>

<div class="width60" style="margin-left:auto; margin-right:auto;">
	<div class="width100">
		<div style="width:24%; text-align:center;">
			<p>Production QC</p>
			<p>&nbsp;</p>
			<p class="brdrItem box" style="display:block;"></p>
		</div>		
		<div style="width:24%; margin-left:1%; text-align:center;">
			<p>QC</p>
			<p>&nbsp;</p>
			<p class="brdrItem box" style="display:block;"></p>
		</div>		
		<div style="width:24%; margin-left:1%; text-align:center;">
			<p>Pack</p>
			<p>&nbsp;</p>
			<p class="brdrItem box" style="display:block;"></p>
		</div>		
		<div style="width:24%; margin-left:1%; text-align:center;">
			<p>Ship</p>
			<p>&nbsp;</p>
			<p class="brdrItem box" style="display:block;"></p>
		</div>	
	</div>	

	<div class="width100">
		<div style="width:24%; text-align:center;">
			<p>Production QC</p>
			<p>&nbsp;</p>
			<p class="brdrItem box" style="display:block;"></p>
		</div>		
		<div style="width:24%; margin-left:1%; text-align:center;">
			<p>QC</p>
			<p>&nbsp;</p>
			<p class="brdrItem box" style="display:block;"></p>
		</div>		
		<div style="width:24%; margin-left:1%; text-align:center;">
			<p>Pack</p>
			<p>&nbsp;</p>
			<p class="brdrItem box" style="display:block;"></p>
		</div>		
		<div style="width:24%; margin-left:1%; text-align:center;">
			<p>Ship</p>
			<p>&nbsp;</p>
			<p class="brdrItem box" style="display:block;"></p>
		</div>	
	</div>	
</div>

<div class="width100" style="position:relative; clear:both; text-align:center;">
  <p class="txtUpCash">Please respond with "Approved, Name and Date",  for order to be placed into production</p>
  <p class="txtSmall">Unless document states final invoice the amount listed may not be the total due. Shipping, tax and any additions during the art or press stages may result in a change of price.</p>
</div>

</body>
</html>