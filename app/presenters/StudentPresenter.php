<?php
/**
 * Created by PhpStorm.
 * User: Greyhound
 * Date: 1.12.2017
 * Time: 17:28
 */
namespace App\Presenters;

use Nette;
use Nette\Application\UI;
use Nette\Database as ND;

class StudentPresenter extends Nette\Application\UI\Presenter
{
    /** @var Nette\Database\Context */
    protected $database;

    /** @var \App\Model\Factories\SearchFormFactory @inject */
    public $searchFormFactory;

    protected $formFilter = "";
    protected $filterSet = false;

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
            $this->formFilter = $values;
            $this->filterSet = true;
        }
    }

    public function renderCourses()
    {
        if (!$this->filterSet) {
            $this->template->posts = $this->database->query(
                "SELECT Predmet.zkratka, Predmet.nazev, Predmet.id_pr FROM
                StudentPredmet NATURAL JOIN Predmet
                WHERE StudentPredmet.id_st = ".$this->user->getId())->fetchAll();
        }
        else{
        $this->template->posts = $this->database->query(
            "SELECT Predmet.nazev, Predmet.id_pr FROM
            StudentPredmet NATURAL JOIN Predmet
            WHERE StudentPredmet.id_st = ".$this->user->getId()." AND (Predmet.nazev = ".$this->formFilter.")")->fetchAll();
        }

    }

    public function renderCourseDetail($id_pr)
    {
        if (!$this->filterSet) {
            $this->template->posts = $this->database->query(
                "SELECT id_zk, Zkouska.nazev, Zkouska.datum, Zkouska.cas, Zkouska.termin_cislo, Termin.p_dosaz_bodu, Termin.max_bodu, Termin.min_bodu, Termin.stav_zkousky, Termin.dat_ohodnoceni, Termin.komentar FROM
                Termin NATURAL JOIN Zkouska
                WHERE Termin.id_st = ".$this->user->getId()." AND Zkouska.id_pr = ".$id_pr.
                " ORDER BY Zkouska.datum"
            )->fetchAll();
        }
        else{
            $this->template->posts = $this->database->query(
                "SELECT id_zk, Zkouska.nazev, Zkouska.datum, Zkouska.cas, Zkouska.termin_cislo, Termin.p_dosaz_bodu,Termin.max_bodu, Termin.min_bodu, Termin.stav_zkousky, Termin.dat_ohodnoceni, Termin.komentar FROM
                Termin NATURAL JOIN Zkouska
                WHERE Termin.id_st = ".$this->user->getId()." AND Zkouska.id_pr = ".$id_pr." AND 
                (Zkouska.nazev = ".$this->formFilter."OR Zkouska.datum = ".$this->formFilter." OR Zkouska.cas = ".$this->formFilter." OR Zkouska.termin_cislo = ".$this->formFilter.
                " ORDER BY Zkouska.datum)"
            )->fetchAll();
        }
    }

    public function renderTerms()
    {
        if (!$this->filterSet) {
            $this->template->posts = $this->database->query(
                "SELECT id_zk, Predmet.nazev, Predmet.zkratka, Zkouska.jmeno, Zkouska.datum, Zkouska.cas, Zkouska.termin_cislo, Termin.p_dosaz_bodu, Termin.p_dosaz_bodu, Zkouska.max_bodu, Zkouska.min_bodu, Termin.stav_zkousky, Termin.dat_ohodnoceni, Termin.komentar FROM
                Termin NATURAL JOIN Zkouska NATURAL JOIN Predmet
                WHERE Termin.id_st = ".$this->user->getId().
                " ORDER BY Zkouska.datum"
            )->fetchAll();
        }
        else{
            $this->template->posts = $this->database->query(
                "SELECT id_zk, Predmet.nazev, Predmet.zkratka, Zkouska.jmeno, Zkouska.datum, Zkouska.cas, Zkouska.termin_cislo, Termin.p_dosaz_bodu, Termin.p_dosaz_bodu, Zkouska.max_bodu, Zkouska.min_bodu, Termin.stav_zkousky, Termin.dat_ohodnoceni, Termin.komentar FROM
                Termin NATURAL JOIN Zkouska NATURAL JOIN Predmet
                WHERE Termin.id_st = ".$this->user->getId()." AND 
                (Predmet.zkratka = ".$this->formFilter."OR Zkouska.jmeno = ".$this->formFilter."OR Zkouska.datum = ".$this->formFilter." OR Zkouska.termin_cislo = ".$this->formFilter.")".
                " ORDER BY Zkouska.datum"
            )->fetchAll();
        }
    }

    public function renderQuestions($id_te)
    {
        if (!$this->filterSet) {
            $this->template->posts = $this->database->query(
                "SELECT nazev, pocet_bodu FROM
                Otazka
                WHERE Otazka.id_te = ".$id_te
            )->fetchAll();
        }
        else{
            $this->template->posts = $this->database->query(
                "SELECT nazev, pocet_bodu FROM
                Otazka
                WHERE Otazka.id_te = ".$id_te. "AND
                (Otazka.nazev = ".$this->formFilter."OR Otazka.pocet_bodu = ".$this->formFilter.")"
            )->fetchAll();
        }
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