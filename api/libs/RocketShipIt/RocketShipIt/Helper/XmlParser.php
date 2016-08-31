<?php

namespace RocketShipIt\Helper;

/**
* Used internally to parse xml into an array
*/
class XmlParser
{

    //Stores the object representation of XML data
    var $params = array();
    var $root = NULL;
    var $global_index = -1;
    var $fold = false;

    /* Constructor for the class
    * Takes in XML data as input( do not include the <xml> tag
    */
    function __construct($input='', $xmlParams=array(XML_OPTION_CASE_FOLDING => 0))
    {
        if ($input != '') {
            $this->load($input, $xmlParams);
        }
    }

    public function load($input, $xmlParams=array(XML_OPTION_CASE_FOLDING => 0))
    {
        // Short circuit if input is already an array or obj
        if (gettype($input) != 'string') {
            return $input;
        }

        // If not UTF-8, encode it
        // Why? mb_detect_encoding() is unreliable
        if (!preg_match('!!u', $input)) {
            $input = utf8_encode($input);
        }

        $xmlp = xml_parser_create();
        foreach($xmlParams as $opt => $optVal) {
                switch( $opt ) {
                case XML_OPTION_CASE_FOLDING:
                    $this->fold = $optVal;
                break;
                default:
                break;
                }
                xml_parser_set_option($xmlp, $opt, $optVal);
        }

        if (xml_parse_into_struct($xmlp, $input, $vals)) {
            $this->root = $this->foldCase($vals[0]['tag']);
            $this->params = $this->xml2ary($vals);
        } else {
            //echo xml_error_string(xml_get_error_code($xmlp));
        }
        xml_parser_free($xmlp);
    }
    
    function foldCase($arg)
    {
        return( $this->fold ? strtoupper($arg) : $arg);
    }

    function stripNamespace($tag)
    {
        $parts = explode(':', $tag);
        return end($parts);
    }

    function xml2ary($vals)
    {
        $mnary=array();
        $ary=&$mnary;
        foreach ($vals as $r) {
            $t = $this->stripNamespace($r['tag']);
            if ($r['type']=='open') {
                if (isset($ary[$t]) && !empty($ary[$t])) {
                    if (isset($ary[$t][0])){
                        $ary[$t][]=array(); 
                    } else {
                        $ary[$t]=array($ary[$t], array());
                    } 
                    $cv=&$ary[$t][count($ary[$t])-1];
                } else {
                    $cv=&$ary[$t];
                }
                $cv=array();
                if (isset($r['attributes'])) { 
                    foreach ($r['attributes'] as $k=>$v) {
                    $cv[$k]=$v;
                    }
                }
                
                $cv['_p']=&$ary;
                $ary=&$cv;

            } else if ($r['type'] == 'complete') {
                if (isset($ary[$t]) && !empty($ary[$t])) { // same as open
                    if (isset($ary[$t][0])) {
                        if (is_array($ary[$t])) {
                            $ary[$t][]=array();
                        } else {
                            $ary[$t] = array($ary[$t]);
                        }
                    } else {
                        $ary[$t]=array($ary[$t], array());
                    }
                    $cv=&$ary[$t][];
                } else {
                    $cv=&$ary[$t];
                } 
                if (isset($r['attributes'])) {
                    foreach ($r['attributes'] as $k => $v) {
                        $cv[$k] = $v;
                    }
                }
                //$cv['VALUE'] = (isset($r['value']) ? $r['value'] : '');
                $cv = (isset($r['value']) ? $r['value'] : '');
    
            } elseif ($r['type'] == 'close') {
                $ary = &$ary['_p'];
            }
        }
        
        $this->delP($mnary);

        return $mnary;
    }
    
    // _Internal: Remove recursion in result array
    function delP(&$ary)
    {
        foreach ($ary as $k => $v) {
            if ($k === '_p') {
                unset($ary[$k]);
            }
            else if(is_array($ary[$k])) {
                $this->delP($ary[$k]);
            }
        }
    }

    /* Returns the root of the XML data */
    function getRoot()
    {
        return $this->root; 
    }

    /* Returns the array representing the XML data */
    function getData()
    {
        return $this->params; 
    }
}
