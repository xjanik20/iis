<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI;


class HomepagePresenter extends Nette\Application\UI\Presenter
{
    protected function createComponentLoginForm()
    {
        $form = new UI\Form;
        $form->addText('name', 'Name:');
        $form->addPassword('password', 'Password:');
        $form->addSubmit('login', 'Sign up');
        $form->onSuccess[] = [$this, 'loginFormSucceeded'];
        return $form;
    }

    // called after form is successfully submitted
    public function loginFormSucceeded(UI\Form $form, $values)
    {


        // ...
        $this->flashMessage('You have successfully signed up.');
        $this->redirect('Homepage:');

    }
}
