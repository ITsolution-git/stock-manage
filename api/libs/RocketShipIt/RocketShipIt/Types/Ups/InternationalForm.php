<?php

namespace RocketShipIt\Types\Ups;

use \RocketShipIt\Helper\DomBuilder;

class InternationalForm
{

    public $xml = '<InternationalForms>
        <FormType></FormType>
        <CN22Form/>
        <UserCreatedForm>
          <DocumentID></DocumentID>
        </UserCreatedForm>
        <AdditionalDocumentIndicator></AdditionalDocumentIndicator>
        <FormGroupIdName></FormGroupIdName>
        <SEDFilingOption></SEDFilingOption>
        <Contacts>
          <ForwardAgent>
            <CompanyName></CompanyName>
            <TaxIdentificationNumber></TaxIdentificationNumber>
            <Address>
              <AddressLine1></AddressLine1>
              <AddressLine2></AddressLine2>
              <AddressLine3></AddressLine3>
              <City></City>
              <StateProvinceCode></StateProvinceCode>
              <PostalCode></PostalCode>
              <CountryCode></CountryCode>
            </Address>
          </ForwardAgent>
          <UltimateConsignee>
            <CompanyName></CompanyName>
            <Address>
              <AddressLine1></AddressLine1>
              <AddressLine2></AddressLine2>
              <AddressLine3></AddressLine3>
              <City></City>
              <StateProvinceCode></StateProvinceCode>
              <PostalCode></PostalCode>
              <CountryCode></CountryCode>
            </Address>
          </UltimateConsignee>
          <IntermediateConsignee>
            <CompanyName></CompanyName>
            <Address>
              <AddressLine1></AddressLine1>
              <AddressLine2></AddressLine2>
              <AddressLine3></AddressLine3>
              <City></City>
              <StateProvinceCode></StateProvinceCode>
              <PostalCode></PostalCode>
              <CountryCode></CountryCode>
            </Address>
          </IntermediateConsignee>
          <Producer>
            <Option></Option>
            <CompanyName></CompanyName>
            <TaxIdentificationNumber></TaxIdentificationNumber>
            <Address>
              <AddressLine1></AddressLine1>
              <AddressLine2></AddressLine2>
              <AddressLine3></AddressLine3>
              <City></City>
              <StateProvinceCode></StateProvinceCode>
              <PostalCode></PostalCode>
              <CountryCode></CountryCode>
            </Address>
            <AttentionName></AttentionName>
            <Phone>
              <Number></Number>
              <Extension></Extension>
            </Phone>
            <EMailAddress></EMailAddress>
          </Producer>
        </Contacts>
        <ProductsPlaceholder/>
        <InvoiceNumber></InvoiceNumber>
        <InvoiceDate></InvoiceDate>
        <PurchaseOrderNumber></PurchaseOrderNumber>
        <TermsOfShipment></TermsOfShipment>
        <ReasonForExport></ReasonForExport>
        <Comments></Comments>
        <DeclarationStatement></DeclarationStatement>
        <Discount>
          <MonetaryValue></MonetaryValue>
        </Discount>
        <FreightCharges>
          <MonetaryValue></MonetaryValue>
        </FreightCharges>
        <InsuranceCharges>
          <MonetaryValue></MonetaryValue>
        </InsuranceCharges>
        <OtherCharges>
          <MonetaryValue></MonetaryValue>
          <Description></Description>
        </OtherCharges>
        <CurrencyCode></CurrencyCode>
        <BlanketPeriod>
          <BeginDate></BeginDate>
          <EndDate></EndDate>
        </BlanketPeriod>
        <ExportDate></ExportDate>
        <ExportingCarrier></ExportingCarrier>
        <CarrierID></CarrierID>
        <InBondCode></InBondCode>
        <EntryNumber></EntryNumber>
        <PointOfOrigin></PointOfOrigin>
        <ModeOfTransport></ModeOfTransport>
        <PortOfExport></PortOfExport>
        <PortOfUnloading></PortOfUnloading>
        <LoadingPier></LoadingPier>
        <PartiesToTransaction></PartiesToTransaction>
        <RoutedExportTransactionIndicator></RoutedExportTransactionIndicator>
        <ContainerizedIndicator></ContainerizedIndicator>
        <License>
          <Number></Number>
          <Date></Date>
          <ExceptionCode></ExceptionCode>
        </License>
        <ECCNNumber></ECCNNumber>
        <OverridePaperlessIndicator></OverridePaperlessIndicator>
        <ShipperMemo></ShipperMemo>
        <MultiCurrencyInvoiceLineTotal></MultiCurrencyInvoiceLineTotal>
      </InternationalForms>';

