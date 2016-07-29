<?php

namespace RocketShipIt\Helper;

/**
 * General.
 *
 * @author RocketShipIt
 */
class General
{
    public function startsWith($haystack, $needle)
    {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }

    public function endsWith($haystack, $needle)
    {
        return $needle === '' || substr($haystack, -strlen($needle)) === $needle;
    }

    public function getCarrierFromTracking($trackNum)
    {
        if ($this->isValidUpsTracking($trackNum)) {
            return 'UPS';
        }

        if ($this->isValidUspsTracking($trackNum)) {
            return 'USPS';
        }

        if ($this->isValidDhlTracking($trackNum)) {
            return 'DHL';
        }

        if ($this->isValidFedexTracking($trackNum)) {
            return 'FEDEX';
        }
    }

    public function isValidTracking($carrier, $trackNum)
    {
        $carrier = strtoupper($carrier);
        if ($carrier == 'UPS') {
            return $this->isValidUpsTracking($trackNum);
        }

        if ($carrier == 'FEDEX') {
            return $this->isValidFedexTracking($trackNum);
        }

        if ($carrier == 'USPS') {
            return $this->isValidUspsTracking($trackNum);
        }

        if ($carrier == 'DHL') {
            return $this->isValidDhlTracking($trackNum);
        }

        return false;
    }

    /*
      Determine if this is a UPS tracking number.
      This checks the format of the number and calculates
      if the checkdigit is correct.

      Returns: true - tracking number is OK
               false - not a valid tracking number


    */
    public function isValidUpsTracking($tracknum)
    {
        if (!preg_match('/^(1Z[A-Z0-9]{16}|[0-9]{11}|[A-T][0-9]{10})$/i', $tracknum)) {
            return false;
        }

        //
        //  Calculate checkdigit and compare
        //
        $tracknum = strtoupper($tracknum);

        $str = preg_replace('/^(1Z|[A-Z])/', '', $tracknum);
        $len = strlen($str);
        $last_ch = substr($str, -1);

        $t = 0;
        for ($i = 0; $i < ($len - 1); ++$i) {
            $ch = substr($str, $i, 1);
            $n = ord($ch) - 48;
            if ($n < 0 || $n >= 10) {
                $n = (ord($ch) - 63) % 10;  // non-numeric
            }

            if (($i % 2) == 0) {
                $t += $n;
            } else {
                $t += (2 * $n);
            }
        }

        $x = $t % 10;
        if ($x == 0) {
            $checkdigit = $x;
        } else {
            $checkdigit = 10 - $x;
        }

        if ($last_ch != $checkdigit) {
            return false;
        }

        return true;
    }

    /*
      Determines if this a valid DHL tracking number
      with the proper checkdigit.

      DHL calculates a checkdigit by doing a MOD 7 on
      the first part of the tracking number (everything
      except the final digit).

      Returns: true - tracking number is OK
               false - not a valid tracking number
    */
    public function isValidDhlTracking($tracknum)
    {
        # tracking numbes are 10 digits
        if (!preg_match('/^([0-9]{10})$/i', $tracknum)) {
            return false;
        }

        $len = strlen($tracknum);
        $last_ch = substr($tracknum, -1);

        $checkdigit = substr($tracknum, 0, $len - 1) % 7;

        if ($last_ch != $checkdigit) {
            return false;
        }

        return true;
    }

