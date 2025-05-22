<?php
require_once(__DIR__ . '../../bdd/db.php'); // si besoin

$isConnected = isset($_SESSION['ID_Utilisateur']);
?>

<div class="containerBody">

    <div class="headerAccueil">
        <h1>Accueil</h1>
        <div class="buttonAccueil">
            <?php if (!$isConnected): ?>
                <a href="index.php?page=inscription">Inscription</a>
                <a href="index.php?page=connexion">Connexion</a>
            <?php else: ?>
                <a href="index.php?page=profil">
                    <img src="assets/images/<?= htmlspecialchars($_SESSION['imageProfil'] ?? 'imageProfil.png') ?>" alt="Profil" style="width: 32px; height: 32px; border-radius: 50%; vertical-align: middle;">
                    <?= htmlspecialchars($_SESSION['pseudo']) ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!$isConnected): ?>
        <h2>Suggestion des communautés</h2>
        <h2>Evenements en cours</h2>
    <?php else: ?>
        <h2>Suggestion d'Amis</h2>
        <h2>Evenements en cours</h2>
    <?php endif; ?>

</div>
