<?php
session_start();
require_once(__DIR__ . '/../bdd/db.php');

if (!isset($_SESSION['user']['id']) || !isset($_GET['id'])) {
    $_SESSION['alerte_communaute'] = "Erreur : paramètres manquants.";
    header("Location: index.php?page=communaute");
    exit;
}

$idUtilisateur = $_SESSION['user']['id'];
$idCommunaute = (int)$_GET['id'];

// Vérifie si l'utilisateur est membre
$stmt = $dbh->prepare("SELECT COUNT(*) FROM utilisateur_communaute WHERE id_utilisateur = ? AND id_communaute = ?");
$stmt->execute([$idUtilisateur, $idCommunaute]);
$estMembre = $stmt->fetchColumn();

if ($estMembre > 0) {
    // Supprimer de la table utilisateur_communaute
    $stmt = $dbh->prepare("DELETE FROM utilisateur_communaute WHERE id_utilisateur = ? AND id_communaute = ?");
    $stmt->execute([$idUtilisateur, $idCommunaute]);

    // Décrémenter le compteur membres
    $stmt = $dbh->prepare("UPDATE communaute SET membres = GREATEST(membres - 1, 0) WHERE id_communaute = ?");
    $stmt->execute([$idCommunaute]);

    $_SESSION['alerte_communaute'] = "Vous avez quitté la communauté.";
} else {
    $_SESSION['alerte_communaute'] = "Vous n'étiez pas membre de cette communauté.";
}

header("Location: index.php?page=communaute");
exit;
