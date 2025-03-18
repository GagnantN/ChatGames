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

// Mettre à jour le statut de la relation
$stmtUser = $dbh->prepare("
    UPDATE FRIENDS SET status = 'Accepter' 
    WHERE id_envoyeur = :friend_id AND id_receveur = :id_user AND status = 'Attente'
");
$stmtUser->execute(['friend_id' => $friend_id, 'id_user' => $user_id]);
$stmtUser->fetchAll(PDO::FETCH_ASSOC);

$stmtAmis = $dbh->prepare("
    INSERT INTO FRIENDS (id_envoyeur, id_receveur, status)
    VALUES (:id_envoyeur, :id_receveur, :status)
");
$stmtAmis->execute(['id_envoyeur' => $user_id, 'id_receveur' => $friend_id, 'status' => 'Accepter']);

if ($stmtUser->rowCount() > 0) {
    echo json_encode(["message" => "Ami accepté avec succès"]);
} else {
    echo json_encode(["message" => "Erreur lors de l'acceptation"]);
}
?>