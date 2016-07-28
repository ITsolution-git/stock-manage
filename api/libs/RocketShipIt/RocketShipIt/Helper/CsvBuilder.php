<?php

namespace RocketShipIt\Helper;

class CsvBuilder
{

    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    // Generate CSV data from array
    public function toString()
    {
        // Open in-memory file
        $fh = fopen('php://temp', 'rw');

        // write out the headers
        fputcsv($fh, array_keys(current($this->data)));

        // write out the data
        foreach ($this->data as $row) {
            fputcsv($fh, $row);
        }
        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        return $csv;
    }
}
