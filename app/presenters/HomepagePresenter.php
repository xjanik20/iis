<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI;


class HomepagePresenter extends Nette\Application\UI\Presenter
{
    private $backlink;

    protected function beforeRender(){
        parent::beforeRender();
        $this->backlink = $this->storeRequest();
    }

    protected function createComponentLoginForm()
    {

        $form = new UI\Form;
        $form->addText('name', 'Name:')
            ->setRequired('Zadejte prosím login');
        $form->addPassword('password', 'Password:')
            ->setRequired('Zadejte Heslo')
            ->addRule(UI\Form::MIN_LENGTH, 'Heslo musí mít alespoň %d znaky', 4);
        $form->addSubmit('login', 'Sign up');

        $form->addProtection('Vypršel časový limit, odešlete formulář znovu');

        $form->onSuccess[] = [$this, 'loginFormSucceeded'];



        return $form;
    }

    // called after form is successfully submitted
    public function loginFormSucceeded(UI\Form $form, $values)
    {


        // ...
        try{
            $this->user->login($values['name'], $values['password']);
        }
        catch(Nette\Security\AuthenticationException $e){
            $this->flashMessage($e->getMessage());
        }
        $values['name'];
        if($this->user->isInRole('Admin')) {
            $this->redirect("Admin:students");
        }
        if($this->user->isInRole('Ucitel')) {
            $this->flashMessage("Ucitel login");
        }
        if($this->user->isInRole('Student')) {
            $this->flashMessage("Student login");
        }
        $this->restoreRequest($this->backlink);

    }
}