    /*
      Determine if this is a FedEx tracking number.
      This checks the format of the number and calculates
      if the checkdigit is correct.

      Returns: true - tracking number is OK
               false - not a valid tracking number
    */
    public function isValidFedexTracking($tracknum)
    {
        if (!preg_match('/^([0-9]{12}|[0-9]{14,15}|[0-9]{22}|[0-9]{34})$/i', $tracknum)) {
            return false;
        }

        if (strlen($tracknum) == 34) {
            // Take last 14, strip off leading zeros
            $tracknum = ltrim(substr($tracknum, -14), '0');
        }

        $len = strlen($tracknum);
        $last_ch = substr($tracknum, -1);
        $stop = 0;
        $mod = 3;

        if ($len == 12) {  // EXPRESS - 12 digits
            $stop = 0;
            $mod = 3;
        } elseif ($len == 22) {  // GROUND - 22 digits
            $stop = 7;
            $mod = 2;
        } elseif ($len == 14 || $len == 15) {  // GROUND - 14 or 15 digits
            $stop = 0;
            $mod = 2;
        }

        $t = 0;
        $j = 0;
        for ($i = ($len - 2); $i >= $stop; --$i) {
            $ch = substr($tracknum, $i, 1);
            $n = ord($ch) - 48;
            $pos = $j % $mod;

            if ($mod == 3) {
                if ($pos == 0) {
                    $t += $n;
                } elseif ($pos == 1) {
                    $t += ($n * 3);
                } else {
                    $t += ($n * 7);
                }
            } elseif ($mod == 2) {
                if ($pos == 0) {
                    $t += ($n * 3);
                } else {
                    $t += $n;
                }
            }
            ++$j;
        }

        if ($mod == 3) {
            $x = $t % 11;
            if ($x == 0 || $x == 10) {
                $checkdigit = 0;
            } else {
                $checkdigit = $x;
            }
        } elseif ($mod == 2) {
            $x = $t % 10;
            if ($x == 0) {
                $checkdigit = 0;
            } else {
                $checkdigit = 10 - $x;
            }
        }

        if ($last_ch != $checkdigit) {
            # might be FedEx Smartpost tracking
            if ($len == 22) {
                return $this->isValidUspsTracking($tracknum);
            }

            return false;
        }

        return true;
    }

    /*
      Returns true if this is a USPS tracking number
      with the proper checkdigit.

      Based on information from USPS publications:
        http://www.usps.com/cpim/ftp/pubs/pub91.pdf
        http://www.usps.com/cpim/ftp/pubs/pub97.pdf
    */
    public function isValidUspsTracking($tracking)
    {
        if (preg_match('/^(\d{2})(\d{18})$/', $tracking, $m)) {  # 20 digits
            //
            //  Check first 2 digits for Service Type Code and
            //  add a '91' prefix for certain codes
            //
            if ($m[1] == '04') {
                $tracking = '91'.$tracking;
            }

            return($this->isUspsMod10($tracking));
        } elseif (preg_match('/^(\d{8})(\d{22})$/', $tracking, $m)) {  // 30 digits
            return($this->isUspsMod10($m[2]));
        } elseif (preg_match('/^(91|94|95)\d{20}$/', $tracking)) { // 22 digits, begins with '91','94' or '95
            return($this->isUspsMod10($tracking));
        } elseif (preg_match('/^[A-Z]{2}(\d{9})[A-Z]{2}$/i', $tracking, $m)) {  // intl (i.e. 'CJ249113184US')
            return($this->isUspsMod11($m[1]));
        }

        return false;
    }

    /*
      Return true if this is a
      USPS digit string with a
      valid MOD-10 checkdigit.

      MOD-10 is used for domestic
      mail only.
    */
    public function isUspsMod10($str)
    {
        $len = strlen($str);
        $sum = 0;
        for ($i = 0; $i < $len - 1; ++$i) {
            $d = substr($str, $i, 1);

            if ($i % 2) {
                $sum += $d;     # ODD position (1,3,5...)
            } else {
                $sum += $d * 3; # EVEN position (2,4,6...)
            }
        }
        $remainder = $sum % 10;
        if ($remainder) {
            $checkdigit = 10 - $remainder;
        } else {
            $checkdigit = $remainder;
        }
        $lastdigit = substr($str, $len - 1, 1);

        return ($lastdigit == $checkdigit);
    }

    /*
      Return true if this is a
      USPS digit string with a
      valid MOD-11 checkdigit

      MOD-11 is required for international mail,
      but may be used for domestic mail with
      USS code 39.
    */
    public function isUspsMod11($str)
    {
        //
        //  Do MOD 11 verification of checkdigit
        //
        static $f = array(8,6,4,2,3,5,9,7);

        $len = strlen($str);
        $sum = 0;
        for ($i = 0; $i < ($len - 1); ++$i) {
            $d = substr($str, $i, 1);
            $sum += $d * $f[$i];
        }
        $remainder = $sum % 11;

        if ($remainder == 0) {
            $checkdigit = 5;
        } elseif ($remainder == 1) {
            $checkdigit = 0;
        } else {
            $checkdigit = 11 - $remainder;
        }
        $lastdigit = substr($str, ($len - 1), 1);

        return ($lastdigit == $checkdigit);
    }

