<?php

namespace App\Presenters;

use Nette;
use Nette\Database as ND;

class AdminPresenter extends Nette\Application\UI\Presenter
{
    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    protected function createComponentSearchForm()
    {
        $form = App\Model\Factories\SearchformFacory::create();
        $form->onSuccess[] = [$this, 'searchFormSucceeded'];
    }

    protected function searchFormSucceeded(UI\Form $form, $values)
    {
        return;
    }

    public function renderStudents()
    {
        $this->template->post = $this->database->table('Student');
    }

    public function renderTeachers()
    {
        $this->template->post = $this->database->table('Ucitel');
    }
}