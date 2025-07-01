<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '../../bdd/db.php');



$id_demandeur = $_SESSION['user']['id'];
$id_cible = $_GET['id'] ?? null;

if (!$id_cible) {
    die("ID utilisateur manquant");
}

// Vérifie s’il existe déjà une conversation
$stmt = $dbh->prepare("SELECT id FROM conversations WHERE (user1 = ? AND user2 = ?) OR (user1 = ? AND user2 = ?)");
$stmt->execute([$id_demandeur, $id_cible, $id_cible, $id_demandeur]);
$conv = $stmt->fetch();

if (!$conv) {
    $dbh->prepare("INSERT INTO conversations (user1, user2) VALUES (?, ?)")->execute([$id_demandeur, $id_cible]);
    $conv_id = $dbh->lastInsertId();
} else {
    $conv_id = $conv['id'];
}

// Ajoute le message système
$content = $_SESSION['user']['pseudo'] . ' demande à être votre ami.';
$dbh->prepare("INSERT INTO messages (conversation_id, sender_id, content, type) VALUES (?, ?, ?, 'system')")
    ->execute([$conv_id, $id_demandeur, $content]);

header("Location: index.php?page=messagerie&id=$conv_id");
exit;
