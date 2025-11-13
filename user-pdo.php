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
            [':login' => $login]
        );
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user['password'])) {

                $this->id = $user['id'];
                $this->login = $user['login'];
                $this->email = $user['email'];
                $this->firstname = $user['firstname'];
                $this->lastname = $user['lastname'];

                return true;
            } else {
                echo "Mauvais mot de passe";
                return false;
            }
        }
    }
    public function disconnect()
    {
        $this->id = null;
        $this->login = null;
        $this->email = null;
        $this->firstname = null;
        $this->lastname = null;
    }

    public function delete()
    {
        $sql = "DELETE FROM utilisateurs WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(
            [':id' => $this->id]
        );
        echo "Utilisateur supprimé";

        $this->id = null;
        $this->login = null;
        $this->email = null;
        $this->firstname = null;
        $this->lastname = null;
    }

    public function update($login, $password, $email, $firstname, $lastname)
    {
        $this->login = $login;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;



        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE  utilisateurs SET login = :login, password = :password , email = :email, firstname = :firstname, lastname = :lastname WHERE id = :id";


        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id' => $this->id,
            ':login' => $login,
            ':password' => $hashedPassword,
            ':email' => $email,
            ':firstname' => $firstname,
            ':lastname' => $lastname,
        ]);

        echo "Utilisateur mis à jour !";
    }

    public function isConnected()
    {
        if ($this->id != null) {
            return true;
        } else {
            return false;
        }
    }

    public function getAllInfos()
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'email' => $this->email,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
        ];
    }

    public function getLogin()
    {
        return $this->login;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }
}
