<?php

require_once 'db.php';

function getJeu(){
    global $dbh;
    $stmt = $dbh->query("SELECT * FROM GAME");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUser(){
    global $dbh;
    $stmt = $dbh->query("SELECT * FROM users");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function registerUser($nom, $prenom, $email, $pseudo, $password, $image) {
    // Variable de connexion à la base de données
    global $dbh;
    // Requête SQL pour inscrire un nouvel utilisateur
    $stmt = $dbh->prepare("INSERT INTO users (nom, prenom, email, pseudo, mot_de_passe, image_profil) VALUES (:nom, :prenom, :email, :pseudo, :mot_de_passe, :image_profil)");
    // Execution de la requête avec les paramètres pour éviter les injections SQL
    return $stmt->execute([
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':email' => $email,
        ':pseudo' => $pseudo,
        // Cryptage du mot de passe
        ':mot_de_passe' => password_hash($password, PASSWORD_DEFAULT),
        ':image_profil' => $image,
    ]);
}

// Récupère un jeu spécifique par ID
function getJeuById($id) {
    global $dbh;
    $stmt = $dbh->prepare("SELECT * FROM GAME WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Récupère tous les images d'un jeu par ID
function getImageByID($id) {
    global $dbh;
    $stmt = $dbh->prepare("SELECT images FROM GAME WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Récupère un utilisateur spécifique par ID
function isLoggedIn() {
    return isset($_SESSION['user']);
}

function redirect($url) {
    header("Location: $url");
    exit;
}


//Afficher le profil
// function afficheProfil(){
//     $user_id = $_SESSION["user_id"];

//     $stmt = $dbh->prepare("
//         SELECT jeu.nom, jeu.description, jeu.image, jeu.id 
//         FROM jeu
//         INNER JOIN favoris ON jeu.id = favoris.jeu_id
//         WHERE favoris.user_id = :user_id
//     ");
//     $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
//     $stmt->execute();

//     $jeux = $stmt->fetchAll(PDO::FETCH_ASSOC);

//     $nom = $_SESSION["nom"];
//     $prenom = $_SESSION["prenom"];
//     $email = $_SESSION["email"];
//     $pseudo = $_SESSION["user_pseudo"];
//     $image = $_SESSION["image_profil"];

//     return;
// }
?>