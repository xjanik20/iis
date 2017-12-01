<?php
/**
 * Created by PhpStorm.
 * User: Greyhound
 * Date: 1.12.2017
 * Time: 17:28
 */

use Nette;
use Nette\Application\UI;
use Nette\Database as ND;

class StudentPresenter extends Nette\Application\UI\Presenter
{
    /** @var Nette\Database\Context */
    private $database;

    /** @var Nette\Database\Table\Selection */
    private $searchResult = null;

    /** @var \App\Model\Factories\SearchFormFactory @inject */
    public $searchFormFactory;

    private $formFilter = false;

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
        if ($values['filter']) {
            if ($this->getAction() == ('tests')) {
                $this->searchResult = $this->database->table('Zkouska')->
                where('jmeno = ? OR id_pr.nazev = ? OR id_te.datum', $values['filter'], $values['filter'], $values['filter']);
            } elseif ($this->getAction() == ('terms')) {
                $this->searchResult = $this->database->table('Termin')->
                where('datum = ? OR stav_zkousky = ?', $values['filter'], $values['filter']);
            } elseif ($this->getAction() == ('questions')) {
                $this->searchResult = $this->database->table('Otazka')->
                where('nazev = ?', $values['filter']);
            }
            $this->formFilter = true;
        }
    }

    public function renderTests()
    {
        if (!$this->formFilter) {
            $this->searchResult = $this->database->table('Zkouska');
        }
        $this->template->posts = $this->searchResult->fetchAll();
    }

    public function renderTerms($id_zk)
    {
        if (!$this->formFilter) {
            $this->searchResult = $this->database->table('Termin');
        }
        $this->template->posts = $this->searchResult->where('id_zk = ?', $id_zk)->fetchAll();
    }

    public function renderQuestions($id_te)
    {
        if (!$this->formFilter) {
            $this->searchResult = $this->database->table('Otazka');
        }
        $this->template->posts = $this->searchResult->where('id_te= ?', $id_te)->fetchAll();
    }


    public function ActionSignup($id_te, $id_zk)
    {
        $row = $this->database->table('termin')->where('id_te = ? AND stav_zkousky = ?', $id_te, 1)->fetch();
        if ($row) {
            $this->database->table('termin')->where('id_te')->update(['stav_zkousky' => '2']);
            $this->flashMessage("Termín přihlášen");
        }
        else{$this->flashMessage("Chyba, přihlášení se nezdařilo");}
        $this->redirect('student:terms', $id_zk);
    }

    public function ActionSignoff($id_te, $id_zk)
    {
        $row = $this->database->table('termin')->where('id_te = ? AND stav_zkousky = ?', $id_te, 2)->fetch();
        if ($row) {
            $this->database->table('termin')->where('id_te')->update(['stav_zkousky' => '1']);
            $this->flashMessage("Termín odhlášen");
        }
        else{$this->flashMessage("Chyba, odhlášení se nezdařilo");}
        $this->redirect('student:terms', $id_zk);
    }

}