<?php
Namespace App\Model\Factories;
use Nette\Application\UI;

class SearchFormFactory
{
    /** @return UI\Form */
    public static function create()
    {
        $form = new UI\Form;
        $form->addText('filter', 'Filter:');
        $form->addSubmit('search', 'Hledej');
        return $form;
    }
}