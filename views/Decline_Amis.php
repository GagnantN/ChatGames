<?php
session_start();
require_once "../bdd/db.php"; // Connexion à la base

if (!isset($_SESSION['id_user'])) {
    echo json_encode(["message" => "Utilisateur non connecté"]);
    exit;
}

$user_id = $_SESSION['id_user']; // L'ID de l'utilisateur connecté
$friend_id = $_GET['friend_id'] ?? null; // Récupère l'ID de l'ami depuis l'URL

if (!$friend_id) {
    echo json_encode(["message" => "ID de l'ami manquant"]);
    exit;
}

// Supprimer la demande d'ami
$stmt = $dbh->prepare("
    DELETE FROM FRIENDS 
    WHERE id_envoyeur = :friend_id AND id_receveur = :id_user AND status = 'Attente'
");
$stmt->execute(['friend_id' => $friend_id, 'id_user' => $user_id]);
$stmt->fetchAll(PDO::FETCH_ASSOC);

if ($stmt->rowCount() > 0) {
    echo json_encode(["message" => "Demande d'ami refusée"]);
} else {
    echo json_encode(["message" => "Erreur lors du refus"]);
}
?>