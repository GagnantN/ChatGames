<?php
// Connexion et session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '../../bdd/db.php');

$idUtilisateur = $_SESSION['user']['id'] ?? null;
$idCommunaute = $_POST['id_communaute'] ?? null;
$message = trim($_POST['message'] ?? '');

if ($idUtilisateur && $idCommunaute && $message !== '') {
    $stmt = $dbh->prepare("
        INSERT INTO messages_communaute (id_communaute, id_utilisateur, contenu) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$idCommunaute, $idUtilisateur, $message]);
}

header("Location: index.php?page=profilCommunaute&id=$idCommunaute");
exit;
