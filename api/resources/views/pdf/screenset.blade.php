
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
  <table >
    <tr >
      <td>
      <br>
        <p><b>{{$company->companyname}}</b></p>
        <span>{{$company->prime_address1}} {{$company->prime_address_street}}</span>
        <span>{{$company->prime_address_city}}, {{$company->prime_address_state}} {{$company->prime_address_zip}}</span>
        <span>P: {{$company->prime_phone_main}}</span>
        <p><a href="http://www.culturestdio.net">www.culturestdio.net</a></p>
      </td>
      <td style="padding-top:0;"><img src="{{$company->companyphoto}}" title="Culture Studio" alt="Culture Studio"></td>
      <td>
        <p style="text-align: right;">Art Approval Job #{{$company->order_id}}</p>
        <p style="text-align: right;"><b>{{$company->client_company}}</b></p>
        <p style="text-align: right;">Attn:{{$company->first_name}} {{$company->last_name}}</p>
      </td>
    </tr>
  </table>

  <table style="font-weight:400;margin-top:20px;">
    <tr>
      <td>Culture Contact : {{$company->f_name}} {{$company->l_name}}</td>
      <td>Job Name : {{$company->order_name}}</td>
    </tr>
    <tr>
      <td>Brand Coordinator : {{Session::get('name')}}</td>
      <td>Client PO# : {{$company->order_name}}</td>
    </tr>
    <tr>
      <td>In Hands Date: {{$company->in_hands_by}}</td>
    </tr>
  </table>
  <hr style="border:1px solid #000;">
<?php foreach($data as $key=>$value){ 

  ?>
  <table style="margin-top:15px;">
    <tr style="font-weight:100;font-size:15px; margin-bottom:5px;">
      <td><?php echo (!empty($value[0]->screen_set))?$value[0]->screen_set:''; ?></td>
      <td>h:<?php echo $value[0]->screen_height; ?> X w:<?php echo $value[0]->screen_width; ?></td>
    </tr>
    <tr>
      <td>
        <img src="<?php echo $value[0]->mokup_logo; ?>" >
      </td>
      <td>
          <table border="1">
            <tr>
              <td align="center"><b>H</b></td>
              <td align="center"><b>Inq Type</b></td>
              <td align="center"><b>Color</b></td>
              <td align="center"><b>Pantone</b></td>
            </tr>
            <?php foreach($value as $sec_key=>$sec_value){ ?>
            <tr>
              <td align="center"><?php echo $sec_key+1; ?></td>
              <td align="center"><?php echo $sec_value->inq; ?></td>
              <td align="center"><?php echo $sec_value->color_name; ?></td>
              <td align="center"><?php echo $sec_key; ?></td>
            </tr>
            <?php } ?>
          </table>
      </td>
    </tr>
  </table>
  <hr style="border:1px solid #000;">

<?php  if(($key+1)%2==0)
{ ?>
<div style="page-break-before: always;"></div>
  <table >
    <tr >
      <td>
      <br>
        <p><b>{{$company->companyname}}</b></p>
        <span>{{$company->prime_address1}} {{$company->prime_address_street}}</span>
        <span>{{$company->prime_address_city}}, {{$company->prime_address_state}} {{$company->prime_address_zip}}</span>
        <span>P: {{$company->prime_phone_main}}</span>
        <p><a href="http://www.culturestdio.net">www.culturestdio.net</a></p>
      </td>
      <td style="padding-top:0;"><img src="{{$company->companyphoto}}" title="Culture Studio" alt="Culture Studio"></td>
      <td>
        <p style="text-align: right;">Art Approval Job #{{$company->order_id}}</p>
        <p style="text-align: right;"><b>{{$company->client_company}}</b></p>
        <p style="text-align: right;">Attn:{{$company->first_name}} {{$company->last_name}}</p>
      </td>
    </tr>
  </table>
<hr style="border:1px solid #000;">
<?php }}
?>

<div style="page-break-before: always;"></div>
  <table >
    <tr >
      <td>
      <br>
        <p><b>{{$company->companyname}}</b></p>
        <span>{{$company->prime_address1}} {{$company->prime_address_street}}</span>
        <span>{{$company->prime_address_city}}, {{$company->prime_address_state}} {{$company->prime_address_zip}}</span>
        <span>P: {{$company->prime_phone_main}}</span>
        <p><a href="http://www.culturestdio.net">www.culturestdio.net</a></p>
      </td>
      <td style="padding-top:0;"><img src="{{$company->companyphoto}}" title="Culture Studio" alt="Culture Studio"></td>
      <td>
        <p style="text-align: right;">Art Approval Job #{{$company->order_id}}</p>
        <p style="text-align: right;"><b>{{$company->client_company}}</b></p>
        <p style="text-align: right;">Attn:{{$company->first_name}} {{$company->last_name}}</p>
      </td>
    </tr>
  </table>
  <hr style="border:1px solid #000;">
<table style="margin-top:50px;">
<tr><td align="center"><b>Mokup Image</b></td></tr>
  <?php foreach($data as $key=>$value){ ?>
    <tr>
      <td align="center"><img src="<?php echo $value[0]->mokup_image; ?>"  ></td>
    </tr>
   <?php } ?>
</table>
  <table style="margin-top: 20px;">
    <tr>
      <td><b>Culture Contact : {{$company->f_name}} {{$company->l_name}}</b></td>
      <td>Job Name : {{$company->order_name}}</td>
    </tr>
    <tr>
      <td>Brand Coordinator : Anabel Ruiz</td>
      <td>Client PO# : 3954710m2780test1</td>
    </tr>
    <tr>
      <td>In Hands Date: {{$company->in_hands_by}}</td>
    </tr>
  </table>
</body>
</html>