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

    /** @var Nette\Database\Table\Selection */
    private $searchResult = null;

    /** @var \App\Model\Factories\SearchFormFactory @inject */
    public $searchFormFactory;

    public function __construct(Nette\Database\Context $database)
    {
        parent::__construct();
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
        if ($this->getAction()==('students')) {$this->searchResult = $this->database->table('Student');}
        elseif ($this->getAction()==('teachers')) {$this->searchResult = $this->database->table('Ucitel');}
        else {$this->error('Stránka nebyla nalezena'); return;}

        if($values['filter']) {
            $this->searchResult = $this->searchResult->where('login = ? OR jmeno = ? OR prijmeni = ?',$values['filter'],$values['filter'],$values['filter']);
        }

        $this->formFilter = true ;
    }

    protected function createComponentAddAccountForm()
    {
        $form = new UI\Form;
        $form->addText('login', 'Login:')->setRequired('zadejte login');
        $form->addText('jmeno', 'Jméno:')->setRequired('zadejte jmeno');
        $form->addText('prijmeni', 'Příjmení:')->setRequired('zadejte příjmení');
        $form->addText('heslo', 'Heslo:')->setRequired('zadejte heslo')
            ->addRule(UI\Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaky', 4);
        $form->addSubmit('create', 'Přidej');

        $form->onSuccess[] = [$this, 'addAccountFormSucceeded'];
        return $form;
    }

    public function addAccountFormSucceeded(UI\Form $form, $values)
    {
        $table = "";
        if ($this->getAction()==('students')) {$table = 'Student';}
        elseif ($this->getAction()==('teachers')) {$table = 'Ucitel';}

        if ($this->database->table('Student')->where('login',$values['login'])->fetch() ||
            $this->database->table('Ucitel')->where('login',$values['login'])->fetch() ||
            $this->database->table('Admin')->where('login',$values['login'])->fetch()){
            $this->flashMessage("Login již existuje");
            $this->redirect('this');
        }
        else{
            $this->database->table($table)->insert([
                "login" => $values['login'],
                "jmeno" => $values['jmeno'],
                "prijmeni" => $values['prijmeni'],
                "heslo" => $values['heslo']
            ]);
        }

    }

    protected function createComponentEditAccountForm()
    {
        $form = new UI\Form;
        $form->addHidden('id');
        $form->addText('login', 'Login:')->setRequired('zadejte login');
        $form->addText('jmeno', 'Jméno:')->setRequired('zadejte jmeno');
        $form->addText('prijmeni', 'Příjmení:')->setRequired('zadejte příjmení');
        $form->addText('heslo', 'Heslo:')->setRequired('zadejte heslo')
            ->addRule(UI\Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaky', 4);
        $form->addSubmit('edit', 'Editovat');

        $form->onSuccess[] = [$this, 'editAccountFormSucceeded'];
        return $form;
    }

    public function editAccountFormSucceeded(UI\Form $form, $values)
    {
        $table = "";
        $idcolumn = "";
        if ($this->getAction()==('students')) {$table = 'Student'; $idcolumn = 'id_st';}
        elseif ($this->getAction()==('teachers')) {$table = 'Ucitel';$idcolumn = 'id_uc';}

        if ($this->database->table('Student')->where('login',$values['login'])->fetch() ||
            $this->database->table('Ucitel')->where('login',$values['login'])->fetch() ||
            $this->database->table('Admin')->where('login',$values['login'])->fetch()){
            $this->flashMessage("Login již existuje");
            $this->redirect('this');
        }
        elseif (!$this->database->table($table)->where($idcolumn,$values['id'])->fetch()){
            $this->flashMessage("Uživatel s uvedeným id nenalezen");
            $this->redirect('this');
        }
        else{
            $this->database->table($table)->where("? = ?",$idcolumn,$values['id'])->update([
                "login" => $values['login'],
                "jmeno" => $values['jmeno'],
                "prijmeni" => $values['prijmeni'],
                "heslo" => $values['heslo']
            ]);
            $this->flashMessage("Uzivatel editován");
        }

    }

    public function renderStudents()
    {
        if(!$this->formFilter){
            $this->template->posts = $this->database->table('Student')->fetchAll();
        }
        else{
            $this->template->posts = $this->searchResult->fetchAll();
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
    }

    public function actionDeleteRow($table, $login)
    {
        $res = $this->database->table($table)->where('login', $login)->delete();
        if (!$res) {
            $this->error('Záznam se nepodařilo smazat');
        }
        else {
            $this->flashMessage(($table == "Student" ? "Student" : "Učitel") . " '$login' odebrán ze systému");
        }
        $this->redirect($table == "Student" ? 'students' : 'teachers');
    }
}