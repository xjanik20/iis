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
        if(!$this->user->isallowed("Users","view")) $this->error("Permission denied",403);

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
        if(!$this->user->isallowed("Users","add")) $this->error("Permission denied",403);
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
        $form->addText('id');
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
        if(!$this->user->isallowed("Users","edit")) $this->error("Permission denied",403);
        $table = "";
        $idcolumn = "";
        if ($this->getAction()==('students')) {$table = 'Student'; $idcolumn = 'id_st';}
        elseif ($this->getAction()==('teachers')) {$table = 'Ucitel'; $idcolumn = 'id_uc';}
        else {$this->flashMessage("Something went wrong");}
        $row = $this->database->query("SELECT * FROM ?name WHERE ?name = ?", $table, $idcolumn, $values['id'])->fetch();
        if (!$row){
            $this->flashMessage("Uživatel s uvedeným id nenalezen");
            $this->redirect('this');
        }
        elseif ( $row->login != $values['login'] && ($this->database->table('Student')->where('login = ?',$values['login'])->fetch() ||
            $this->database->table('Ucitel')->where('login = ?',$values['login'])->fetch() ||
            $this->database->table('Admin')->where('login = ?',$values['login'])->fetch())){
            $this->flashMessage("Login již existuje");
            $this->redirect('this');
        }
        else{
            $this->database->query(
                "UPDATE ?name SET 
                login=? , jmeno=? , prijmeni=? , heslo=? 
                WHERE ?name = ?",
                $table, $values['login'], $values['jmeno'], $values['prijmeni'], $values['heslo'],
                $idcolumn, $values['id']
            );
            $this->flashMessage("Uživatel editován");
        }

    }

    public function renderStudents()
    {
        if(!$this->user->isallowed("Users","view")) $this->error("Permission denied",403);
        if(!$this->formFilter){
            $this->template->posts = $this->database->table('Student')->fetchAll();
        }
        else{
            $this->template->posts = $this->searchResult->fetchAll();
        }
    }

    public function renderTeachers()
    {
        if(!$this->user->isallowed("Users","view")) $this->error("Permission denied",403);
        if(!$this->formFilter){
            $this->template->posts = $this->database->table('Ucitel')->fetchAll();
        }
        else{
            $this->template->posts = $this->searchResult->fetchAll();
        }
    }

    public function actionDeleteRow($table, $login)
    {
        if(!$this->user->isallowed("Users","delete")) $this->error("Permission denied",403);
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