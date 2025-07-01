<?php
// Connexion et session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '../../bdd/db.php');

$idUtilisateur = $_SESSION['user']['id'];
$idCommunaute = (int)$_GET['id'];


if (!isset($_SESSION['user']['id']) || !isset($_GET['id'])) {
    $_SESSION['alerte_communaute'] = "Erreur : paramètres manquants.";
    header("Location: index.php?page=communaute");
    exit;
}

// Vérifie si l'utilisateur est déjà membre
$stmt = $dbh->prepare("SELECT COUNT(*) FROM utilisateur_communaute WHERE id_utilisateur = ? AND id_communaute = ?");
$stmt->execute([$idUtilisateur, $idCommunaute]);
$dejaMembre = $stmt->fetchColumn();

if ($dejaMembre > 0) {
    $_SESSION['alerte_communaute'] = "Vous êtes déjà membre de cette communauté.";
    header("Location: index.php?page=communaute");
    exit;
}

// Ajoute l'utilisateur à la communauté
$stmt = $dbh->prepare("INSERT INTO utilisateur_communaute (id_utilisateur, id_communaute) VALUES (?, ?)");
$stmt->execute([$idUtilisateur, $idCommunaute]);

// Incrémente le compteur de membres dans la table communaute
$stmt = $dbh->prepare("UPDATE communaute SET membres = membres + 1 WHERE id_communaute = ?");
$stmt->execute([$idCommunaute]);

// Message automatique
$pseudo = $_SESSION['user']['pseudo'];
$stmt = $dbh->prepare("
    INSERT INTO messages_communaute (id_communaute, id_utilisateur, contenu, type) 
    VALUES (?, ?, ?, 'system')
");
$stmt->execute([$idCommunaute, $idUtilisateur, "$pseudo a rejoint la communauté."]);


$_SESSION['alerte_communaute'] = "Bienvenue dans la communauté !";
header("Location: index.php?page=communaute");
exit;
