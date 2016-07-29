<?php

namespace RocketShipIt\Service\Rate;

use \RocketShipIt\Helper\XmlParser;
use \RocketShipIt\Helper\XmlBuilder;
use \RocketShipIt\Helper\General;

/**
* Main Rate class for producing rates for various packages/shipments
*
* This class is a wrapper for use with all carriers to produce rates 
* Valid carriers are: UPS, USPS, and FedEx.
*/
class Usps extends \RocketShipIt\Service\Common implements \RocketShipIt\RateInterface
{
    var $packageCount;
    
    function __construct()
    {
        $classParts = explode('\\', __CLASS__);
        $carrier = end($classParts);
        $this->helper = new General;
        parent::__construct($carrier);
    }

    public function getSimpleRates($user_func=null)
    {
        $usps = $this->getAllRates();
        return $this->simplifyUSPSRatesXml($this->core->xmlResponse, $user_func);
    }

    function simplifyUSPSRatesXml($xml, $user_func=null)
    {
        $rates = array();
        $xmlObj = \simplexml_load_string($xml);

        if (isset($xmlObj->Description)) {
            return array(
                'error' => (string) $xmlObj->Description
            );
        }

        foreach ($xmlObj->Package as $package) {
            if (isset($package->Error)) {
                return array('error' => $package->Error->Description);
            }

            $services = array();
            if (!isset($package->Service)) {
                $services = $package->Postage;
            } else {
                $services = $package->Service;
            }

            foreach ($services as $service) {
                $serviceDescription = '';
                if (isset($service->SvcDescription)) {
                    $serviceDescription = $this->stripHtml($service->SvcDescription);
                }
                if (isset($service->MailService)) {
                    $serviceDescription = $this->stripHtml($service->MailService);
                }

                $postage = 0.00;
                if (isset($service->Postage)) {
                    $postage = (string) $service->Postage;
                }
                if (isset($service->Rate)) {
                    $postage = (string) $service->Rate;
                }
                if (isset($service->CommercialRate)) {
                    $postage = (string) $service->CommercialRate;
                }
                $attrs = $service->attributes();
                if (isset($attrs->ID)) {
                    $serviceCode = (string) $attrs->ID;
                }
                if (isset($attrs->CLASSID)) {
                    $serviceCode = (string) $attrs->CLASSID;
                }

                if (!isset($rates[$serviceCode])) {
                    $rate = array('desc' => $serviceDescription, 'rate' => $postage, 'service_code' => $serviceCode);
                    $rates[$serviceCode] = $rate;
                } else {
                    $rates[$serviceCode]['rate'] += $postage;
                }

                if (!empty($user_func)) {
                    $classParts = explode('\\', __CLASS__);
                    $carrier = end($classParts);
                    $rates[] = call_user_func($user_func, $carrier, $service, $rates[$serviceCode]);
                }
            }
        }

        if (empty($rates)) {

            return $this->arrayFromXml($this->core->xmlResponse);
        }
        
        return array_values($rates);
    }

    function getSimpleRate()
    {
        $usps = $this->getRate();
        if (!isset($usps['Error']) && !isset($usps['RateV4Response']['Package']['Error'])) {
            return $usps['RateV4Response']['Package']['Postage']['Rate'];
        } else {
            if (isset($usps['Error']['Description'])) {
                return $usps['Error']['Description'];
            } else {
                return $usps['RateV4Response']['Package']['Error']['Description'];
            }
        }
    }

    function getAllRates()
    {
        return $this->getRate(true);
    }

    function getRate($allAvailableRates=false)
    {
        if (!$this->isInternational($this->toCountry)) {
            $xmlString = $this->buildUSPSRateXml($allAvailableRates);
        } else {
            if ($allAvailableRates) {
                $xmlString = $this->buildUSPSInternationalRateXml($allAvailableRates);
            } else {
                return array('error' => 'Please use getAllRates() for international USPS rate quotes');
            }
        }
        
        $this->core->request('ShippingAPI.dll', $xmlString);

        // Convert the xmlString to an array
        return $this->arrayFromXml($this->core->xmlResponse);
    }

