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
            $this->formFilter = $values['filter'];
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
                "SELECT id_zk, Zkouska.jmeno as jmeno_zkousky, Zkouska.datum, Zkouska.cas, Zkouska.termin_cislo, Termin.p_dosaz_bodu, Zkouska.max_bodu, Zkouska.min_bodu, Termin.stav_zkousky, Termin.dat_ohodnoceni, Termin.komentar, Ucitel.login, Ucitel.jmeno AS jmeno_ucitele, Ucitel.prijmeni FROM
                Termin NATURAL JOIN Zkouska LEFT JOIN Ucitel ON Termin.id_uc = Ucitel.id_uc
                WHERE Termin.id_st = ".$this->user->getId()." AND Zkouska.id_pr = ".$id_pr.
                " ORDER BY Zkouska.datum"
            )->fetchAll();
        }
        else{
            $this->template->posts = $this->database->query(
                "SELECT id_zk, Zkouska.jmeno as jmeno_zkousky, Zkouska.datum, Zkouska.cas, Zkouska.termin_cislo, Termin.p_dosaz_bodu, Zkouska.max_bodu, Zkouska.min_bodu, Termin.stav_zkousky, Termin.dat_ohodnoceni, Termin.komentar, Ucitel.login, Ucitel.jmeno AS jmeno_ucitele, Ucitel.prijmeni FROM
                Termin NATURAL JOIN Zkouska LEFT JOIN Ucitel ON Termin.id_uc = Ucitel.id_uc ON Termin.id_uc = Ucitel.id_uc
                WHERE Termin.id_st = ".$this->user->getId()." AND Zkouska.id_pr = ".$id_pr." AND 
                (Zkouska.nazev = ".$this->formFilter." OR Zkouska.datum = ".$this->formFilter." OR Zkouska.cas = ".$this->formFilter." OR Zkouska.termin_cislo = ".$this->formFilter.")".
                " ORDER BY Zkouska.datum"
            )->fetchAll();
        }
        $this->template->stavy = [
            0 => "přihlášení ještě není otevřeno",
            1 => "nepřihlášen",
            2 => "přihlášen",
            3 => "nepřihlášen, přihlašování zavřeno",
            4 => "nepřihlášen, přihlašování zavřeno",
            5 => "zkouška absolvována",
            6 => "zkouška opravena"];

        foreach($this->template->posts as $post){
            if ($post->login == NULL){
                $post->login = "Nehodnoceno";
                $post->jmeno_ucitele = "";
                $post->prijmeni = "";
            }
        }
    }

    public function renderTerms()
    {
        if (!$this->filterSet) {
            $this->template->posts = $this->database->query(
                "SELECT id_zk, Predmet.nazev, Predmet.zkratka, Zkouska.jmeno as jmeno_zkousky, Zkouska.datum, Zkouska.cas, Zkouska.termin_cislo, Termin.p_dosaz_bodu, Zkouska.max_bodu, Zkouska.min_bodu, Termin.stav_zkousky, Termin.dat_ohodnoceni, Termin.komentar, Ucitel.login, Ucitel.jmeno AS jmeno_ucitele, Ucitel.prijmeni FROM
                Termin NATURAL JOIN Zkouska NATURAL JOIN Predmet LEFT JOIN Ucitel ON Termin.id_uc = Ucitel.id_uc
                WHERE Termin.id_st = ".$this->user->getId().
                " ORDER BY Zkouska.datum"
            )->fetchAll();
        }
        else{
            $this->template->posts = $this->database->query(
                "SELECT id_zk, Predmet.nazev, Predmet.zkratka, Zkouska.jmeno as jmeno_zkousky, Zkouska.datum, Zkouska.cas, Zkouska.termin_cislo, Termin.p_dosaz_bodu, Zkouska.max_bodu, Zkouska.min_bodu, Termin.stav_zkousky, Termin.dat_ohodnoceni, Termin.komentar, Ucitel.login, Ucitel.jmeno AS jmeno_ucitele, Ucitel.prijmeni FROM
                Termin NATURAL JOIN Zkouska NATURAL JOIN Predmet LEFT JOIN Ucitel ON Termin.id_uc = Ucitel.id_uc
                WHERE Termin.id_st = ".$this->user->getId()." AND 
                (Predmet.zkratka = ".$this->formFilter." OR Zkouska.jmeno = ".$this->formFilter."OR Zkouska.datum = ".$this->formFilter." OR Zkouska.termin_cislo = ".$this->formFilter.")".
                " ORDER BY Zkouska.datum"
            )->fetchAll();
        }
        $this->template->stavy = [
            0 => "přihlášení ještě není otevřeno",
            1 => "nepřihlášen",
            2 => "přihlášen",
            3 => "nepřihlášen, přihlašování zavřeno",
            4 => "nepřihlášen, přihlašování zavřeno",
            5 => "zkouška absolvována",
            6 => "zkouška opravena"];

        foreach($this->template->posts as $post){
            if ($post->login == NULL) {
                $post->login = "Nehodnoceno";
                $post->jmeno_ucitele = "";
                $post->prijmeni = "";
            }
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
                (Otazka.nazev = ".$this->formFilter." OR Otazka.pocet_bodu = ".$this->formFilter.")"
            )->fetchAll();
        }
    }


    public function ActionSignup($id_te, $id_zk)
    {
        $zkouska = $this->database->table('Zkouska')->where('id_zk = ?',$id_zk)->fetch();
        $row = $this->database->table('termin')->where('id_te = ? AND stav_zkousky = ?', $id_te, 1)->fetch();
        if (!$row or !$zkouska) {
            $this->flashMessage("Chyba, přihlášení se nezdařilo");
        }
        elseif(
            $this->database->query(
            "SELECT COUNT(*) AS cnt FROM
                Zkouska NATURAL JOIN Termin
                GROUP BY Zkouska.nazev, Termin.stav_zkousky
                WHERE Zkouska.nazev = ".$zkouska->nazev." Termin.id_st = ".$row->id_st." AND (Termin.stav_zkousky > 3)
                HAVING COUNT(*) > 2"
            )->fetch()
        )
        {
            $this->flashMessage("Chyba: tři absolvované termíny");
        }
        elseif(
        $this->database->query(
            "SELECT * FROM
                Zkouska NATURAL JOIN Termin
                WHERE Zkouska.nazev = ".$zkouska->nazev." Termin.id_st = ".$row->id_st." AND (Termin.stav_zkousky = 2 OR Termin.stav_zkousky = 4)"
        )->fetch()
        ){
            $this->flashMessage("Chyba: jiný termín zkoušky přihlášen");
        }
        else{
            $this->database->table('termin')->where('id_te')->update(['stav_zkousky' => '2']);
            $this->flashMessage("Termín přihlášen");
        }

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