<?php
session_start();
require_once(__DIR__ . '/../bdd/db.php');

$message_id = $_POST['message_id'];
$reponse = $_POST['reponse']; // 'oui' ou 'non'

// Met à jour le message pour stocker la réponse
$stmt = $dbh->prepare("UPDATE messages SET response = ? WHERE id = ?");
$stmt->execute([$reponse, $message_id]);

if ($reponse === 'non') {
    // Trouver l'ID de la conversation
    $stmt = $dbh->prepare("SELECT conversation_id FROM messages WHERE id = ?");
    $stmt->execute([$message_id]);
    $conv_id = $stmt->fetchColumn();

    // Supprimer la conversation
    $dbh->prepare("DELETE FROM messages WHERE conversation_id = ?")->execute([$conv_id]);
    $dbh->prepare("DELETE FROM conversations WHERE id = ?")->execute([$conv_id]);
}


// Redirige vers la messagerie
header('Location: index.php?page=messagerie');
exit;
?>