    function addPackageToShipment($package)
    {
        $isInternational = $this->isInternational($this->toCountry);

        if (!isset($this->core->packagesObject)) {
            $this->core->packagesObject = new xmlBuilder(true);
        }

        $xml = $this->core->packagesObject;

        if ($this->machinable == '') {
            $this->machinable = 'false';
        }
        
        // Create package ID
        $packageId = NULL;
        switch(substr($this->packageCount, -1)) { 
            case 1: $packageId = $this->packageCount . 'ST'; break;
            case 2: $packageId = $this->packageCount . 'ND'; break;
            case 3: $packageId = $this->packageCount . 'RD'; break;
            default: $packageId = $this->packageCount . 'TH'; break;
        }
        
        if ($isInternational) {
            
            $country = (strlen($this->toCountry) == 2) 
                        ? $this->core->getCountryName($this->toCountry)
                        : $this->toCountry;
            
            // Assign options
            $options = array(
                'packageId'     => $packageId,
                'toCountry'     => $country,
        		'weightPounds'	=> $package->weightPounds,
            	'weightOunces'  => $package->weightOunces,
            	'weight'        => $package->weight,
                'packagingType' => $this->packagingType,
                'width'         => $package->width,
                'height'        => $package->height,
                'length'        => $package->length
            );
            $xml = $this->buildUSPSInternationalPackage($xml, $options);
        } else {
            $xml->push('Package', array('ID' => $packageId));
            if ($this->service == 'ONLINE') {
                $xml->element('Service', 'ONLINE');
            } else {
                $xml->element('Service', 'ALL');
            }
            if ($this->firstClassMailType != '') {
                $xml->element('FirstClassMailType', $package->firstClassMailType);
            }
            $xml->element('ZipOrigination',$this->shipCode);
            $xml->element('ZipDestination',$this->toCode);

            // Calculate weight in lbs and ounces based on weight parameter
            if ($package->weight != '') {
                $lbsAndOunces = $this->helper->weightToLbsOunces($package->weight);
                $xml->element('Pounds', $lbsAndOunces[0]);
                $xml->element('Ounces', $lbsAndOunces[1]);
            } else {
                $xml->element('Pounds', (string) $package->weightPounds);
                $xml->element('Ounces', (string) $package->weightOunces);
            }

            if ($package->container != '') {
                $xml->element('Container', $package->container);
            } else {
                $xml->emptyelement('Container');
            }
            
            $girth = $package->length + ($package->height*2) + ($package->width*2);

            if($girth > 84) $xml->element('Size','LARGE');
            else $xml->element('Size','Regular');

            $xml->element('Width', $package->width);
            $xml->element('Length', $package->length);
            $xml->element('Height', $package->height);
            $xml->element('Girth', $girth);

            $xml->element('Machinable', $this->machinable);
            $xml->pop(); // Close Package  
        }
        
        $this->core->packagesObject = $xml;

        return true;
    }
    
    function buildUSPSInternationalPackage($xml, $options)
    {
        if ($this->machinable == '') {
            $this->machinable = 'false';
        }
        $xml->push('Package',array('ID' => $options['packageId']));
            
            if ($options['weight'] != '') {
                $weight =  $options['weight'];
                $lbsAndOunces = $this->helper->weightToLbsOunces($weight);
                $xml->element('Pounds', $lbsAndOunces[0]);
                $xml->element('Ounces', $lbsAndOunces[1]);
            } else {
                $xml->element('Pounds', $options['weightPounds']);
                $xml->element('Ounces', $options['weightOunces']);
            }

            $xml->element('Machinable', $this->machinable);
            
            if ($options['packagingType'] != '') {
                $xml->element('MailType', $options['packagingType']);
            } else {
                $xml->element('MailType', 'Package');
            }

            $xml->push('GXG');
                $xml->element('POBoxFlag', 'N');
                $xml->element('GiftFlag', 'N');
            $xml->pop();

            if (isset($options['insuredValue'])) {
                if ($options['insuredValue'] != '') {
                    $xml->element('ValueOfContents', $options['insuredValue']);
                } else {
                    $xml->emptyelement('ValueOfContents');
                }
            } else {
                $xml->emptyelement('ValueOfContents');
            }

            $xml->element('Country', $options['toCountry']);
            
            if (isset($options['container'])) {
                if ($options['container'] != '') {
                    $xml->element('Container', $options['container']);
                } else {
                    $xml->emptyelement('Container');
                }
            } else {
                $xml->element('Container', 'RECTANGULAR');
            }
            
            $girth = ($options['length'] * 2) + ($options['width'] * 2);

            if ($options['length'] + $girth > 84) $xml->element('Size', 'LARGE');
            else $xml->element('Size', 'REGULAR');
            
            $xml->element('Width', (string)$options['width']);
            $xml->element('Length',(string)$options['length']);
            $xml->element('Height',(string)$options['height']);
            $xml->element('Girth', (string)$girth);

            // Required for GXG rates
            $xml->element('OriginZip', $this->shipCode);
            
        $xml->pop(); // Close Package

        return $xml;
    }

