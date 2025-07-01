<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '../../bdd/db.php');

$idUser = $_SESSION['user']['id'] ?? null;
$idEvent = $_GET['id'] ?? null;

if (!$idUser || !$idEvent) {
    die("Requête invalide.");
}

// Vérifie si déjà inscrit
$stmt = $dbh->prepare("SELECT COUNT(*) FROM inscription_evenement WHERE id_utilisateur = ? AND id_evenement = ?");
$stmt->execute([$idUser, $idEvent]);
$alreadyJoined = $stmt->fetchColumn();

if ($alreadyJoined) {
    $_SESSION['alerte_evenement'] = "Vous êtes déjà inscrit à cet événement.";
    header("Location: index.php?page=evenements");
    exit;
}

// Inscription
$stmt = $dbh->prepare("INSERT INTO inscription_evenement (id_utilisateur, id_evenement) VALUES (?, ?)");
$stmt->execute([$idUser, $idEvent]);

$_SESSION['alerte_evenement'] = "Inscription réussie à l'événement !";
header("Location: index.php?page=evenements");
exit;

?>
