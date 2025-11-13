<?php

class User
{
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;
    public $password;

    private $db;
    public function __construct($db, $id = null, $login = null, $email = null, $firstname = null, $lastname = null, $password = null)
    {
        $this->db = $db;
    }

    //CRUD

    public function registrer($login, $password, $email, $firstname, $lastname) {}
}