    public function jsonPrettyPrint($json)
    {
        $result = '';
        $level = 0;
        $prev_char = '';
        $in_quotes = false;
        $ends_line_level = null;
        $json_length = strlen($json);

        for ($i = 0; $i < $json_length; ++$i) {
            $char = $json[$i];
            $new_line_level = null;
            $post = '';
            if ($ends_line_level !== null) {
                $new_line_level = $ends_line_level;
                $ends_line_level = null;
            }
            if ($char === '"' && $prev_char != '\\') {
                $in_quotes = !$in_quotes;
            } elseif (!$in_quotes) {
                switch ($char) {
                    case '}': case ']':
                        $level--;
                        $ends_line_level = null;
                        $new_line_level = $level;
                        break;

                    case '{': case '[':
                        $level++;
                    case ',':
                        $ends_line_level = $level;
                        break;

                    case ':':
                        $post = ' ';
                        break;

                    case ' ': case '  ': case "\n": case "\r":
                        $char = '';
                        $ends_line_level = $new_line_level;
                        $new_line_level = null;
                        break;
                }
            }
            if ($new_line_level !== null) {
                $result .= "\n".str_repeat('  ', $new_line_level);
            }
            $result .= $char.$post;
            $prev_char = $char;
        }

        return $result;
    }

    public function getValueFromPath($arr, $path, $default = null)
    {
        $path = explode('/', $path);
        $dest = $arr;
        $finalKey = array_pop($path);
        foreach ($path as $key) {
            if (!isset($dest[$key])) {
                return $default;
            }
            $dest = $dest[$key];
        }
        if (!isset($dest[$finalKey])) {
            return $default;
        }

        return $dest[$finalKey];
    }

    public function setValueFromPath(&$arr, $path, $value)
    {
        $path = explode('/', $path);
        $dest = &$arr;
        $finalKey = array_pop($path);
        foreach ($path as $key) {
            $dest = &$dest[$key];
        }
        $dest[$finalKey] = $value;
    }

    public function weightToLbsOunces($weight)
    {
        list($lbs, $partialLb) = explode('.', "$weight.");
        $actualOunces = ($weight - floor($weight)) * 16;
        if ($actualOunces < 1 && $actualOunces > 0) {
            $actualOunces = 1;
        }
        $ounces = round($actualOunces, 2);

        return array((string) $lbs, (string) $ounces);
    }

    public function xmlPrettyPrint($xml, $tryAgain = false)
    {
        if (empty($xml)) {
            return $xml;
        }
        $originalXml = $xml;

        if ($tryAgain) {
            $xml = preg_replace('/<\?xml .*\?>/', '', $xml);
            $xml = '<root>'.$xml.'</root>';
        }
        $previous_value = libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->strictErrorChecking = false;
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $status = $doc->loadXML($xml, LIBXML_NOWARNING);
        $formatted_xml = $doc->saveXML();
        libxml_clear_errors();
        libxml_use_internal_errors($previous_value);

        if ($status) {
            if ($tryAgain) {
                $formatted_xml = str_replace("<root>\n", '', $formatted_xml);
                $formatted_xml = str_replace("</root>\n", '', $formatted_xml);
                $formatted_xml = str_replace('<root>', '', $formatted_xml);
                $formatted_xml = str_replace('</root>', '', $formatted_xml);
            }

            return $formatted_xml;
        } else {
            if ($tryAgain == false) {
                return $this->xmlPrettyPrint($xml, true);
            }

            return $originalXml;
        }
    }

    /**
     * Create html code for base64 embedded image.
     *
     * This function will return valid html for an
     * embedded base64 image.  This html does not
     * work in all browsers.
     */
    public function label_html($base64EncodedLabel, $imageType)
    {
        return sprintf('<img src="data:image/%s;base64,%s" alt="Label" />',
            $imageType,
            $base64EncodedLabel
        );
    }
}
