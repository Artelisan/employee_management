<?php

namespace App\Service;

use DOMDocument;
use DOMException;
use SimpleXMLElement;

class XMLConnection
{
    public function __construct()
    {
    }

    /** @throws DOMException */
    public function connect(string $xmlFileName)
    {
        if (!file_exists($xmlFileName)) {
            (new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><employees></employees>'))->asXML($xmlFileName);
        }
        $xmlDoc = new DOMDocument('1.0', 'UTF-8');
        $xmlDoc->formatOutput = true;

        $root = $xmlDoc->createElement('employees');
        $xmlDoc->appendChild($root);
    }
}