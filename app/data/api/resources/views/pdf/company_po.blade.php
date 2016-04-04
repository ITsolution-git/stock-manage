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
            <td align="left" valign="top" width="25%">kavi
            </td>
            <td align="center" valign="top" width="25%" class="tableCol">
              <span style="font-size:15px; line-height:15px;">Ok
              </span>
              <br/>
            </td>
            <td align="right" valign="top" width="50%">PL
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td align="left" valign="top" width="100%">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td align="left" valign="top" width="50%">
              <span>ShipBy: {{$arr_poline[0]->ship_date}}</span><br>
              <span>In Hand Date: {{$arr_poline[0]->hand_date}}</span><br>
              <span>JobName: {{$arr_poline[0]->product_name}}</span>
            </td>
            <td align="left" valign="top" width="13%"></td>
            <td align="left" valign="top" width="40%" style="border:1px solid #999; border-radius: 2px; "><p><strong style="font-size:15px; line-height:15px;">SHIP TO</strong></p><br/>
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
          <td></td>
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
            <th valign="top" width="30%" style="border:1px solid #000000;"> <b>Item</b></th>
            <th valign="top" width="30%" style="border:1px solid #000000;"> <b>Description</b></th>
            <th valign="top" width="10%" style="border:1px solid #000000;"> <b>Color</b></th>
            <th valign="top" width="10%" style="border:1px solid #000000;"> <b>Size</b></th>
            <th valign="top" width="10%" style="border:1px solid #000000;"> <b>Qnty</b></th>
            <th valign="top" width="10%" style="border:1px solid #000000;"> <b>Cost Per</b></th>
          </tr>
        </thead>
        <tbody class="color-grey">
          <?php
          $total_pieces = 0;
          $po_total = 0;
          ?>
          @foreach ($arr_poline as $poline)
          <?php
          $total_pieces += $poline->qnty;
          $po_total += $poline->unit_price;
          ?>
          <tr>
            <td align="left" valign="top" class="brdrBox" width="30%" style="border:1px solid #000000;"> {{$poline->product_name}}</td>
            <td align="left" valign="top" class="brdrBox" width="30%" style="border:1px solid #000000;"> {{$poline->product_description}}</td>
            <td align="left" valign="top" class="brdrBox" width="10%" style="border:1px solid #000000;"> {{$poline->product_color}}</td>
            <td align="left" valign="top" class="brdrBox" width="10%" style="border:1px solid #000000;"> {{$poline->size}}</td>
            <td align="left" valign="top" class="brdrBox" width="10%" style="border:1px solid #000000;"> {{$poline->qnty}}</td>
            <td align="left" valign="top" class="brdrBox" width="10%" style="border:1px solid #000000;"> {{$poline->unit_price}}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </td>
  </tr>
  <tr>
    <td align="left" valign="top" width="100%">&nbsp;</td>
  </tr>
  <tr>
    <td align="left" valign="top" width="100%">
      <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tbody class="color-grey">
          <tr>
            <td align="right" valign="top" class="brdrBox" width="65%" ></td>
            <td align="right" valign="top" class="brdrBox" width="20%" >Total Pieces  </td>
            <td align="right" valign="top" class="brdrBox" width="5%" ></td>
            <td align="center" valign="top" class="brdrBox" width="10%" style="border:1px solid #000000;"> {{$total_pieces}}</td>
            
          </tr>
          <tr>
            <td align="right" valign="top" class="brdrBox" width="65%" ></td>
            <td align="right" valign="top" class="brdrBox" width="20%" >Po Total  </td>
            <td align="right" valign="top" class="brdrBox" width="5%" ></td>
            <td align="center" valign="top" class="brdrBox" width="10%" style="border:1px solid #000000;"> {{$po_total}}</td>
            
          </tr>
        </tbody>
      </table>
    </td>
  </tr>
</table>
</body>
</html>