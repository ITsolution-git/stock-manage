<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="description" content="">
  <title>Shipping Label</title>
  <style type="text/css">
    
    div.boxbrdr {
      border:1px solid #000000;
      color: #000000;
      font-size: 50px;
      margin-top: 10px;
      padding-top:70px;
      padding-right:15px;
      padding-left:15px;
      text-align: center;       
      width: 50px !important;
      height: 50px !important;
    }
    
  </style>

</head>
<body style="padding:0; margin:0">

<table width="500" align="center" border="0" cellspacing="0" cellpadding="0" style="font-size:10px;">
<tr>
<td style="width:100%">
  <table width="100%" align="center" border="0" cellspacing="0" cellpadding="0" style="font-family: arial; font-size:11px;">
      
      <tr>
      <?php if($shipping->boxing_type == 'Retail')
      {
        $count=1;
        $totalCount=count($shipping_boxes);
        ?>
        <td align="left" valign="top" width="100%">
          @foreach ($shipping_boxes as $box)

            <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <tr height="10"><td style="width:100%">&nbsp;</td></tr>
            </table>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <thead class="title">
                <tr>
                  <th align="left" valign="top" width="25%"><img src="{{$company_detail[0]->photo}}" title="" alt="" height="100px" width="100px"></th>
                  <th align="left" valign="top" width="25%">&nbsp;</th>
                  <th align="right" valign="top" width="50%" style="font-size:11px; line-height:16px;">
                      <span><strong>{{$shipping->description}}</strong></span><br />
                      <span>{{$shipping->address}} {{$shipping->address2}}</span><br/>
                      <span>{{$shipping->city}} {{$shipping->state}}</span><br />
                      <span>{{$shipping->zipcode}} {{$shipping->country}}</span>
                  </th>
                </tr>
              </thead>
            </table>
            <?php if($shipping->custom_po != ''){?> 
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td align="left" valign="top">PO: {{$shipping->custom_po}}</td>
            </tr>
            </table>
            <?php
            }
            ?>
          <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <thead class="title">
                <tr>
                  <th align="left" valign="top" width="49%" style="font-size:16px; line-height:32px;"><span><strong>Garment / Item Description</strong></span></th>
                  <th align="left" valign="top" width="2%">&nbsp;</th>
                  <th align="right" valign="top" width="49%" style="font-size:16px; line-height:32px;"><span><strong>Color</strong></span></th>
                </tr>
              </thead>
              <tbody class="color-grey">
                <tr>
                  <td align="left" valign="top" colspan="3">&nbsp;</td>
                </tr>
                <tr>
                  <td align="left" valign="middle" colspan="3" style="border-top:1px solid #000000;">&nbsp;</td>
                </tr>
                
                <tr style="height:150px;">
                  <td align="left" valign="top" width="70%" height="10"><span>{{$box->product_name}}</span>
                  </td>
                  <td align="left" valign="top" width="2%">&nbsp;</td>
                  <td align="right" valign="top" width="18%" height="10"><span>{{$box->color_name}}</span>
                  </td>
                </tr>
                <tr style="height:150px;">
                  <td align="left" valign="top" width="49%" height="10">
                    <div class="boxbrdr">{{$box->box_qnty}}</div>
                  </td>
                  <td align="left" valign="top" width="2%">&nbsp;</td>
                  <td align="right" valign="top" width="49%" height="10">
                    <div class="boxbrdr">{{$box->size}}</div>
                  </td>
                </tr>
                <tr>
                  <td align="left" valign="top" colspan="3">&nbsp;</td>
                </tr>
                <tr>
                  <td align="left" valign="middle" colspan="3"><span><strong>BOX {{$count}} of {{$totalCount}}</strong></span></td>
                </tr>
              </tbody>
          </table>
          <?php
          if($count<$totalCount){
          ?>
          <div style="page-break-before: always;"></div>
          <?php
          }
          $count++; ?>
          @endforeach
        </td>
      <?php
      }
      else
      {
      ?>
        <td align="left" valign="top" width="100%">
          <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <thead class="title">
                <tr>
                  <th align="left" valign="top" width="25%"><img src="{{$company_detail[0]->photo}}" title="" alt="" height="100px" width="100px"></th>
                  <th align="left" valign="top" width="25%">&nbsp;</th>
                  <th align="right" valign="top" width="50%" style="font-size:11px; line-height:16px;">
                      <span><strong>{{$shipping->description}}</strong></span><br />
                      <span>{{$shipping->address}} {{$shipping->address2}}</span><br/>
                      <span>{{$shipping->city}} {{$shipping->state}}</span><br />
                      <span>{{$shipping->zipcode}} {{$shipping->country}}</span>
                  </th>
                </tr>
              </thead>
            </table>
            <?php if($shipping->custom_po != ''){?> 
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td align="left" valign="top">PO: {{$shipping->custom_po}}</td>
            </tr>
            </table>
            <?php
            }
            ?>
          <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <thead class="title">
              <tr>
                <th align="left" valign="top" width="50%" style="border-bottom:1px solid #000000;"><strong>Garment / Item Description</strong></th>
                <th align="left" valign="top" width="20%" style="border-bottom:1px solid #000000;"><strong>Color</strong></th>
                <th align="left" valign="top" width="20%" style="border-bottom:1px solid #000000;"><strong>Size</strong></th>
                <th align="center" valign="top" width="10%" style="border-bottom:1px solid #000000;"><strong>Qty.</strong></th>
              </tr>
            </thead>
            <tbody class="color-grey">
              <?php
              $count=1;
              $page=1;
              if(count($shipping_boxes)%9==0){
                  $totalCount=count($shipping_boxes)%9;
                }else{
                  $totalCount=(count($shipping_boxes)%9) + 1;
                }
              
              ?>
            @foreach ($shipping_boxes as $box)
              <tr>
                <td align="left" valign="top" class="brdrBox" width="50%" style="border-bottom:1px solid #000000;">{{$box->product_name}}</td>
                <td align="left" valign="top" class="brdrBox" width="20%" style="border-bottom:1px solid #000000;">{{$box->color_name}}</td>
                <td align="left" valign="top" class="brdrBox" width="20%" style="border-bottom:1px solid #000000;">{{$box->size}}</td>
                <td align="center" valign="top" class="brdrBox" width="10%" style="border-bottom:1px solid #000000;">{{$box->boxed_qnty}}</td>
              </tr>
              <?php
              $count++;
              if($count%9 == 0){
              ?>
              
              <?php
              $page++;
              ?>
          </table>
              <div style="page-break-before: always;"></div>
              <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td align="left" valign="top" width="100%">
                  <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top:none;">
                  <thead class="title">
                    <tr>
                      <th align="left" valign="top" width="25%"><img src="{{$company_detail[0]->photo}}" title="" alt="" height="100px" width="100px"></th>
                      <th align="left" valign="top" width="25%">&nbsp;</th>
                      <th align="right" valign="top" width="50%" style="font-size:11px; line-height:16px;" height="100px">
                          <span><strong>{{$shipping->description}}</strong></span><br />
                          <span>{{$shipping->address}} {{$shipping->address2}}</span><br/>
                          <span>{{$shipping->city}} {{$shipping->state}}</span><br />
                          <span>{{$shipping->zipcode}} {{$shipping->country}}</span>
                      </th>
                    </tr>
                  </thead>
                </table>

                </td>
              </tr>
                
                
                <thead class="title">
                  <?php if($shipping->custom_po != ''){?> 
                   <tr>
                      <td align="left" valign="top" width="100%" colspan="4">PO: {{$shipping->custom_po}}</td>
                    </tr>
                  <?php
                  }
                  ?>
                  <tr>
                    <th align="left" valign="top" width="50%" style="border-bottom:1px solid #000000;"><strong>Garment / Item Description</strong></th>
                    <th align="left" valign="top" width="20%" style="border-bottom:1px solid #000000;"><strong>Color</strong></th>
                    <th align="left" valign="top" width="20%" style="border-bottom:1px solid #000000;"><strong>Size</strong></th>
                    <th align="center" valign="top" width="10%" style="border-bottom:1px solid #000000;"><strong>Qty.</strong></th>
                  </tr>
                </thead>
                
              <tbody class="color-grey">
              <?php
              }
              ?>
            @endforeach
            </tbody>
          </table>
        </td>
      <?php
      }
      ?>
    </tr>
  </table>
</td>
</tr>
</table>
</body>
</html>