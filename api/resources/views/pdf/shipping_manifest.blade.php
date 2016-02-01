<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="description" content="">
  <title>Shipping Manifest</title>

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
    p {
      margin: 0;
      padding: 0;
      font-size: 14px;
      line-height: 19px;
    }
    thead.title th {
      padding: 5px 0;
    }
    thead.title th:first-child {
      padding-left: 10px;
    }
    tbody.color-grey td {
      background: #e5e5e5;
      border:0 none;
      border-bottom: 1px solid #999999;      
      line-height: 19px;
      height: 20px;
      padding: 5px 0 0 0;
    }
    tbody.color-grey tr td:first-child {
      padding-left: 10px;
    }
  </style>
</head>
<table border="0" cellpadding="0" cellspacing="0" width="100%">    
    <tr>
      <td align="left" valign="top" width="100%">          
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td align="left" valign="top" width="25%">
              <!-- <img src="../../../../images/logo-stokkup-login.png" alt="Logo" /> -->
            </td>
            <td align="left" valign="top" width="25%"><span style="font-size:15px; line-height:15px;"><strong>Stokkup</strong></span>
              <br/>
            <span>101,ahmedabad,Gujarat<br/>India - 380007<br />www.google.com</span>
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
              <span>SHIP TO</span><br />
              <div class="prntBrdr">
                <span>{{$shipping->description}}</span><br />
                <span>{{$shipping->address}} {{$shipping->address2}}</span><br />
                <span>{{$shipping->city}} {{$shipping->state}} {{$shipping->zipcode}}</span><br />
                <span>{{$shipping->country}}</span><br />
              </div>
            </td>
            <td align="left" valign="top" width="2%">&nbsp;</td>
            <td align="left" valign="top" width="30%">    
                <span>&nbsp;</span> <br />         
                <span>PO Number : #</span><br />
                <span>&nbsp;</span><br />
                <span>Shipped On : {{$shipping->shipping_by}}</span><br />
              
            </td>
            <td align="left" valign="top" width="30%">   
                <span>Tracking Number(s)</span><br />
                <span>&nbsp;</span><br />
                <span>&nbsp;</span><br />
                <span>SKU : </span><br />
             
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <td align="left" valign="top" width="100%">&nbsp;</td>
    </tr>

    <tr>
      <td align="left" valign="top" width="100%" class="boxItem">Box</td>
    </tr>

    <tr>
        <td align="left" valign="top" width="100%">        
          <table border="0" cellpadding="0" cellspacing="0" width="100%">
              <thead class="title">
                <tr>
                  <th align="left" valign="top" width="15%">No.</th>
                  <th align="center" valign="top" width="10%">Size</th>
                  <th align="left" valign="top" width="10%">Group</th>
                  <th align="left" valign="top" width="15%">Color</th>
                  <th align="left" valign="top" width="30%">Description</th>
                  <th align="left" valign="top" width="10%">Defect Spoil</th>
                  <th align="center" valign="top" width="10%">Qnty</th>
                </tr>
              </thead>
              <tbody class="color-grey">
              @foreach ($shipping_boxes as $box)
                <tr>
                  <td align="left" valign="top" class="brdrBox" width="15%">{{$box->count}}</td>
                  <td align="center" valign="top" class="brdrBox" width="10%">{{$box->size}}</td>
                  <td align="left" valign="top" class="brdrBox" width="10%">{{$box->size_group_name}}</td>
                  <td align="left" valign="top" class="brdrBox" width="15%">{{$box->color_name}}</td>
                  <td align="left" valign="top" class="brdrBox" width="30%">{{$box->product_name}}</td>
                  <td align="left" valign="top" class="brdrBox" width="10%">{{$box->spoil}}</td>
                  <td align="center" valign="top" class="brdrBox" width="10%">{{$box->boxed_qnty}}</td>
                </tr>
              @endforeach
              </tbody>
          </table>
        </td>
      </tr>

      <tr>
        <td align="left" valign="top" width="100%">&nbsp;</td>
      </tr>
</table>
</body>
</html>
