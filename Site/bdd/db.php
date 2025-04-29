<?php

// On commence la session pour accéder à $_SESSION
session_start();

// Configuration de la connexion à la base de données
$host = 'localhost';
$dbname = 'projet_fil_rouge_gagnant';
$user = 'root';
$password = '';

try {
    // Connexion à la base de données
    $dbh = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
    // Activer les erreurs PDO
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En cas d'erreur de connexion, afficher un message d'erreur
    die("Erreur de connexion : " . $e->getMessage());
}

?>
