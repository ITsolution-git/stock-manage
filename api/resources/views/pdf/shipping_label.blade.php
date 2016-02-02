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
    }
    div.boxbrdr {
      border:2px solid #000000;
      color: #000000;
      font-size: 70px;
      margin-top: 10px;
      padding-top:70px;
      padding-right:15px;
      padding-left:15px;
      text-align: center;       
      width: 100%;
      height: 50px;
    }
    h1, h2, h3, h4, h5, h6 {
      margin: 0;
      padding: 0;      
      line-height: 1;
    }
    table, tr, td, th {
      margin: 0;
      padding: 0;
    }
    th {
      font-size: 22px;
      height: 30px;
      line-height: 19px;
      padding: 5px;
    }
    td {
      font-size: 16px;
    }
    td.topbrdr {
      border-top:1px solid #999999;
    }
    p {
      margin: 0;
      padding: 0;
      font-size: 14px;
      line-height: 19px;
    }
    thead.title th {
      padding: 5px 0;
      border-bottom: 1px solid #999999;
    }
    tbody.color-grey td {
      border:0 none;
      line-height: 19px;
      height: 20px;
      padding: 10px 0;
    }
  </style>
</head>
<table border="0" cellpadding="0" cellspacing="0" width="100%">    
    <tr>
        <td align="left" valign="top" width="100%">          
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="left" valign="top" width="25%">
                        <img src="<?php echo url().'/uploads/company/'.$company_detail[0]->company_logo;?>" alt="Logo" style="display:block; max-width:100%;" width="80" />
                    </td>
                    <td align="center" valign="top" width="25%" class="tableCol">
                        <span style="font-size:15px; line-height:15px;">
                            <strong>{{$company_detail[0]->name}}</strong>                
                        </span>
                        <br/>
                        <span>{{$company_detail[0]->address}}, {{$company_detail[0]->city}}, {{$company_detail[0]->state}}, <br/>{{$company_detail[0]->country}} - {{$company_detail[0]->zip}}<br />{{$company_detail[0]->url}}</span>
                    </td>
                    <td align="right" valign="top" width="50%"><span>{{$shipping->description}}</span><br />
                        <span>{{$shipping->address}} {{$shipping->address2}}</span><br />
                        <span>{{$shipping->city}} {{$shipping->state}} {{$shipping->zipcode}}</span><br />
                        <span>{{$shipping->country}}</span><br />
                    </td>
                </tr>
                <tr>
                    <td align="left" valign="top">&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
      <td align="left" valign="top" width="100%">PO : </td>
    </tr>
    
    <tr>
      <?php if($shipping->boxing_type == '0')
      { ?>
        <td align="left" valign="top" width="100%">        
          <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <thead class="title">
                <tr>
                  <th align="left" valign="top" width="49%">Description</th>
                  <th align="left" valign="top" width="2%">&nbsp;</th>
                  <th align="right" valign="top" width="49%">Color</th>
                </tr>
              </thead>
              <tbody class="color-grey">
                @foreach ($shipping_boxes as $box)
                <tr>
                  <td align="left" valign="top" width="49%" height="10">
                    {{$box->product_name}}
                    <div class="boxbrdr">3</div>
                  </td>
                  <td align="left" valign="top" width="2%">&nbsp;</td>
                  <td align="right" valign="top" width="49%" height="10">
                    {{$box->color_name}}
                    <div class="boxbrdr" style="float:right;">{{$box->size}}</div>
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
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
          <thead class="title">
            <tr>
              <th align="left" valign="top" width="10%">Qty.</th>
              <th align="center" valign="top" width="20%">Type</th>
              <th align="left" valign="top" width="20%">Size</th>
              <th align="left" valign="top" width="20%">Color</th>
              <th align="left" valign="top" width="30%">Description</th>
            </tr>
          </thead>
          <tbody class="color-grey">
          @foreach ($shipping_boxes as $box)
            <tr>
              <td align="left" valign="top" class="brdrBox" width="10%">{{$box->qnty}}</td>
              <td align="center" valign="top" class="brdrBox" width="20%">{{$box->size_group_name}}</td>
              <td align="left" valign="top" class="brdrBox" width="20%">{{$box->size_group_name}}</td>
              <td align="left" valign="top" class="brdrBox" width="20%">{{$box->color_name}}</td>
              <td align="left" valign="top" class="brdrBox" width="30%">{{$box->product_name}}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      <?php
      }
      ?>
    </tr>
</table>
</body>
</html>