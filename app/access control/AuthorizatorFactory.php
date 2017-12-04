<?php
Namespace App\Model\AccessControl;
use Nette;

class AuthorizatorFactory
{
    /**
     * @return Nette\Security\Permission
     */
    public static function create()
    {
        $acl = new  Nette\Security\Permission;
        $acl->addRole('guest');
        $acl->addRole('Student');
        $acl->addRole('Ucitel');
        $acl->addRole('Admin');
        $acl->addResource("Users");
        $acl->addResource("Courses");
        $acl->addResource("Exams");
        $acl->addResource("Terms");
        $acl->addResource("Marks");

        $acl->allow('Admin',Nette\Security\Permission::ALL, ['view','edit','add','delete']);
        $acl->allow('Ucitel',['Courses',"Exams","Terms","Marks"], ['view','edit','add','delete']);
        $acl->allow('Student', ['Courses',"Exams","Marks","Terms"], "view");
        $acl->allow('Student',"Terms", "edit");

        return $acl;
    }
}