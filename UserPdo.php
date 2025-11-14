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
     * Échappe et nettoie les données utilisateur
     * @param string $value - La valeur à échapper
     * @return string - La valeur échappée
     */
    private function e($value)
    {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }


    /**
     * Le constructeur reçoit l'objet PDO
     * et le stocke dans la methode db_connect()
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
        $password = '';

        try {
            // Création de la connexion PDO
            $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

            // Configuration des options PDO
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            self::$db = $db;
        } catch (PDOException $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }
    }

    //METHODE CRUD

    public function register($login, $password, $email, $firstname, $lastname)
    {

        // AJOUTER : Validation de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email invalide");
        }

        // AJOUTER : Validation du mot de passe
        if (strlen($password) < 8) {
            throw new Exception("Le mot de passe doit contenir au moins 8 caractères");
        }

        // AJOUTER : Validation des champs vides
        if (empty($login) || empty($email) || empty($firstname) || empty($lastname)) {
            throw new Exception("Tous les champs sont obligatoires");
        }

        $login = $this->e($login);
        $email = $this->e($email);
        $firstname = $this->e($firstname);
        $lastname = $this->e($lastname);

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO utilisateurs (login, password, email, firstname, lastname)
               VALUES (:login, :password, :email, :firstname, :lastname)";

        $stmt = self::$db->prepare($sql);
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


    /**
     * Connecte l'utilisateur
     * @param $login, $password
     */
    public function connect($login, $password)
    {

        $login = $this->e($login);

        $sql = "SELECT * FROM utilisateurs WHERE login = :login";
        $stmt = self::$db->prepare($sql);

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
                throw new Exception("Mot de passe incorrect");
            }
        } else {
            throw new Exception("Utilisateur non trouvé");
        }
    }

    /**
     * Deconnecte l'utilisateur
     */
    public function disconnect()
    {
        $this->id = null;
        $this->login = null;
        $this->email = null;
        $this->firstname = null;
        $this->lastname = null;
    }


    /**
     * Supprime et Deconnecte un utilisateur
     */
    public function delete()
    {
        $sql = "DELETE FROM utilisateurs WHERE id = :id";

        $stmt = self::$db->prepare($sql);
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

    /**
     * Met à jour les attribut de l'objet
     * Modifie les informations en base de données
     */
    public function update($login, $password, $email, $firstname, $lastname)
    {
        // AJOUTER : Validation de l'email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email invalide");
        }

        // AJOUTER : Validation du mot de passe
        if (strlen($password) < 8) {
            throw new Exception("Le mot de passe doit contenir au moins 8 caractères");
        }

        // AJOUTER : Validation des champs vides
        if (empty($login) || empty($email) || empty($firstname) || empty($lastname)) {
            throw new Exception("Tous les champs sont obligatoires");
        }

        // Échapper les entrées
        $login = $this->e($login);
        $email = $this->e($email);
        $firstname = $this->e($firstname);
        $lastname = $this->e($lastname);



        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "UPDATE  utilisateurs SET login = :login, password = :password , email = :email, firstname = :firstname, lastname = :lastname WHERE id = :id";


        $stmt = self::$db->prepare($sql);
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

    /**
     * Retourne un booléen (true ou false)
     * Permettant de savoir si un utilisateur est connecter ou non
     */
    public function isConnected()
    {
        if ($this->id != null) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Retourne un tableau contenant
     *  l'ensemble des informations de l'utilisateur
     */
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
