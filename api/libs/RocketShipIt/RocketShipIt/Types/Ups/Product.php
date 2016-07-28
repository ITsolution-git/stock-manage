<?php

namespace RocketShipIt\Types\Ups;

class Product extends \RocketShipIt\Types\Type
{
    public $xml = '
        <Product>
          <Description></Description>
          <Unit>
            <Number></Number>
            <Value></Value>
            <UnitOfMeasurement>
              <Code></Code>
              <Description></Description>
            </UnitOfMeasurement>
          </Unit>
          <CommodityCode></CommodityCode>
          <PartNumber></PartNumber>
          <OriginCountryCode></OriginCountryCode>
          <JointProductionIndicator></JointProductionIndicator>
          <NetCostCode></NetCostCode>
          <NetCostDateRange>
            <BeginDate></BeginDate>
            <EndDate></EndDate>
          </NetCostDateRange>
          <PreferenceCriteria></PreferenceCriteria>
          <ProducerInfo></ProducerInfo>
          <MarksAndNumbers></MarksAndNumbers>
          <NumberOfPackagesPerCommodity></NumberOfPackagesPerCommodity>
          <ProductWeight>
            <UnitOfMeasurement>
              <Code></Code>
              <Description></Description>
            </UnitOfMeasurement>
            <Weight></Weight>
          </ProductWeight>
          <VehicleID></VehicleID>
          <ScheduleB>
            <Number></Number>
            <Quantity></Quantity>
            <UnitOfMeasurement>
              <Code></Code>
              <Description></Description>
            </UnitOfMeasurement>
          </ScheduleB>
          <ExportType></ExportType>
          <SEDTotalValue></SEDTotalValue>
          <ExcludeFromForm>
            <FormType></FormType>
          </ExcludeFromForm>
          <ProductCurrencyCode></ProductCurrencyCode>
          <PackingListInfo>
            <PackageAssociated>
              <PackageNumber></PackageNumber>
              <ProductAmount></ProductAmount>
            </PackageAssociated>
          </PackingListInfo>
        </Product>
        ';

    public $tagMap = array(
        'invoiceLinePartNumber' => 'PartNumber',
        'invoiceLineOriginCountryCode' => 'OriginCountryCode',
        'numberOfPackagesPerCommodity' => 'NumberOfPackagesPerCommodity',
        'commodityCode' => 'CommodityCode',
        'netCostCode' => 'NetCostCode',
        'preferenceCriteria' => 'PreferenceCriteria',
        'producerInfo' => 'ProducerInfo',
        'exportType' => 'ExportType',
        'packageNumber' => 'PackageNumber',
        'productAmount' => 'ProductAmount',
        'sedTotalValue' => 'SEDTotalValue',
    );

    public $xpathMap = array(
        'invoiceLineNumber' => '//Unit/Number',
        'invoiceLineValue' => '//Unit/Value',
        'invoiceLineDescription' => '/Product/Description',
        'weightUnit' => '//ProductWeight/UnitOfMeasurement/Code',
        //'weightUnit' => '//ProductWeight/UnitOfMeasurement/Description',
        'weight' => '//ProductWeight/Weight',
        'scheduleBNumber' => '//ScheduleB/Number',
        'scheduleBQty' => '//ScheduleB/Quantity',
        'scheduleBUnitCode' => '//ScheduleB/UnitOfMeasurement/Code',
        'unitCode' => '//Unit/UnitOfMeasurement/Code'
    );

    public $staticMap = array(
        // 'SEDTotalValue' => '20.00'
    );

    public $staticXpathMap = array(
        // '//ScheduleB/Quantity' => '1',
    );

    public function buildDocument()
    {
        $dom = new \DOMDocument;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($this->xml);
        $dom->formatOutput = true;

        foreach ($this->tagMap as $param => $tag) {
            if (!isset($this->parameters[$param])) {
                continue;
            }
            $this->setTags($dom, $tag, $this->parameters[$param]);
        }

        foreach ($this->xpathMap as $param => $query) {
            if (!isset($this->parameters[$param])) {
                continue;
            }
            $this->setTagWithXpath($dom, $query, $this->parameters[$param]);
        }

        foreach ($this->staticMap as $tag => $value) {
            $this->setTags($dom, $tag, $value);
        }

        foreach ($this->staticXpathMap as $query => $value) {
            $this->setTagWithXpath($dom, $query, $value);
        }

        //$this->removeTags($dom, 'ExportDate');
        $this->removeBlank($dom);

        return $dom;
    }

    function buildFormXml()
    {
        $dom = $this->buildDocument();
        return $dom->saveXML($dom->documentElement);
    }
}
