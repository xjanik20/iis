<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI;
use Nette\Database as ND;

class AdminPresenter extends Nette\Application\UI\Presenter
{
    /** @var Nette\Database\Context */
    private $database;
    private $formFilter = false;
    private $searchResult = null;

    /** @var \App\Model\Factories\SearchFormFactory @inject */
    public $searchFormFactory;

    public function __construct(Nette\Database\Context $database)
    {
        $this->database = $database;
    }

    protected function createComponentSearchForm()
    {
        $form = $this->searchFormFactory->create();
        $form->onSuccess[] = [$this, 'searchFormSucceeded'];
        return $form;
    }

    public function searchFormSucceeded(UI\Form $form, $values)
    {
        $searchResult = null;
        if ($this->getAction()==('students')) {$this->searchResult = $this->database->table('Student');}
        elseif ($this->getAction()==('teachers')) {$this->searchResult = $this->database->table('Ucitel');}
        else {$this->error('Stránka nebyla nalezena'); return;}

        if($values['login']) {$this->searchResult = $this->searchResult->where('login',$values['login']);}
        if($values['jmeno']) {$this->searchResult = $this->searchResult->where('jmeno',$values['jmeno']);}
        if($values['prijmeni']) {$this->searchResult = $this->searchResult->where('prijmeni',$values['prijmeni']);}
        $this->formFilter = true ;
    }

    public function renderStudents()
    {
        if(!$this->formFilter){
            $this->template->posts = $this->database->table('Student')->fetchAll();
        }
        else{
            $this->template->posts = $this->searchResult->fetchAll();
        }
        if (!$this->template->posts) {
            $this->error('Stránka nebyla nalezena');
        }
    }

    public function renderTeachers()
    {
        if(!$this->formFilter){
            $this->template->posts = $this->database->table('Ucitel')->fetchAll();
        }
        else{
            $this->template->posts = $this->searchResult->fetchAll();
        }
        if (!$this->template->posts) {
            $this->error('Stránka nebyla nalezena');
        }
    }

    public function actionDeleteRow($id, $login)
    {
        $res = $this->database->table('Student')->where('id_st', $id)->delete();
        if (!$res) {
            $this->error('Záznam se nepodařilo smazat');
        }
        else {
            $this->flashMessage("Student '$login' odebrán ze systému");
        }
        $this->redirect('students');
    }
}