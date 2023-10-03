<?php

namespace App\Components\Employee;

use App\Enum\EmployeeAttributes;
use Contributte\FormsBootstrap\BootstrapForm;

class FormFactory
{
    public function create(?string $id = null): BootstrapForm
    {
        $form = new BootstrapForm();

        $form->addText(EmployeeAttributes::NAME->value, 'Full Name')
            ->setPlaceholder('Full name')
            ->setRequired()
            ->getLabelPrototype()->setAttribute('class', 'd-none');
        $form->addEmail(EmployeeAttributes::EMAIL->value, 'Email Address')
            ->setPlaceholder('Email')
            ->setRequired()
            ->getLabelPrototype()->setAttribute('class', 'd-none');
        $form->addInteger(EmployeeAttributes::AGE->value, 'Age')
            ->setPlaceholder('Age')
            ->setRequired()
            ->getLabelPrototype()->setAttribute('class', 'd-none');

        $form->addSubmit('submit', $id ? 'Update Employee' : 'Add Employee')
            ->setHtmlAttribute('class', 'btn btn-primary btn-lg btn-block');

        return $form;
    }
}