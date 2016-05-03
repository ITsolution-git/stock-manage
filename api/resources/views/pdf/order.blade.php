
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="description" content="">
  <title>Orders Print</title>

  <style type="text/css">
    @media print {
      body { margin: 0; padding: 0; }
      td.tableCol { margin: 0; padding: 0; }
      h1.title { margin: 0; padding: 0; font-size: 15px; line-height: 16px; }
      .brdrBox { border:1px solid #999999; }
      div.payDetails { margin: 0; padding: 0; line-height: 1; }
      span { margin: 0; padding: 0; line-height: 1; }
      td { margin: 0; padding: 0; font-size: 12px !important; }
   }
    
  </style>
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" width="100%">    
  <tr>
    <td align="left" valign="top" width="100%">          
      <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <td align="left" valign="top" width="25%">

            <img src="" alt="Logo" style="display:block; max-width:100%;" width="80" />
          </td>
          <td align="center" valign="top" width="25%" class="tableCol">
            <span style="font-size:15px; line-height:15px;">
                <strong>{{$data['company_detail'][0]->name}}</strong>                
            </span>
            <br/>
            <span>{{$data['company_detail'][0]->prime_address1}}, {{$data['company_detail'][0]->prime_address_city}}, {{$data['company_detail'][0]->prime_address_state}}, <br/>{{$data['company_detail'][0]->prime_address_country}} - {{$data['company_detail'][0]->prime_address_zip}}<br />{{$data['company_detail'][0]->url}}</span>
          </td>
          <td align="right" valign="top" width="50%">
            <span>
                <strong>Estimate #{{$data['order']->id}}</strong> <br/> 
                <strong>Created On: {{$data['order']->created_date}}</strong><br/>
                <strong>Job Name:  {{$data['order']->name}}</strong></p>
          </td>
        </tr>
        <tr>
          <td align="left" valign="top">&nbsp;</td>
        </tr>
      </table>
    </td>
  </tr>
    </table>
  </body>
  </html>