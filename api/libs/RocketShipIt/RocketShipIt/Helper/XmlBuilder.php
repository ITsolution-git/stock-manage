<?php

namespace RocketShipIt\Helper;

// Simon Willison, 16th April 2003
// Based on Lars Marius Garshol's Python XMLWriter class
// See http://www.xml.com/pub/a/2003/04/09/py-xml.html

/**
* Class used to build xml internally
*/
class XmlBuilder
{

    var $xml;
    var $indent;
    var $stack = array();

    function __construct($subordinateSection=false) {
        //$this->indent = getGenericDefault("debugMode") == 1 ? '   ' : '';   // indent if debugging
        $this->indent = '   ';   // indent
        if (!$subordinateSection) {
            $this->xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
        }
    }

    function indent() {
        for ($i = 0, $j = count($this->stack); $i < $j; $i++) {
            $this->xml .= $this->indent;
        }
    }

    function push($element, $attributes = array()) {
        $this->indent();
        $this->xml .= '<'.$element;
        foreach ($attributes as $key => $value) {
            $method = new \ReflectionFunction('htmlspecialchars');
            $num_params = $method->getNumberOfParameters();
            if ($num_params == 4) {
                $this->xml .= ' '.$key.'="'.htmlspecialchars($value, ENT_NOQUOTES,'ISO-8859-1', false).'"';
            } else {
                $this->xml .= ' '.$key.'="'.htmlspecialchars($value, ENT_NOQUOTES,'ISO-8859-1').'"';
            }
        }
        $this->xml .= ">\n";
        $this->stack[] = $element;
    }

    function element($element, $content, $attributes = array()) {
        if ($content != '') {
            $this->indent();
            $this->xml .= '<'.$element;
            foreach ($attributes as $key => $value) {
                $method = new ReflectionFunction('htmlspecialchars');
                $num_params = $method->getNumberOfParameters();
                if ($num_params == 4) {
                    $this->xml .= ' '.$key.'="'.htmlspecialchars($value, ENT_NOQUOTES, 'ISO-8859-1', false).'"';
                } else {
                    $this->xml .= ' '.$key.'="'.htmlspecialchars($value, ENT_NOQUOTES, 'ISO-8859-1').'"';
                }
            }
            $method = new \ReflectionFunction('htmlspecialchars');
            $num_params = $method->getNumberOfParameters();
            if ($num_params == 4) {
                $this->xml .= '>'.htmlspecialchars($content, ENT_NOQUOTES, 'ISO-8859-1', false).'</'.$element.'>'."\n";
            } else {
                $this->xml .= '>'.htmlspecialchars($content, ENT_NOQUOTES, 'ISO-8859-1').'</'.$element.'>'."\n";
            }
        }
    }

    function emptyelement($element, $attributes = array()) {
        $this->indent();
        $this->xml .= '<'.$element;
        foreach ($attributes as $key => $value) {
            $method = new ReflectionFunction('htmlspecialchars');
            $num_params = $method->getNumberOfParameters();
            if ($num_params == 4) {
                $this->xml .= ' '.$key.'="'.htmlspecialchars($value, ENT_NOQUOTES, 'ISO-8859-1', false).'"';
            } else {
                $this->xml .= ' '.$key.'="'.htmlspecialchars($value, ENT_NOQUOTES, 'ISO-8859-1').'"';
            }
        }
        $this->xml .= " />\n";
    }

    function pop() {
        $element = array_pop($this->stack);
        $this->indent();
        $this->xml .= "</$element>\n";
    }

    // Added MS 03-31-2011
    function append($xmlString) {
        $this->xml .= "$xmlString";
    }

    function getXml() {
        return $this->xml;
    }
}
