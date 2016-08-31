<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="description" content="">
  <title>Shipping Label</title>

  <style type="text/css">
    body {
      margin: 0;
      padding: 0;
      border: 0 none;
    }
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
    p {
      margin:0;
      padding:0;
      font-size: 10px;
      line-height: 14px;
    }
    th {
      line-height: 19px;
    }
    td {
      line-height: 19px;
    } 
  </style>
</head>
<table border="0" cellpadding="0" cellspacing="0" width="100%">    
    <tr>
        <td align="left" valign="top" width="100%">          
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="left" valign="top" width="25%">
                        <!-- <img src="<?php echo url().'/uploads/company/'.$company_detail[0]->company_logo;?>" alt="Logo" style="display:block; max-width:100%;" width="80" /> -->
                    </td>
                    <td align="center" valign="top" width="25%" class="tableCol">
                        <span style="font-size:15px; line-height:15px;">
                            <strong>{{$company_detail[0]->name}}</strong>                
                        </span><br/>
                        <span>{{$company_detail[0]->address}}, {{$company_detail[0]->city}}, {{$company_detail[0]->state}}, <br/>
                          {{$company_detail[0]->country}} - {{$company_detail[0]->zip}}<br />
                          {{$company_detail[0]->url}}
                        </span>
                    </td>
                    <td align="right" valign="top" width="50%"><p><strong><span>{{$shipping->description}}</span><br />
                        <span>{{$shipping->address}} {{$shipping->address2}}</span><br />
                        <span>{{$shipping->city}} {{$shipping->state}} {{$shipping->zipcode}} {{$shipping->country}}</span></strong></p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <tr>
      <td align="left" valign="top" width="100%">&nbsp;</td>
    </tr>

    <tr>
      <td align="left" valign="top" width="100%">          
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
              <td align="left" valign="top" width="100%">PO : </td>
            </tr>
          </table>
        </td>
    </tr>

    <tr>
      <?php if($shipping->boxing_type == '0')
      { ?>
        <td align="left" valign="top" width="100%">        
          <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <thead class="title">
                <tr>
                  <th align="left" valign="top" width="49%" style="font-size:16px; line-height:32px;"><span><strong>Description</strong></span></th>
                  <th align="left" valign="top" width="2%">&nbsp;</th>
                  <th align="right" valign="top" width="49%" style="font-size:16px; line-height:32px;"><span><strong>Color</strong></span></th>
                </tr>
              </thead>
              <tbody class="color-grey">
                <tr>
                  <td align="left" valign="top" colspan="3" style="border-top:1px solid #000000;">&nbsp;</td>
                </tr>
                @foreach ($shipping_boxes as $box)
                <tr>
                  <td align="left" valign="top" width="49%" height="10"><span>{{$box->product_name}}</span><br/>
                    <div class="boxbrdr">3</div>
                  </td>
                  <td align="left" valign="top" width="2%">&nbsp;</td>
                  <td align="right" valign="top" width="49%" height="10"><span>{{$box->color_name}}</span><br/>
                    <div class="boxbrdr">{{$box->size}}</div>
                  </td>
                </tr>
                @endforeach
              </tbody>
          </table>
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
                <th align="left" valign="top" width="10%" style="border:1px solid #000000;"> Qty.</th>
                <th align="center" valign="top" width="20%" style="border:1px solid #000000;"> Type</th>
                <th align="left" valign="top" width="20%" style="border:1px solid #000000;"> Size</th>
                <th align="left" valign="top" width="20%" style="border:1px solid #000000;"> Color</th>
                <th align="left" valign="top" width="30%" style="border:1px solid #000000;"> Description</th>
              </tr>
            </thead>
            <tbody class="color-grey">
            @foreach ($shipping_boxes as $box)
              <tr>
                <td align="center" valign="top" class="brdrBox" width="20%" style="border:1px solid #000000;"> {{$box->boxed_qnty}}</td>
                <td align="center" valign="top" class="brdrBox" width="20%" style="border:1px solid #000000;"> {{$box->size_group_name}}</td>
                <td align="left" valign="top" class="brdrBox" width="20%" style="border:1px solid #000000;"> {{$box->size_group_name}}</td>
                <td align="left" valign="top" class="brdrBox" width="20%" style="border:1px solid #000000;"> {{$box->color_name}}</td>
                <td align="left" valign="top" class="brdrBox" width="30%" style="border:1px solid #000000;"> {{$box->product_name}}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </td>
      <?php
      }
      ?>
    </tr>
</table>
</body>
</html>