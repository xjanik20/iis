<?php
Namespace App\Model\AccessControl;
use Nette\Security as NS ;
use Nette\Database as ND ;

class AuthorizatorFactory
{
    /** @return NS\Permission */
    public static function create()
    {
        $acl = new NS\Permission;
        $acl->addRole('guest');
        $acl->addRole('Student');
        $acl->addRole('Ucitel');
        $acl->addRole('Admin');
        return $acl;
    }
}