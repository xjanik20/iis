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
}