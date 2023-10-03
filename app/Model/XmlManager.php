<?php

namespace App\Model;

use App\Enum\EmployeeAttributes;
use DOMXPath;
use Exception;
use Nette\Http\IResponse;
use SimpleXMLElement;

class XmlManager
{
    public string $xmlFilePath = __DIR__ . '/../employees.xml';

    /** @throws Exception */
    public function add(array $values): void
    {
        $xml = $this->loadFileData();
        $employee = $xml->addChild('employee');

        foreach (EmployeeAttributes::cases() as $attribute)
            $employee[0]->addAttribute($attribute->value, $values[$attribute->value]);

        $xml->asXML($this->xmlFilePath);
    }

    /** @throws Exception */
    public function update(array $values, string $id): void
    {
        $xml = $this->loadFileData();
        $employee = $xml->xpath("//employee[@id='$id']");

        foreach (EmployeeAttributes::cases() as $attribute) {
            $employee[0][$attribute->value] = $values[$attribute->value];
        }

        $xml->asXML($this->xmlFilePath);
    }

    /** @throws Exception */
    public function delete(string $id): void
    {
        $xml = $this->loadFileData();
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $xpath = new DOMXPath($dom);
        $employee = $xpath->query("//employee[@id='$id']")[0];

        $employee->parentNode->removeChild($employee);

        $dom->save($this->xmlFilePath);
    }

    /**
     * @throws Exception
     * @return false|SimpleXMLElement[]|null
     */
    public function getSingleResult(string $id): false|array|null
    {
        return $this->loadFileData()->xpath("//employee[@id='$id']");
    }

    /** @throws Exception */
    public function loadFileData(): SimpleXMLElement|bool
    {
        return simplexml_load_file($this->xmlFilePath, null , LIBXML_NOCDATA) ?: throw new Exception('Data file not found', IResponse::S500_InternalServerError);
    }
}