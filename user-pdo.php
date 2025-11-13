<?php

class Userpdo
{
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;

    private $db;

    /**
     * Le constructeur reçoit l'objet PDO
     * et le stocke dans l'attribut $db
     */
    public function __construct($db, $id = null, $login = null, $email = null, $firstname = null, $lastname = null)
    {
        $this->db = $db;
    }

    //METHODE CRUD

    public function register($login, $password, $email, $firstname, $lastname)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO utilisateurs (login, password, email, firstname, lastname)
               VALUES (:login, :password, :email, :firstname, :lastname)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':login' => $login,
            ':password' => $hashedPassword,
            ':email' => $email,
            ':firstname' => $firstname,
            ':lastname' => $lastname
        ]);
        
        return [
            'login' => $login,
            'email' => $email,
            'firstname' => $firstname,
            'lastname' => $lastname,
        ];
    }

    public function connect($login, $password)
    {
    $sql = "SELECT * FROM utilisateurs WHERE login = :login";
    $stmt = $this->db->prepare($sql);

    $stmt->execute(
        [':login' => $login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
// Vérification n°1 : Avons-nous trouvé un utilisateur ? 
// Si le SELECT n'a rien trouvé (parce que le login n'existe pas), 
// que contiendra la variable $user ?
if ($user) {
    if (password_verify($password, $user['password'])){

        $this->id = $user['id'];
        $this->login = $user['login'];
        $this->email = $user['email'];
        $this->firstname = $user['firstname'];
        $this->lastname = $user['lastname'];
    
        return true;
        
    }else {
        echo "Mauvais mot de passe";
        return false;
    }
}

}
    }
    
}
