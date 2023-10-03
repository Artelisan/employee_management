<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Components\Employee\FormFactory;
use App\Model\EmployeeManager;
use Exception;
use Nette\Application\AbortException;
use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use stdClass;
use Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;

class EmployeePresenter extends Presenter
{
    public function __construct(
        private readonly EmployeeManager $employeeManager,
        private readonly FormFactory $employeeFormFactory,
    )
    {
        parent::__construct();
    }

    /** @throws BadRequestException */
    public function actionUpdate(string $id)
    {
        $employee = $this->employeeManager->getById($id);

        $this['employeeForm']->setDefaults($employee);
    }

    /** @throws AbortException|Exception */
    public function actionDelete(string $id): void
    {
        $this->employeeManager->delete(id: $id);

        $this->flashMessage('Successfully deleted', 'success');
        $this->redirect('Employee:default');
    }

    /** @throws DataGridException|Exception */
    public function createComponentEmployeesGrid($name): DataGrid
    {
        $grid = new DataGrid($this, $name);

        $grid->setDataSource($this->employeeManager->getAll());
        $grid->addColumnText('name', 'Name')
            ->setSortable();
        $grid->addColumnText('email', 'Email')
            ->setSortable();
        $grid->addColumnText('age', 'Age')
            ->setSortable();
        $grid->addAction('id', 'Edit', 'update')
            ->setClass('btn btn-xs btn-warning');
        $grid->addAction('delete', 'Delete')
            ->setClass('btn btn-xs btn-danger')
            ->setConfirmation(
                new StringConfirmation('Do you really want to delete employee %s?', 'name') // Second parameter is optional
            );
        $grid->addToolbarButton('create', 'Add Employee')
            ->setClass('btn btn-xs btn-success');

        $grid->setPagination(false);

        return $grid;
    }

    protected function createComponentEmployeeForm(): Form
    {
        $id = $this->getParameter('id');
        $form = $this->employeeFormFactory->create($id);

        $form->onSuccess[] = [$this, 'employeeFormSuccess'];

        return $form;
    }

    /** @throws BadRequestException|AbortException */
    public function employeeFormSuccess(Form $form, stdClass $values): void
    {
        $id = $this->getParameter('id');
        $valuesArray = json_decode(json_encode($values), true);

        $this->employeeManager->save($valuesArray, $id);

        $this->flashMessage($id ? 'Successfully updated' : 'Successfully created', 'success');
        $this->redirect('Employee:default');
    }

    public function actionChart()
    {
        $employees = $this->employeeManager->getAll();

        $ages = [];
        foreach ($employees as $employee) {
            $ages[$employee['age']] ??= 0;
            $ages[$employee['age']]++;
        }

        ksort($ages);
        $this->template->labels = array_keys($ages);
        $this->template->data = array_values($ages);
    }
}