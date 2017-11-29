<?php
Namespace App\Model\AccessControl;
use Nette\Security as NS ;
use Nette\Database as ND ;

class LoginAuthenticator implements NS\IAuthenticator
{
    public $db;

    function __construct(ND\Connection $db){
        $this->db = $db;
    }

    function authenticate(array $credentials)
    {
        list($username, $password) = $credentials;
        $row = $this->database->table('Student')->where('login', $username)->fetch();
        if (row){
            if ($password == $row->$password){
                return new NS\Identity($row->id,'Student', ['username' => $username]);
            }
        }

        $row = $this->database->table('Ucitel')->where('login', $username)->fetch();
        if (row){
            if ($password == $row->$password){
                return new NS\Identity($row->id,'Ucitel', ['username' => $username]);
            }
        }

        $row = $this->database->table('Admin')->where('login', $username)->fetch();
        if(!row){
            throw new NS\AuthenticationException('User not found.');
        }
        if ($password == $row->$password) {
            return new NS\Identity($row->id,'Admin', ['username' => $username]);
        }
        throw new NS\AuthenticationException('Invalid password.');
    }
}
