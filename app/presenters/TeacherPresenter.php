<?php
/**
 * Created by PhpStorm.
 * User: Greyhound
 * Date: 2.12.2017
 * Time: 13:49
 */

namespace App\Presenters;

use Nette;
use Nette\Application\UI;
use Nette\Database as ND;

class TeacherPresenter extends Nette\Application\UI\Presenter
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
                UcitelPredmet NATURAL JOIN Predmet
                WHERE UcitelPredmet.id_uc = ?",
                $this->user->getId()
            )->fetchAll();
        }
        else{
            $this->template->posts = $this->database->query(
                "SELECT Predmet.nazev, Predmet.id_pr FROM
                UcitelPredmet NATURAL JOIN Predmet
                WHERE UcitelPredmet.id_uc = ? AND (Predmet.nazev = ? OR Predmet.zkratka = ? )",
                $this->user->getId(), $this->formFilter, $this->formFilter
            )->fetchAll();
        }

    }

    public function renderCourseDetail($id_pr)
    {
        if (!$this->filterSet) {
            $this->template->posts = $this->database->query(
                "SELECT id_te, id_zk, Zkouska.jmeno as jmeno_zkousky, Zkouska.datum, Zkouska.cas, Zkouska.termin_cislo, Zkouska.max_studentu, Zkouska.max_bodu, Zkouska.min_bodu FROM
                Zkouska NATURAL JOIN Predmet NATURAL JOIN UcitelPredmet
                WHERE UcitelPredmet.id_uc = ? AND id_pr = ? 
                ORDER BY Zkouska.datum",
                $this->user->getId(),$id_pr
            )->fetchAll();
        }
        else{
            $this->template->posts = $this->database->query(
                "SELECT id_te, id_zk, Zkouska.jmeno as jmeno_zkousky, Zkouska.datum, Zkouska.cas, Zkouska.termin_cislo, Zkouska.max_studentu, Zkouska.max_bodu, Zkouska.min_bodu FROM
                Zkouska NATURAL JOIN Predmet NATURAL JOIN UcitelPredmet
                WHERE UcitelPredmet.id_uc = ? AND id_pr = ? 
                ( Zkouska.nazev = ? OR Zkouska.datum = ? OR Zkouska.cas = ? OR Zkouska.termin_cislo = ? )
                ORDER BY Zkouska.datum",
                $this->user->getId(), $id_pr, $this->formFilter, $this->formFilter, $this->formFilter, $this->formFilter
            )->fetchAll();
        }
        $this->template->predmet = $this->database->query("Select zkratka, nazev FROM Predmet WHERE id_pr = ?",$id_pr)->fetch();
    }

}