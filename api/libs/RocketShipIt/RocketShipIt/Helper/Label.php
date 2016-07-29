<?php

namespace RocketShipIt\Helper;

class Label {

    public $label;

    public function __construct($label)
    {
        $decoded = base64_decode($label, true);
        if ($decoded) {
            $this->label = $decoded;
            return;
        }

        $this->label = $label;
    }

    public function adjustStartingPosition($x, $y)
    {
        $newStartingPosition = "^LH$x,$y";

        $this->label = preg_replace('/\^LH[^^]+/', $newStartingPosition, $this->label);
        $this->label = preg_replace('/\^XA\s*$/m', '^XA'. $newStartingPosition, $this->label);

        return $this->label;
    }

    public function createTextField($x, $y, $fontHeight, $fontWidth, $text)
    {
        return "^FO$x,$y^A0N,$fontHeight,$fontWidth^FWN^FH^FD$text^FS";
    }

    public function addText($x, $y, $fontHeight, $fontWidth, $text)
    {
        return $this->insertBeforeFirstLabelText(
            $this->createTextField($x, $y, $fontHeight, $fontWidth, $text). "\n"
        );
    }

    private function insertBeforeFirstLabelText($insert)
    {
        $str = $this->label;
        $search = '^FO';
        $index = strpos($str, $search);
        if ($index === false) {
            return $str;
        }
        return substr_replace($str, $insert.$search, $index, strlen($search));
    }
}
