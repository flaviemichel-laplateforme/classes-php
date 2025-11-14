<?php

// Configuration de la connexion à la base de données
$host = 'localhost';
$dbname = 'classes';
$username = 'root';
$password = '';  // Mot de passe vide par défaut avec Laragon

try {
    // Création de la connexion PDO
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);

    // Configuration des options PDO
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
