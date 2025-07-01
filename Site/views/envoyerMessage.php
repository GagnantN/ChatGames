<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../bdd/db.php'); // adapte le chemin si besoin

// Vérification de la connexion
if (!isset($_SESSION['user'])) {
    die("Vous devez être connecté pour envoyer un message.");
}

$expediteur_id = $_SESSION['user']['id'];
$destinataire_id = $_POST['destinataire_id'] ?? null;
$message = trim($_POST['message'] ?? '');
$image_path = null;

if (!$destinataire_id || $message === '') {
    die("Destinataire ou message invalide.");
}

// Vérifie que le destinataire existe
$stmt = $dbh->prepare("SELECT COUNT(*) FROM utilisateur WHERE id_utilisateur = ?");
$stmt->execute([$destinataire_id]);

if ($stmt->fetchColumn() == 0) {
    die("Erreur : l'utilisateur destinataire n'existe pas (id = $destinataire_id).");
}


// Vérifie si une conversation existe déjà entre les deux utilisateurs
$stmt = $dbh->prepare("SELECT id FROM conversations WHERE 
    (user1 = :u1 AND user2 = :u2) OR 
    (user1 = :u2 AND user2 = :u1)");
$stmt->execute(['u1' => $expediteur_id, 'u2' => $destinataire_id]);
$conv = $stmt->fetch();

if (!$conv) {
    var_dump($destinataire_id); die();
    // Crée une nouvelle conversation si elle n'existe pas
    $stmt = $dbh->prepare("INSERT INTO conversations (user1, user2) VALUES (?, ?)");
    $stmt->execute([$expediteur_id, $destinataire_id]);
    $conversation_id = $dbh->lastInsertId();
} else {
    $conversation_id = $conv['id'];
}

// Vérifie si les deux sont amis (ex: conversation validée par un message "oui")
$stmt = $dbh->prepare("
    SELECT 1 FROM messages 
    WHERE conversation_id = ? AND type = 'system' AND response = 'oui'
");
$stmt->execute([$conversation_id]);
$isFriend = $stmt->fetchColumn();

if (!$isFriend) {
    die("Vous ne pouvez pas envoyer de message à cet utilisateur.");
}

// Image envoyée ?
if (!empty($_FILES['image']['tmp_name'])) {
    $targetDir = "Site/assets/messages/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $filename = basename($_FILES["image"]["name"]);
    $targetFile = $targetDir . uniqid() . "_" . $filename;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
        $image_path = $targetFile;
    }
}

// Envoi message texte ?
if (!empty($message)) {
    $stmt = $dbh->prepare("INSERT INTO messages (conversation_id, sender_id, content, type) VALUES (?, ?, ?, 'text')");
    $stmt->execute([$conversation_id, $expediteur_id, $message]);
}

// Envoi image ?
if ($image_path) {
    $stmt = $dbh->prepare("INSERT INTO messages (conversation_id, sender_id, content, type) VALUES (?, ?, ?, 'image')");
    $stmt->execute([$conversation_id, $expediteur_id, $image_path]);
}

// Redirection vers la messagerie active
header("Location: index.php?page=messagerie&id=$destinataire_id");
exit;
