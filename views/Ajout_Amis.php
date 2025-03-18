<?php
session_start();
require_once "../bdd/db.php"; // Assure-toi d'avoir ton fichier de connexion

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    die("Erreur : utilisateur non connecté.");
}

$user_id = $_SESSION['id_user']; // ID de l'utilisateur connecté
$friend_id = $_GET['friend_id'] ?? null; // Récupère l'ID de l'ami depuis l'URL

if (!$friend_id || $user_id == $friend_id) {
    die("Erreur : ID invalide.");
}

// Vérifier si la relation existe déjà
$stmtSelect = $dbh->prepare("SELECT * FROM FRIENDS WHERE 
    (id_envoyeur = :id_user AND id_receveur = :friend_id) 
    OR (id_envoyeur = :friend_id AND id_receveur = :id_user)");
$stmtSelect->execute(['id_user' => $user_id, 'friend_id' => $friend_id]);
$stmtSelect->fetchAll(PDO::FETCH_ASSOC);

if ($stmtSelect->rowCount() > 0) {
    die("Déjà en attente ou amis.");
}

// Insérer la demande d'ami
$stmtInsert = $dbh->prepare("INSERT INTO FRIENDS (id_envoyeur, id_receveur, status) VALUES (:id_user, :friend_id, 'Attente')");
$stmtInsert->execute(['id_user' => $user_id, 'friend_id' => $friend_id]);

echo "Demande d'ami envoyée avec succès !";
?>
<a href="Matchmaking.php">Retour</a>