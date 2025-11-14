<?php

class UserPdo
{
    private $id;
    public $login;
    public $email;
    public $firstname;
    public $lastname;

    static $db;

    /**
     * Le constructeur reçoit l'objet PDO
     * et le stocke dans l'attribut $db
     */
    public function __construct($login = null, $email = null, $firstname = null, $lastname = null)
    {
        $this->login = $login;
        $this->email = $email;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->db_connect();
    }

    public function db_connect()
    {
        // Configuration de la connexion à la base de données
        $host = 'localhost';
        $dbname = 'classes';
        $username = 'root';
        $password = '';  // Mot de passe vide par défaut avec Laragon

        // Création de la connexion MySQLi
        $db = new mysqli($host, $username, $password, $dbname);

        // Vérification de la connexion
        if ($db->connect_error) {
            die("Erreur de connexion : " . $db->connect_error);
        }

        // Configuration du charset
        $db->set_charset("utf8mb4");

        self::$db = $db;
    }
    //METHODE CRUD

    public function register($login, $password, $email, $firstname, $lastname)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO utilisateurs (login, password, email, firstname, lastname)
               VALUES (?, ?, ?, ?, ?)";

        $stmt = self::$db->prepare($sql);
        $stmt->bind_param("sssss", $login, $hashedPassword, $email, $firstname, $lastname);
        $stmt->execute();
        $stmt->close();

        return [
            'login' => $login,
            'email' => $email,
            'firstname' => $firstname,
            'lastname' => $lastname,
        ];
    }

    public function connect($login, $password)
    {
        $sql = "SELECT * FROM utilisateurs WHERE login = ?";
        $stmt = self::$db->prepare($sql);
        $stmt->bind_param("s", $login);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

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
        $sql = "DELETE FROM utilisateurs WHERE id = ?";

        $stmt = self::$db->prepare($sql);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $stmt->close();

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

        $sql = "UPDATE utilisateurs SET login = ?, password = ?, email = ?, firstname = ?, lastname = ? WHERE id = ?";

        $stmt = self::$db->prepare($sql);
        $stmt->bind_param("sssssi", $login, $hashedPassword, $email, $firstname, $lastname, $this->id);
        $stmt->execute();
        $stmt->close();

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
    public function getId()
    {
        return $this->id;
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
