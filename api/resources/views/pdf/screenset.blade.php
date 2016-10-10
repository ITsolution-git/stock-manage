
<html>
<head>
<style>
    @page { margin: 180px 50px; }
    #header { position: fixed; left: 0px; top: -180px; right: 0px; height: 150px; background-color: orange; text-align: center; }
    #footer { position: fixed; left: 0px; bottom: -180px; right: 0px; height: 150px; background-color: lightblue; }
    #footer .page:after { content: counter(page, upper-roman); }
    #header_logo{width: 120px; height:100px;}
    
  </style>
  <title>Order Info</title>
</head>

<body>

    <table class="header">
      <tr>
          <td align="left" width="20%"><img src="{{$company->companyphoto}}" title="Culture Studio" height="100" width="100" alt="Culture Studio"></td>
          <td align="left" width="40%">
             Job# {{$company->order_id}}<br>
             Job Name: {{$company->order_name}}<br>
             Client: {{$company->client_company}}
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
                      {{$company->street}} {{$company->address}}
                  </td>
                  </tr>
                  <tr>
                    <td align="left">
                        {{$company->city}}, {{$company->state_name}} {{$company->postal_code}}
                    </td>
                 </tr>
              </table>
          </td>
      </tr>
    </table><br><br>

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
      <td align="center" border="1">{{$company->custom_po}}</td>
      <td align="center" border="1">{{$company->account_manager}}</td>
      <td align="center" border="1"> </td>
      <td align="center" border="1"></td>
      <td align="center" border="1"> {{$company->date_shipped}}</td>
      <td align="center" border="1"> {{$company->in_hands_by}}</td>
      <td align="center" border="1"> $<?php echo round($company->balance_due, 2); ?></td>
    </tr>
 
  </table><br><br>


<?php foreach($data as $key_main=>$value_main)
{ 
  ?>
  <table style="margin-top:15px;">
    <!-- <tr style="font-weight:100;font-size:15px; margin-bottom:5px;">
      <td><?php //echo (!empty($value_main[0][0]->screen_set))?$value_main[0][0]->screen_set:''; ?></td>
      <td>w:<?php //echo $value_main[0][0]->screen_width; ?> X h:<?php //echo $value_main[0][0]->screen_height; ?></td>
    </tr> -->
    <tr>
      <td width="40%">
        <img src="<?php echo $value_main[0][0]->mokup_logo; ?>" style="height:120px; " >
      </td>
      <td width="60%">
      
          <table border="1">
            <tr>
              <td align="center"><b>Color</b></td>
              <td align="center"><b>Pantone</b></td>
              <td align="center"><b>Ink Type</b></td>
            </tr>
            <?php foreach($value_main[0] as $key=>$value){ ?>
            <tr>
              <td align="center"><?php echo $value->color_name; ?></td>
              <td align="center"><?php echo $value->thread_color; ?></td>
              <td align="center"><?php echo $value->inq; ?></td>
            </tr>
            <?php } ?>
          </table>
          
      </td>
    </tr>
  </table>

  <?php  if(!empty($value_main[1])) {?>
    <table>
          <tr>
              <td align="left"><b>Notes</b></td>
          </tr>
        <?php foreach($value_main[1] as $note_key=>$not_value){ ?>
          <tr>
              <td align="left"><?php echo $not_value ?></td>
          </tr>
        <?php } ?>
    </table>
  <?php } ?>
  

  <!-- <hr style="border:1px solid #000;"> -->

<?php  if(($key_main+1)%3==0 && ($key_main+1)!=count($data))
{ ?>
<div style="page-break-before: always;"></div>
  <table class="header">
      <tr>
          <td align="left" width="20%"><img src="{{$company->companyphoto}}" title="Culture Studio" height="100" width="100" alt="Culture Studio"></td>
          <td align="left" width="40%">
             Job# {{$company->order_id}}<br>
             Job Name: {{$company->order_name}}<br>
             Client: {{$company->client_company}}
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
                      {{$company->street}} {{$company->address}}
                  </td>
                  </tr>
                  <tr>
                    <td align="left">
                        {{$company->city}}, {{$company->state_name}} {{$company->postal_code}}
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

<div style="page-break-before: always;"></div>
         <table class="header">
      <tr>
          <td align="left" width="20%"><img src="{{$company->companyphoto}}" title="Culture Studio" height="100" width="100" alt="Culture Studio"></td>
          <td align="left" width="40%">
             Job# {{$company->order_id}}<br>
             Job Name: {{$company->order_name}}<br>
             Client: {{$company->client_company}}
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
                      {{$company->street}} {{$company->address}}
                  </td>
                  </tr>
                  <tr>
                    <td align="left">
                        {{$company->city}}, {{$company->state_name}} {{$company->postal_code}}
                    </td>
                 </tr>
              </table>
          </td>
      </tr>
    </table><br><br>
  
<table>
<tr><td align="center"><b>Mockup Image</b></td></tr>
  <?php /*foreach($data as $key=>$value){*/ ?>
    <tr>
      <td align="center"><img src="<?php echo $company->mokup_image; ?>" style="width: 600px; height: 615px;"  ></td>
    </tr>
   <?php /*}*/ ?>
</table>
  <table style="margin-top: 20px;">
    <tr>
      <td><b>Culture Contact : {{$company->f_name}} {{$company->l_name}}</b></td>
      <td>Job Name : {{$company->order_name}}</td>
    </tr>
    <tr>
      <td>Brand Coordinator : {{Session::get('name')}}</td>
      <td>Client PO# : {{$company->custom_po}}</td>
    </tr>
    <tr>
      <td>In Hands Date: {{$company->in_hands_by}}</td>
    </tr>
  </table>
</body>
</html>
