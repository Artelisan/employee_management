<?php

declare(strict_types=1);

namespace App\Model;

use Exception;
use Nette\Application\BadRequestException;
use Nette\SmartObject;
use SimpleXMLElement;

class EmployeeManager
{
    use SmartObject;

    public function __construct(
        private readonly XmlManager $xmlManager,
    )
    {
    }

    /** @throws Exception */
    public function getAll(): array
    {
        $data = [];
        foreach ($this->xmlManager->loadFileData() as $employee) {
            /** @var SimpleXMLElement $employee */
            $attributes = [];
            foreach ($employee->attributes() as $key => $value) {
                $attributes[$key] = (string)$value;
            }

            $data[] = $attributes;
        }

        return $data;
    }

    /** @throws BadRequestException|Exception */
    public function getById(string $id): ?array
    {
        $result = $this->xmlManager->getSingleResult($id);
        if (!$result) {
            throw new BadRequestException('Employee not found!');
        }

        return ((array) $result[0]->attributes())["@attributes"];
    }

    /** @throws Exception */
    public function findByEmail(string $email): array|false|null
    {
        return $this->xmlManager->loadFileData()->xpath("//employee[@email='$email']");
    }

    /** @throws BadRequestException|Exception */
    public function save(array $values = [], ?string $id = null): void
    {
        if ($id) {
            $values['id'] = $id;

            $this->xmlManager->update($values, $id);
        } else {
            if ($this->findByEmail($values['email'])) {
                throw new BadRequestException('Employee with this email already exist!');
            }

            $lastId = 0;
            foreach ($this->getAll() as $employee) {
                $employeeId = (int)$employee['id'];
                if ($employeeId > $lastId) {
                    $lastId = $employeeId;
                }
            }

            $values['id'] = $lastId + 1;

            $this->xmlManager->add($values);
        }
    }

    /** @throws Exception */
    public function delete(string $id): void
    {
        $this->xmlManager->delete($id);
    }
}