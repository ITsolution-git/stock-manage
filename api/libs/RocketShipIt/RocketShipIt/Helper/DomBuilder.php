<?php

namespace RocketShipIt\Helper;

class DomBuilder
{

    public $parameters = array();
    public $xml = '';
    public $outXml = '';
    public $tagMap = array();
    public $xpathMap = array();
    public $staticMap = array();
    public $staticXpathMap = array();

    public function __construct($xmlString)
    {
        $this->dom = new \DOMDocument;
        $this->dom->preserveWhiteSpace = false;
        $this->dom->loadXML($xmlString);
        $this->dom->formatOutput = true;
    }

    public function setTags($tag, $value)
    {
        $elements = $this->dom->getElementsByTagName($tag);
        foreach ($elements as $element) {
            $element->nodeValue = $value;
        }
    }

    public function removeTags($tag)
    {
        $elements = $this->dom->getElementsByTagName($tag);
        foreach ($elements as $element) {
            $element->parentNode->removeChild($element);
        }
    }

    public function removeBlank()
    {
        $xpath = new \DOMXPath($this->dom);

        foreach($xpath->query('//*[not(node())]') as $node) {
            $node->parentNode->removeChild($node);
        }

        if ($xpath->query('//*[not(node())]')->length > 0) {
            $this->removeBlank($this->dom);
        }
    }

    public function setTagWithXpath($query, $value)
    {
        $xpath = new \DOMXPath($this->dom);

        foreach($xpath->query($query) as $node) {
            $node->nodeValue = $value;
        }
    }

    public function insertDomBefore($placeHolderTag, $newDom)
    {
        $node = $newDom->documentElement;
        $node = $this->dom->importNode($node, true);
        $elements = $this->dom->getElementsByTagName($placeHolderTag);
        foreach ($elements as $refNode) {
            $this->dom->documentElement->insertBefore($node, $refNode);
        }
    }

    public function insertDomInto($query, $newDom)
    {
        $element = $newDom->documentElement;
        $newNode = $this->dom->importNode($element, true);
        $xpath = new \DOMXPath($this->dom);
        foreach ($xpath->query($query) as $oldNode) {
            // Replace node with our new dom
            $oldNode->parentNode->replaceChild($newNode, $oldNode);
        }
    }

    public function mapStaticTags($staticMap)
    {
        foreach ($staticMap as $tag => $value) {
            $this->setTags($tag, $value);
        }
    }

    public function mapXpaths($xpathMap)
    {
        foreach ($xpathMap as $param => $query) {
            if (!isset($this->parameters[$param])) {
                continue;
            }
            $this->setTagWithXpath($query, $this->parameters[$param]);
        }
    }

    public function mapStaticXpaths($staticXpathMap)
    {
        foreach ($staticXpathMap as $query => $value) {
            $this->setTagWithXpath($query, $value);
        }
    }

    public function mapTags($tagMap)
    {
        foreach ($tagMap as $param => $tag) {
            if (!isset($this->parameters[$param])) {
                continue;
            }
            $this->setTags($tag, $this->parameters[$param]);
        }
    }

    public function build()
    {
        $this->removeBlank();

        return $this->dom;
    }

    private function domToXmlString()
    {
        return $this->dom->saveXML($this->dom->documentElement);
    }

    public function getXml()
    {
        if ($this->outXml == '') {
            $this->build();
            $this->outXml = $this->domToXmlString();
        }

        return $this->outXml;
    }

}
