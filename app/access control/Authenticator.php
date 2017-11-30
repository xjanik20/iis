<?php
Namespace App\Model\AccessControl;
use Nette\Security as NS ;
use Nette\Database as ND ;

class LoginAuthenticator implements NS\IAuthenticator
{
    public $db;

    function __construct(ND\Context $db){
        $this->db = $db;
    }

    function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;
        $row = $this->db->table('Student')->where('login', $username)->fetch();
        if (row){
            if ($password == $row->heslo){
                return new NS\Identity($row->id_st,'Student', ['username' => $username]);
            }
        }

        $row = $this->db->table('Ucitel')->where('login', $username)->fetch();
        if (row){
            if ($password == $row->heslo){
                return new NS\Identity($row->id_uc,'Ucitel', ['username' => $username]);
            }
        }

        $row = $this->db->table('Admin')->where('login', $username)->fetch();
        if(!row){
            throw new NS\AuthenticationException('User not found.');
        }
        if ($password == $row->heslo) {
            return new NS\Identity($row->id_ad,'Admin', ['username' => $username]);
        }
        throw new NS\AuthenticationException('Invalid password.');
    }
}
