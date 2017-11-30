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
        $this->template->posts = $this->database->table('Student');
        if (!$this->template->posts) {
            $this->error('Stránka nebyla nalezena');
        }
    }

    public function renderTeachers()
    {
        $this->template->posts = $this->database->table('Ucitel');
        if (!$this->template->posts) {
            $this->error('Stránka nebyla nalezena');
        }
    }

    public function actionDeleteRow($id)
    {
        $res = $this->database->table('Student')->where('id_st', $id)->delete();
        if (!$res) {
            $this->error('Záznam se nepodařilo smazat');
        }
        $this->redirect('students');
    }
}