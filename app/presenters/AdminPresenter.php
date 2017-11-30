<?php

namespace App\Presenters;

use Nette;

class AdminPresenter extends Nette\Application\UI\Presenter
{
    /** @var Nette\Database\Context */
    private $database;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
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