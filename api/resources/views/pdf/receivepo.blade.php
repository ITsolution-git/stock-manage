
<html>
<head>
<style>
    @page { margin: 180px 50px; }
    #header { position: fixed; left: 0px; top: -180px; right: 0px; height: 150px; background-color: orange; text-align: center; }
    #footer { position: fixed; left: 0px; bottom: -180px; right: 0px; height: 150px; background-color: lightblue; }
    #footer .page:after { content: counter(page, upper-roman); }
    #header_logo{width: 120px; height:100px;}
    
  </style>
  <title>Receive Info</title>
</head>

<body>
  <table >
    <tr >
      <td width="30%">
          <table>
              <tr><td><b>{{$company->companyname}}</b></td></tr>
              <tr><td>{{$company->prime_address1}} {{$company->prime_address_street}}
                      {{$company->prime_address_city}}, {{$company->prime_address_state}} {{$company->prime_address_zip}}
                  </td>
              </tr>
              <tr>
                  <td>P: {{$company->prime_phone_main}}</td>
              </tr>
              <tr>
                  <td><a href="http://www.culturestdio.net">www.culturestdio.net</a></td>
              </tr>
          </table>
      </td>
        <td width="40%" align="center"><img id="header_logo" style="height: 100px; width: 100px;" src="{{$company->companyphoto}}" title="Culture Studio" alt="Culture Studio"></td>
      <td width="30%">
          <table>
              <tr><td align="right"><b>Receive PO #{{$company->po_id}}</b></td></tr>
              <tr><td align="right">{{$company->name_company}}</td></tr>
              <tr><td align="right">Attn:{{$company->first_name}} {{$company->last_name}}</td></tr>
          </table>
      </td>
    </tr>
  </table>

  <table style="font-weight:400;margin-top:20px;">
    <tr>
      <td>Company Contact : {{$company->f_name}} {{$company->l_name}}</td>
      <td align="right">Job Name : {{$company->order_name}}</td>
    </tr>
    <tr>
      <td>Brand Coordinator : {{Session::get('name')}}</td>
      <td  align="right">Client PO# : {{$company->custom_po}}</td>
    </tr>
    
  </table>
  <hr style="border:1px solid #000;">
    <table style="margin-top:15px;">
  <tr>
      <th width="26%"><b>Product</b></th>
      <th width="10%"><b>Size</b></th>
      <th width="10%"><b>Color</b></th>
      <th width="10%"><b>Orderd</b></th>
      <th width="12%"><b>Received</b></th>
      <th width="12%"><b>Defective</b></th>
      <th width="10%"><b>Unit Price</b></th>
      <th width="10%"><b>Total</b></th>
    </tr>
<?php foreach($receive_data as $key_main=>$value_main)
{ ?>
<hr style="border:1px solid #000;">
  <?php foreach($value_main['data'] as $key=>$value)
	{ 
  	?>
    <tr>
      <td width="26%"><?php echo (!empty($value->product_name))?$value->product_name:''; ?></td>
      <td width="10%" align="center"><?php echo (!empty($value->size))?$value->size:''; ?></td>
      <td width="10%" align="center"><?php echo (!empty($value->product_color))?$value->product_color:''; ?></td>
      <td width="10%" align="center"><?php echo (!empty($value->qnty_ordered))?$value->qnty_ordered:''; ?></td>
      <td width="12%" align="center"><?php echo (!empty($value->qnty_purchased))?$value->qnty_purchased:''; ?></td>
      <td width="12%" align="center"><?php echo (!empty($value->short))?$value->short:''; ?></td>
      <td width="10%" align="center"><?php echo (!empty($value->unit_price))?$value->unit_price:''; ?></td>
      <td width="10%" align="center"><?php echo (!empty($value->line_total))?$value->line_total:''; ?></td>
    </tr>
    <?php } ?>
	<hr style="border:1px solid #000;">
    <tr>
    	<td >Order Total: <b><?php echo (!empty($value_main['total_product']))?$value_main['total_product']:''; ?></b></td>
    	<td  colspan="3">Order Received: <b><?php echo (!empty($value_main['total_received']))?$value_main['total_received']:''; ?></b></td>
    	<td  colspan="2">Order Defectives: <b><?php echo (!empty($value_main['total_defective']))?$value_main['total_defective']:''; ?></b></td>
    	<td  colspan="2">Order Summary: <b><?php echo (!empty($value_main['total_remains']))?$value_main['total_remains']:''; ?></b></td>
    </tr>

<?php 
}
?>
<hr style="border:1px solid #000;">
<?php if(!empty($company->total_invoice)){ echo '<p style="float:right"> <b>Total </b> : '.$company->total_invoice.'</p>'; } ?>
 </table>
<!--   <hr style="border:1px solid #000;">


  <table style="margin-top: 20px;">
    <tr>
      <td><b>Company Contact : {{$company->f_name}} {{$company->l_name}}</b></td>
      <td>Job Name : {{$company->order_name}}</td>
    </tr>
    <tr>
      <td>Brand Coordinator : {{Session::get('name')}}</td>
      <td>Client PO# : {{$company->order_name}}</td>
    </tr>
    
  </table> -->
</body>
</html>
