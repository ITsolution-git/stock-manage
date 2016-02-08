<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="description" content="">
  <title>Shipping Manifest</title>

  <style type="text/css">
    p {
      margin:0;
      padding:0;
      font-size: 10px;
      line-height: 14px;
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
                    <td align="right" valign="top" width="50%">
                        <span><strong>Job # {{$shipping->order_id}}</strong></span>
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
            <td align="left" valign="top" width="38%"><span>SHIP TO</span>
              <p style="border:1px solid #000000;"><span>{{$shipping->description}}</span><br />
                <span>{{$shipping->address}} {{$shipping->address2}}</span><br/>
                <span>{{$shipping->city}} {{$shipping->state}}</span><br />
                <span>{{$shipping->zipcode}} {{$shipping->country}}</span>
              </p>
            </td>
            <td align="left" valign="top" width="2%">&nbsp;</td>
            <td align="left" valign="top" width="30%"><br /><br /><br /><span>PO Number : #</span><br />
                <span>&nbsp;</span><br />
                <span>Shipped On : {{$shipping->shipping_by}}</span> 
            </td>
            <td align="left" valign="top" width="30%"><br /><br /><br /><span>Tracking Number(s)</span><br />
                <span>&nbsp;</span><br />
                <span>SKU : </span>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <tr>
      <td align="left" valign="top" width="100%">&nbsp;</td>
    </tr>

    <tr>
      <td align="left" valign="top">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td align="left" valign="top" width="100%" style="border-top:1px solid #000000; border-bottom:1px solid #000000;">Box</td>      
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
              <thead class="title">
                <tr>
                  <th align="left" valign="top" width="10%" style="border:1px solid #000000;"> No.</th>
                  <th align="center" valign="top" width="10%" style="border:1px solid #000000;"> Size</th>
                  <th align="left" valign="top" width="15%" style="border:1px solid #000000;"> Group</th>
                  <th align="left" valign="top" width="15%" style="border:1px solid #000000;"> Color</th>
                  <th align="left" valign="top" width="30%" style="border:1px solid #000000;"> Description</th>
                  <th align="left" valign="top" width="10%" style="border:1px solid #000000;"> Defect Spoil</th>
                  <th align="center" valign="top" width="10%" style="border:1px solid #000000;"> Qnty</th>
                </tr>
              </thead>
              <tbody class="color-grey">
              @foreach ($shipping_boxes as $box)
                <tr>
                  <td align="left" valign="top" class="brdrBox" width="10%" style="border:1px solid #000000;"> {{$box->count}}</td>
                  <td align="center" valign="top" class="brdrBox" width="10%" style="border:1px solid #000000;"> {{$box->size}}</td>
                  <td align="left" valign="top" class="brdrBox" width="15%" style="border:1px solid #000000;"> {{$box->size_group_name}}</td>
                  <td align="left" valign="top" class="brdrBox" width="15%" style="border:1px solid #000000;"> {{$box->color_name}}</td>
                  <td align="left" valign="top" class="brdrBox" width="30%" style="border:1px solid #000000;"> {{$box->product_name}}</td>
                  <td align="left" valign="top" class="brdrBox" width="10%" style="border:1px solid #000000;"> {{$box->spoil}}</td>
                  <td align="center" valign="top" class="brdrBox" width="10%" style="border:1px solid #000000;"6> {{$box->boxed_qnty}}</td>
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