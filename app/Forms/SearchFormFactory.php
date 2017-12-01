<?php
Namespace App\Model\Factories;
use Nette\Application\UI;

class SearchFormFactory
{
    /** @return UI\Form */
    public static function create()
    {
        $form = new UI\Form;
        $form->addText('login', 'Login:');
        $form->addText('jmeno', 'Jméno:');
        $form->addText('prijmeni', 'Příjmení:');
        $form->addSubmit('search', 'Hledej');

        $form->addProtection('Vypršel časový limit, odešlete formulář znovu');
        return $form;
    }
}