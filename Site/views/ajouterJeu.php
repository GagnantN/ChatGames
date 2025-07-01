<?php
require_once(__DIR__ . '/../bdd/db.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Id_Jeu'])) {
    $idUtilisateur = $_SESSION['user']['id'];
    $idJeu = $_POST['Id_Jeu'];

    // Vérifie si le jeu est déjà associé à l'utilisateur
    $check = $dbh->prepare("SELECT 1 FROM utilisateur_jeu WHERE id_utilisateur = ? AND Id_Jeu = ?");
    $check->execute([$idUtilisateur, $idJeu]);

    if (!$check->fetch()) {
        $stmt = $dbh->prepare("INSERT INTO utilisateur_jeu (id_utilisateur, Id_Jeu) VALUES (?, ?)");
        $stmt->execute([$idUtilisateur, $idJeu]);
    }

    header('Location: index.php?page=mesJeux');
    exit;
}
?>