    function buildUSPSRateXml($allAvailableRates=false)
    {
        $xml = $this->core->xmlObject;

        if ($this->machinable == '') {
            $this->machinable = 'false';
        }
        $xml->push('RateV4Request', array('USERID' => $this->userid));
            $xml->element('Revision', '2'); // Turns on added services
            if (!isset($this->core->packagesObject)) {
                $xml->push('Package', array('ID' => '1ST'));
                    if ($allAvailableRates) {
                        if ($this->service == 'ONLINE') {
                            $xml->element('Service', 'ONLINE');
                        } else {
                            $xml->element('Service', 'ALL');
                        }
                    } else {
                        $xml->element('Service', $this->service);
                    }
                    if ($this->firstClassMailType != '') {
                        $xml->element('FirstClassMailType',$this->firstClassMailType);
                    }
                    $xml->element('ZipOrigination', $this->shipCode);
                    $xml->element('ZipDestination', $this->toCode);

                    // Calculate weight in lbs and ounces based on weight parameter
                    if ($this->weight != '') {
                        $lbsAndOunces = $this->helper->weightToLbsOunces($this->weight);
                        $xml->element('Pounds', $lbsAndOunces[0]);
                        $xml->element('Ounces', $lbsAndOunces[1]);
                    } else {
                        $xml->element('Pounds', $this->weightPounds);
                        $xml->element('Ounces', $this->weightOunces);
                    }

                    if ($this->packagingType != '') {
                        $xml->element('Container', $this->packagingType);
                    } else {
                        $xml->emptyelement('Container');
                    }
                    
                    $girth = ($this->height*2) + ($this->width*2);

                    if ($this->length + $girth > 84 && $this->length + $girth <= 108) {
                        $xml->element('Size','LARGE');
                    } elseif($this->length + $girth > 108 && $this->length + $girth <= 130) {
                        $xml->element('Size','OVERSIZE');
                    } else {
                        $xml->element('Size','REGULAR');
                    }

                    $xml->element('Width', $this->width);
                    $xml->element('Length', $this->length);
                    $xml->element('Height', $this->height);
                    $xml->element('Girth', $girth);

                    if ($this->groundOnly != '') {
                        $xml->element('GroundOnly', 'true');
                    }

                    $xml->element('Machinable', $this->machinable);
                $xml->pop(); // Close Package
                $xmlString = $xml->getXml();
            } else {
                $xmlString = $xml->getXml();
                $xmlString .= $this->core->packagesObject->getXml();
            }
            $xmlString .= '</RateV4Request>'."\n";

        return 'API=RateV4&XML='. $xmlString;
    }

    function buildUSPSInternationalRateXml($allAvailableRates=false)
    {
        $xmlString = '';
        
        $country = (strlen($this->toCountry) == 2) 
                        ? $this->core->getCountryName($this->toCountry)
                        : $this->toCountry;
        
        $xml = $this->core->xmlObject;
        $xml->push('IntlRateV2Request',array('USERID' => $this->userid));
            
            $xml->element('Revision','2');
            
            if (!isset($this->core->packagesObject)) {
                
                $options = array(
                    'packageId'     => '1ST',
                    'toCountry'     => $country,
            		'weightPounds'	=> $this->weightPounds,
                	'weightOunces'  => $this->weightOunces,
                	'weight'        => $this->weight,
                    'packagingType' => $this->packagingType,
                    'width'         => $this->width,
                    'height'        => $this->height,
                    'length'        => $this->length,
                    'container'     => $this->container,
                    'insuredValue'  => $this->insuredValue
                );
                $xml = $this->buildUSPSInternationalPackage($xml, $options);
                $xmlString = $xml->getXml();
                
            } else {
                $xmlString = $xml->getXml();
                $xmlString .= $this->core->packagesObject->getXml();
            }
        
        $xmlString .= '</IntlRateV2Request>'."\n";
        return 'API=IntlRateV2&XML='. $xmlString;        
    }

    // Checks the country to see if the request is International
    function isInternational($country)
    {
        if ($country == '') {
            return false;
        }
        if ($country == 'US') {
            return false;
        }
        if ($country == $this->core->getCountryName('US')) {
            return false;
        }

        return true;
    }

    function stripHtml($text)
    {
        $no_html = strip_tags(html_entity_decode($text, ENT_COMPAT, 'UTF-8'));
        // Strip html special chars
        $no_html = str_replace('&reg;', '', $no_html);
        return preg_replace('/[^A-Za-z0-9\- ]/', '', $no_html);
    }
}