    public $tagMap = array(
        'invoice' => 'InvoiceNumber',
        'invoiceDate' => 'InvoiceDate',
        'exportDate' => 'ExportDate',
        'inBondCode' => 'InBondCode',
        'pointOfOrigin' => 'PointOfOrigin',
        'modeOfTransport' => 'ModeOfTransport',
        'partiesToTransaction' => 'PartiesToTransaction',
        'exportingCarrier' => 'ExportingCarrier',
        'blanketBeginDate' => 'BeginDate',
        'blanketEndDate' => 'EndDate',
        'currency' => 'CurrencyCode',
        'reasonForExport' => 'ReasonForExport',
    );

    public $xpathMap = array(
        'customsFormType' => '/InternationalForms/FormType',
        'licenseNumber' => '//License/Number',
        'licenseDate' => '//License/Date',
        'consigneeName' => '//UltimateConsignee/CompanyName',
        'consigneeAddr1' => '//UltimateConsignee/Address/AddressLine1',
        'consigneeCity' => '//UltimateConsignee/Address/City',
        'consigneeCountry' => '//UltimateConsignee/Address/CountryCode',
        'producerName' => '//Producer/CompanyName',
        'producerAddr1' => '//Producer/Address/AddressLine1',
        'producerCity' => '//Producer/Address/City',
        'producerState' => '//Producer/Address/StateProvinceCode',
        'producerCountry' => '//Producer/Address/CountryCode',
        'freightCharges' => '//FreightCharges/MonetaryValue',
    );

    public $staticMap = array(
        'DeclarationStatement' => 'I hereby certify that the information on this invoice is true and correct and the contents and value of this shipment is as stated above.',
        'SEDFilingOption' => '01' // Only available option
    );

    function buildCn22Form()
    {
        $xml = '<CN22Form>
          <LabelSize>6</LabelSize>
          <PrintsPerPage>1</PrintsPerPage>
          <LabelPrintType>pdf</LabelPrintType>
          <CN22Type>4</CN22Type>
          <CN22OtherDescription>merchandise</CN22OtherDescription>
          <FoldHereText />
          <CN22ContentPlaceholder />
        </CN22Form>
        ';

        $builder = new DomBuilder($xml);
        $builder->parameters = $this->parameters;
        $staticTags = array(
            'cn22LabelSize' => 'LabelSize',
            'cn22PrintsPerPage' => 'PrintsPerPage',
            'cn22LabelPrintType' => 'LabelPrintType',
            'cn22Type' => 'CN22Type',
            'cn22OtherDescription' => 'CN22OtherDescription',
        );
        $builder->mapTags($staticTags);
        if (isset($this->parameters['cn22Content'])) {
            foreach ($this->parameters['cn22Content'] as $params) {
                $builder->insertDomBefore('CN22ContentPlaceholder', $this->buildCn22Content($params));
            }
        }

        return $builder->dom;
    }

    function buildCn22Content($params)
    {
        $xml = '<CN22Content>
            <CN22ContentQuantity>1</CN22ContentQuantity>
            <CN22ContentDescription>3 x New Garcinia Cambogia</CN22ContentDescription>
            <CN22ContentWeight>
              <UnitOfMeasurement>
                <Code>OZS</Code>
              </UnitOfMeasurement>
              <Weight>0.3</Weight>
            </CN22ContentWeight>
            <CN22ContentTotalValue>19.00</CN22ContentTotalValue>
            <CN22ContentCurrencyCode>USD</CN22ContentCurrencyCode>
            <CN22ContentCountryOfOrigin>US</CN22ContentCountryOfOrigin>
          </CN22Content>
        ';

        $builder = new DomBuilder($xml);
        $builder->parameters = $params;
        $staticTags = array(
            'cn22ContentQty' => 'CN22ContentQuantity',
            'cn22ContentDescription' => 'CN22ContentDescription',
            'cn22ContentWeightUnit' => 'Code',
            'cn22ContentWeight' => 'Weight',
            'cn22ContentTotalValue' => 'CN22ContentTotalValue',
            'cn22ContentCurrencyCode' => 'CN22ContentCurrencyCode',
            'cn22ContentCountryOfOrigin' => 'CN22ContentCountryOfOrigin',
        );
        $builder->mapTags($staticTags);

        return $builder->dom;
    }

    function buildFormXml()
    {
        $builder = new DomBuilder($this->xml);
        $builder->parameters = $this->parameters;
        $builder->mapTags($this->tagMap);
        $builder->mapXpaths($this->xpathMap);
        $builder->mapStaticTags($this->staticMap);

        if (isset($this->parameters['customs'])) {
            foreach ($this->parameters['customs'] as $params) {
                $product = new \RocketShipIt\Types\Ups\Product;
                $product->parameters = $params;
                $newDom = $product->buildDocument();
                $builder->insertDomBefore('ProductsPlaceholder', $newDom);
            }
        }

        if ($this->parameters['customsFormType'] == '09') {
            $builder->insertDomInto('//CN22Form', $this->buildCn22Form());
        }

        $builder->removeBlank();

        return $builder->getXml();
    }
}
