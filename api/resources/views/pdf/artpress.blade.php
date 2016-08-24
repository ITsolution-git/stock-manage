
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
    <table>
    	<tr>
      		<td align="right"><img src="{{$size[0]->companyphoto}}" title="Culture Studio" height="100" width="100" alt="Culture Studio"></td>
    	</tr>
    </table>
  
  <hr style="border:1px solid #000;">

  <table style="margin-top:15px;">
  	<tr>
      <td colspan="3" align="center">PRESS DETAILS</td>
    </tr>
    <tr >
       <td width="30%" align="center"  style="border: 1px solid #000;">ART</td>
       <td width="70%" align="center" style="border: 1px solid #000;">PRESS SETUP</td>
    </tr>
    <tr style="margin-top: 20px;"> 
    	<td width="30%" style="border: 1px solid #000;"> 
    		<table>
    			<tr><td align="center">Mokup Logo</td></tr>
    			<tr><td align="center"><img src="{{$size[0]->mokup_logo}}" title="Culture Studio" height="150" width="150" alt="Culture Studio"></td></tr>
    			<tr><td align="center">Mokup Image</td></tr>
    			<tr><td align="center"><img src="{{$size[0]->mokup_image}}" title="Culture Studio" height="100" width="100" alt="Culture Studio"></td></tr>
    		</table>
    	</td>
    	<td width="70%" style="border: 1px solid #000;">
          <table>
           <hr style="border:1px solid #000;">
            <tr>
              <td align="center"><b>#POS</b></td>
              <td align="center"><b>COLOR</b></td>
              <td align="center"><b>PANTONE</b></td>
              <td align="center"><b>INK</b></td>
              <td align="center"><b>SQG</b></td>
              <td align="center"><b>STR</b></td>
            </tr>
             <hr style="border:1px solid #000;">
        <?php foreach($color as $key=>$value) {?>    
            <tr>
              <td align="center">{{$key+1}}</td>
              <td align="center">{{$value->color_name}}</td>
              <td align="center">-</td>
              <td align="center">{{$value->inq}}</td>
              <td align="center">{{$value->squeegee}}</td>
              <td align="center">{{$value->stroke}}</td>
            </tr>
        <?php } ?>
          </table>
      </td>
    </tr>
   <tr><td colspan="3"></td></tr><tr><td colspan="3"></td></tr>
    <tr>
    	<td align="center" ><p style="padding-top: 20px;">TOTAL GARMENTS</p></td>
    	<td align="center" ><p style="padding-top: 20px;">_______________</p></td>
    </tr>
  </table>
  <div style="page-break-before: always;"></div>

 <table>
    	<tr>
      		<td align="right"><img src="{{$size[0]->companyphoto}}" title="Culture Studio" height="100" width="100" alt="Culture Studio"></td>
    	</tr>
    </table>
  
  <hr style="border:1px solid #000;">
 <table style="margin-top:15px;">
 	<tr>
 		<td align="center" height="20">GARMENTS</td>	
 	</tr>
 </table>
 <table style="margin-top:15px;">
 	<hr style="border:1px solid #000;">
    <tr>
      <td align="center" width="40%"><b>Product</b></td>
      <td align="center" width="20%"><b>Qnty</b></td>
      <td align="center" width="10%"><b>Size</b></td>
      <td align="center" width="30%"><b>Color</b></td>
    </tr>
    <hr style="border:1px solid #000;">

 <?php foreach($size as $key=>$value) {?>
 	<tr>
 		<td width="40%">{{$value->product_name}}</td>
 		<td align="center" width="20%">{{$value->qnty}}</td>
 		<td align="center" width="10%">{{$value->size}}</td>
 		<td align="center" width="30%">{{$value->product_color}}</td>	
 	</tr>
  <?php } ?>
  </table>


</body>
</html>
