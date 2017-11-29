<?php
Namespace App\AccessControl;
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
        $row = $this->database->table('users')
            ->where('username', $username)->fetch();
        if(!row){
            throw new NS\AuthenticationException('User not found.');
        }
        if (NS\Passwords::verify($password, $row->password)) {
            throw new NS\AuthenticationException('Invalid password.');
        }
        return new NS\Identity($row->id,$row->role, ['username' => $username]);
    }
}
