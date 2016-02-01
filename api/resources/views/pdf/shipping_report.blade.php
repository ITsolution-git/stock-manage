<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="description" content="">
  <title>Shipping Reports</title>

  <style type="text/css">
    body {
      margin: 0;
      padding: 0;
    }
    div.prntBrdr {
      border:1px solid #000000;
      border-radius: 4px;
      padding-top:15px;
      padding-right:15px;
      padding-left:15px;
      padding-bottom:15px;
      text-align: left;    
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
      font-size: 14px;
      height: 20px;
      line-height: 19px;
      padding: 5px;
    }
    td {
      font-size: 14px;        
    }
    td.boxItem {
      border:1px solid #999999;
      padding: 5px 10px;
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
      background: #e5e5e5;
      border:0 none;
      line-height: 19px;
      height: 20px;
      padding: 5px 0 0 0;
    }
    tbody.totalDetails {
      border-top:1px solid #999999;
      border-bottom:1px solid #999999;
    }
    tbody.totalDetails td {
      padding: 5px 0;
    }
  </style>
</head>
<table border="0" cellpadding="0" cellspacing="0" width="100%">    
    <tr>
      <td align="left" valign="top" width="100%">          
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td align="left" valign="top" width="25%">
             <!--  <img src="../../../../images/logo-stokkup-login.png" alt="Logo" /> -->
            </td>
            <td align="left" valign="top" width="25%">
              <h1>Stokkup</h1>
              <p>Address</p>
              <p>www.url.com</p>
            </td>
            <td align="right" valign="top" width="50%">
              <p><strong>Job # {{$shipping->order_id}}</strong></p>
            </td>
          </tr>
          <tr>
              <td align="left" valign="top">&nbsp;</td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <td align="left" valign="top" width="100%">          
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td align="left" valign="top" width="38%">
              <p>Client</p>
              <div class="prntBrdr">
                <p>{{$shipping->description}}</p>
                <p>{{$shipping->address}} {{$shipping->address2}}</p>
                <p>{{$shipping->city}} {{$shipping->state}} {{$shipping->zipcode}}</p>
                <p>{{$shipping->country}}</p>
              </div>
            </td>
            <td align="left" valign="top" width="2%">&nbsp;</td>
            <td align="left" valign="top" width="40%">    
                <p>&nbsp;</p>          
                <p><strong>PO Number : #</strong></p>
                <p><strong>{{$shipping->job_name}}</strong></p>
                <p>Shipped On : {{$shipping->shipping_by}}</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <td align="left" valign="top" width="100%">&nbsp;</td>
    </tr>

    <tr>
      <td align="left" valign="top" width="100%" style="font-size:18px;">  Garments For Job:{{$shipping->job_name}}</td>
    </tr>

    <tr>
      <td align="left" valign="top" width="100%">&nbsp;</td>
    </tr>

    <tr>
      <td align="left" valign="top" width="100%" class="boxItem">
      SKU: : COLOR: {{$shipping_items[0]->color_name}}: [XS]:{{$other_data['xs_qnty']}}[M]:{{$other_data['m_qnty']}}[L]:{{$other_data['l_qnty']}}[XL]:{{$other_data['xl_qnty']}}[2X]:{{$other_data['2xl_qnty']}}[3X]:{{$other_data['3xl_qnty']}}:TOTAL QNTY:{{$other_data['total_qnty']}}
      </td>
    </tr>

    <tr>
      <td align="left" valign="top" width="100%">&nbsp;</td>
    </tr>
    <tr>
      <td align="left" valign="top" width="100%">&nbsp;</td>
    </tr>
    <tr>
      <td align="left" valign="top" width="100%">&nbsp;</td>
    </tr>

    <tr>
      <td align="left" valign="top" width="100%" class="topbrdr">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td width="10%">&nbsp;</td>
            <td width="45%">
              <p>{{$shipping->description}}</p>
                <p>{{$shipping->address}} {{$shipping->address2}}</p>
                <p>{{$shipping->city}} {{$shipping->state}} {{$shipping->zipcode}}</p>
                <p>{{$shipping->country}}</p>
            </td>
            <td width="45%" style="font-size:24px;">
              Tracking Number
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
        <td align="left" valign="top" width="100%">        
          <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td align="left" valign="top" colspan="5" height="20">&nbsp;</td>
              </tr>
              <thead class="title">
                <tr>
                  <th align="center" valign="top" width="15%">Size</th>
                  <th align="left" valign="top" width="15%">Group</th>
                  <th align="left" valign="top" width="20%">Color</th>
                  <th align="left" valign="top" width="40%">Description</th>
                  <th align="right" valign="top" width="10%">Qnty&nbsp;</th>
                </tr>
              </thead>
              <tbody class="color-grey">
                @foreach ($shipping_boxes as $box)
                <tr>
                  <td align="center" valign="top" class="brdrBox" width="10%">{{$box->size}}</td>
                  <td align="left" valign="top" class="brdrBox" width="10%">{{$box->size_group_name}}</td>
                  <td align="left" valign="top" class="brdrBox" width="10%">{{$box->color_name}}</td>
                  <td align="left" valign="top" class="brdrBox" width="60%">{{$box->product_name}}</td>
                  <td align="right" valign="top" class="brdrBox" width="10%">{{$box->boxed_qnty}}&nbsp;&nbsp;&nbsp;</td>
                </tr>
                @endforeach
              </tbody>
          </table>
        </td>
      </tr>

      <tr>
        <td align="left" valign="top" width="100%">        
          <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <tbody class="totalDetails">
                <tr>
                  <td align="center" valign="top" class="brdrBox" width="4%">&nbsp;</td>
                  <td align="left" valign="top" class="brdrBox" width="50%" style="font-size:16px;">
                    <strong>TOTAL BOXES {{$other_data['total_box']}}</strong>
                  </td>
                  <td align="right" valign="top" class="brdrBox" width="45%" style="font-size:16px;">
                    <strong>Totoal Pieces  {{$other_data['total_pieces']}}&nbsp;&nbsp;</strong>
                  </td>
                </tr> 
              </tbody>
          </table>
        </td>
      </tr>
      <tr>
        <td align="left" valign="top" width="100%">&nbsp;</td>
      </tr>

      <tr>
      <td align="left" valign="top" width="100%" class="topbrdr" style="border-bottom:1px solid #999999;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td width="45%">
              <p>Total Spoilage: {{$other_data['total_spoil']}}</p>
              <p>Total Manufacturer Defect: {{$other_data['total_md']}}</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
</table>
</body>
</html>