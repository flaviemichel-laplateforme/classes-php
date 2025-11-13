<?php

class User
{
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;

    public function __construct($id, $login, $email, $firstname, $lastname)
    {
        $this->login = $login;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
    }
}
