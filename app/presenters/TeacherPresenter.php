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
            $this->formFilter = $values['filter'];
            $this->filterSet = true;
        }
    }

    protected function createComponentEvaluationForm()
    {
        $form = new UI\Form;

        return $form;
    }

    protected function createComponentCreateExamForm()
    {
        $form = new UI\Form;
        $form->addText('jmeno', 'Jméno Zkoušky:')->setRequired('zadejte Jméno Zkoušky');
        $form->addInteger('termin_cislo', 'Termín:')->setRequired('zadejte termín')
            ->addRule(UI\Form::MIN, 'Maximální počet studentů musí být minimálně %d',1 );
        $form->addInteger('max_studentu', 'Maximální počet studentů:')->setRequired('zadejte login Maximální počet studentů')
            ->addRule(UI\Form::MIN, 'Maximální počet studentů musí být minimálně %d',1 );
        $form->addInteger('max_bodu', 'Maximální počet bodů:')->setRequired('zadejte jmeno Maximální počet bodů')
            ->addRule(UI\Form::MIN, 'Maximální počet bodů musí být minimálně %d',1 );
        $form->addInteger('min_bodu', 'Minimální počet bodů:')->setRequired('zadejte Minimální počet bodů')
            ->addRule(UI\Form::MIN, 'Minimální počet bodů musí být minimálně %d',0 );
        $form->addInteger('pocet_otázek', 'Počet otázek:')->setRequired('zadejte Počet otázek')
            ->addRule(UI\Form::MIN, 'Počet otázek musí být minimálně %d',1 );
        $form->addText('datum', 'Datum prvního termínu:')
            ->addRule(UI\Form::PATTERN, "Datum být ve tvaru \"RRRR-MM-DD\"","[0-9]{4}-[0-9]{2}-[0-9]{2}");
        $form->addText('cas', 'čas prvního termínu:')
            ->addRule(UI\Form::PATTERN, "Datum být ve tvaru \"HH:MM\"","[0-9]{2}:[0-9]{2}");
        $form->addSubmit('create', 'Vytvořit');

        $form->onSuccess[] = [$this, 'createExamFormSucceeded'];
        return $form;
    }

    public function createExamFormSucceeded(UI\Form $form, $values)
    {
        $id_zk = $this->database->table('Zkouska')->insert([
            "jmeno" => $values['jmeno'],
            "termin_cislo" => $values['termin'],
            "max_bodu" => $values['max_bodu'],
            "min_bodu" => $values['min_bodu'],
            "max_studentu" => $values['max_studentu'],
            "pocet_otazek" => $values['pocet_otazek'],
            "datum" => $values['datum'],
            "cas" => $values['cas']
        ])->getPrimary();
        $student_ids = $this->database->table('Student')->select("id_st")->fetchAll();
        foreach ($student_ids as $id_st){
            $id_te = $this->database->table('Termin')->insert([
                "stav_zkousky" => 0,
                "id_zk" => $id_zk,
                "id_st" => $id_st['id_st']
            ])->getPrimary();
            for ($i=1;$i<$values['pocet_otazek']+1;$i++){
                $this->database->table('Otazka')->insert([
                   "id_te" => $id_te['id_te'],
                   "pocet_bodu" => 0,
                   "nazev" => $i,
                ]);
            }
        }
        $this->flashMessage("Zkouska založena");
    }

    protected function createComponentEditExamForm()
    {
        $form = new UI\Form;
        $form->addHidden('id');
        $form->addText('jmeno', 'Jméno Zkoušky:')->setRequired('zadejte Jméno Zkoušky');
        $form->addInteger('termin_cislo', 'Termín:')->setRequired('zadejte termín')
            ->addRule(UI\Form::MIN, 'Maximální počet studentů musí být minimálně %d',1 );
        $form->addInteger('max_studentu', 'Maximální počet studentů:')->setRequired('zadejte login Maximální počet studentů')
            ->addRule(UI\Form::MIN, 'Maximální počet studentů musí být minimálně %d',1 );
        $form->addInteger('max_bodu', 'Maximální počet bodů:')->setRequired('zadejte jmeno Maximální počet bodů')
            ->addRule(UI\Form::MIN, 'Maximální počet bodů musí být minimálně %d',1 );
        $form->addInteger('min_bodu', 'Minimální počet bodů:')->setRequired('zadejte Minimální počet bodů')
            ->addRule(UI\Form::MIN, 'Minimální počet bodů musí být minimálně %d',0 );
        $form->addInteger('pocet_otázek', 'Počet otázek:')->setRequired('zadejte Počet otázek')
            ->addRule(UI\Form::MIN, 'Počet otázek musí být minimálně %d',1 );
        $form->addText('datum', 'Datum prvního termínu:')
            ->addRule(UI\Form::PATTERN, "Datum být ve tvaru \"RRRR-MM-DD\"","[0-9]{4}-[0-9]{2}-[0-9]{2}");
        $form->addText('cas', 'čas prvního termínu:')
            ->addRule(UI\Form::PATTERN, "Datum být ve tvaru \"HH:MM\"","[0-9]{2}:[0-9]{2}");
        $form->addSubmit('create', 'Vytvořit');

        $form->onSuccess[] = [$this, 'createExamFormSucceeded'];
        return $form;
    }

    public function editExamFormSucceeded(UI\Form $form, $values)
    {
        $row = $this->database->table('Zkouska')->where("id_zk = ?",$values['id'])->fetch();
        if (!$row){
            $this->flashMessage("Zkouska s daným ID neexistuje");
            $this->redirect('this');
        }
        else{
            $this->database->table('Zkouska')->where("id_zk = ?",$values['id'])->update([
                "jmeno" => $values['jmeno'],
                "termin_cislo" => $values['termin'],
                "max_bodu" => $values['max_bodu'],
                "min_bodu" => $values['min_bodu'],
                "max_studentu" => $values['max_studentu'],
                "pocet_otazek" => $values['pocet_otazek'],
                "datum" => $values['datum'],
                "cas" => $values['cas']
            ]);
            if($row->pocet_otazek != $values ['pocet_otazek']) {
                $termin_ids = $this->database->table('Termin')->select("id_te")->fetchAll();
                foreach ($termin_ids as $id_te) {
                    if($row->pocet_otazek < $values['pocet_otazek']){
                        for ($i = $row->pocet_otazek + 1; $i < $values['pocet_otazek'] + 1; $i++) {
                            $this->database->table('Otazka')->insert([
                                "id_te" => $id_te['id_te'],
                                "pocet_bodu" => 0,
                                "nazev" => $i,
                            ]);
                        }
                    }
                    else{
                        $this->database->table('Otazka')
                            ->where("id_te = ? AND nazev > ?",$id_te['id_te'],$values['pocet_otazek'])
                            ->delete();
                    }
                }
            }
            $this->flashMessage("Zkouska upravena");
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
                "SELECT Predmet.zkratka, Predmet.nazev, Predmet.id_pr FROM
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
                "SELECT id_zk, Zkouska.stav, Zkouska.jmeno as jmeno_zkousky, Zkouska.datum, Zkouska.cas, Zkouska.termin_cislo, Zkouska.max_studentu, Zkouska.max_bodu, Zkouska.min_bodu FROM
                Zkouska NATURAL JOIN Predmet NATURAL JOIN UcitelPredmet
                WHERE UcitelPredmet.id_uc = ? AND id_pr = ? 
                ORDER BY Zkouska.datum",
                $this->user->getId(),$id_pr
            )->fetchAll();
        }
        else {
            $this->template->posts = $this->database->query(
                "SELECT id_zk, Zkouska.stav, Zkouska.jmeno as jmeno_zkousky, Zkouska.datum, Zkouska.cas, Zkouska.termin_cislo, Zkouska.max_studentu, Zkouska.max_bodu, Zkouska.min_bodu FROM
                Zkouska NATURAL JOIN Predmet NATURAL JOIN UcitelPredmet
                WHERE UcitelPredmet.id_uc = ? AND id_pr = ?
                AND ( Zkouska.jmeno = ? OR Zkouska.datum = ? OR Zkouska.cas = ? OR Zkouska.termin_cislo = ? )
                ORDER BY Zkouska.datum",
                $this->user->getId(), $id_pr, $this->formFilter, $this->formFilter, $this->formFilter, $this->formFilter
            )->fetchAll();
        }

        $this->template->stavy = [
            0 => "Přihlašování otevřeno",
            1 => "Přihlašování zavřeno",
            2 => "Zkouška Proběhla a není opravena",
            3 => "Zkouška Proběhla a je opravena"];

        $this->template->info = $this->database->query("Select zkratka, nazev FROM Predmet WHERE id_pr = ?",$id_pr)->fetch();
    }

    public function renderExam($id_zk)
    {
        if (!$this->filterSet) {
            $this->template->posts = $this->database->query(
            "SELECT id_te, Student.login, Student.jmeno, Student.prijmeni, Termin.stav_zkousky
            FROM Zkouska NATURAL JOIN Termin NATURAL JOIN Student
            WHERE id_zk = ? AND (Termin.stav_zkousky = 2 OR Termin.stav_zkousky > 3)
            ORDER BY Termin.stav_zkousky"
            )->fetchAll();
        }
        else{
            $this->template->posts = $this->database->query(
                "SELECT id_te, Student.login, Student.jmeno, Student.prijmeni, Termin.stav_zkousky
            FROM Zkouska NATURAL JOIN Termin NATURAL JOIN Student
            WHERE id_zk = ? AND (Termin.stav_zkousky = 2 OR Termin.stav_zkousky > 3)
            AND ( Student.login = ? OR Student.jmeno = ? OR Student.prijmeni = ? OR Termin.stav_zkousky = ? )
            ORDER BY Termin.stav_zkousky"
            )->fetchAll();
        }
        $this->template->info = $this->database->query("Select Predmet.nazev,Predmet.zkratka, Zkouska.jmeno, Zkouska.stav, termin_cislo FROM Zkouska NATURAL JOIN Predmet WHERE id_zk = ?",$id_zk)->fetch();
    }

    public function renderQuestions($id_te)
    {
        if (!$this->filterSet) {
            $this->template->posts = $this->database->query(
                "SELECT nazev, pocet_bodu FROM
                Otazka
                WHERE Otazka.id_te = ?",
                $id_te
            )->fetchAll();
        }
        else{
            $this->template->posts = $this->database->query(
                "SELECT nazev, pocet_bodu FROM
                Otazka
                WHERE Otazka.id_te = ? AND
                ( Otazka.nazev = ? OR Otazka.pocet_bodu = ? )",
                $id_te, $this->formFilter, $this->formFilter
            )->fetchAll();
        }
        $this->template->info = $this->database->query("
            SELECT Student.login, Student.jmeno, Student.prijmeni, Predmet.zkratka, Predmet.nazev, Zkouska.Jmeno, Zkouska.temin_cislo FROM
            Termin NATURAL JOIN Zkouska NATURAL JOIN Predmet NATURAL JOIN Student
            WHERE id_te = ?",
            $id_te
        )->fetch();
    }

    public function actionDeleteExam($id_pr, $id_zk){
        $this->database->table('Zkouska')->where('id_zk = ?',$id_zk)->delete();
        $this->flashMessage("Zkouska smazána");
        $this->redirect('this',$id_pr);
    }

}