<?php

namespace RocketShipIt\Types;

class Type
{
    public $parameters = array();

    public function setTags($dom, $tag, $value)
    {
        $elements = $dom->getElementsByTagName($tag);
        foreach ($elements as $element) {
            $element->nodeValue = $value;
        }
    }

    public function removeTags($dom, $tag)
    {
        $elements = $dom->getElementsByTagName($tag);
        foreach ($elements as $element) {
            $element->parentNode->removeChild($element);
        }
    }

    public function removeBlank($dom)
    {
        $xpath = new \DOMXPath($dom);

        foreach($xpath->query('//*[not(node())]') as $node) {
            $node->parentNode->removeChild($node);
        }

        if ($xpath->query('//*[not(node())]')->length > 0) {
            $this->removeBlank($dom);
        }
    }

    public function setTagWithXpath($dom, $query, $value)
    {
        $xpath = new \DOMXPath($dom);

        foreach($xpath->query($query) as $node) {
            $node->nodeValue = $value;
        }
    }

    public function insertDomBefore($dom, $placeHolderTag, $newDom)
    {
        $node = $newDom->documentElement;
        $node = $dom->importNode($node, true);
        $elements = $dom->getElementsByTagName($placeHolderTag);
        foreach ($elements as $refNode) {
            $dom->documentElement->insertBefore($node, $refNode);
        }
    }
}
