<?php
    require_once(__DIR__ . '../../bdd/db.php'); 
    $ajaxPages = ['details', 'jeu', 'event', 'communaute'];
    $currentPage = $_GET['page'] ?? 'details';
?>


<div class="containerBody">
    <p>Mon profil > Détails</p>
    <div class="containerHeaderProfil">
        <img src="Site/assets/images/<?= htmlspecialchars($_SESSION['user']['imageProfil']) ?>">
        <div class="info">
            <div class="title"><p><?= htmlspecialchars($_SESSION['user']['pseudo'] ?? '') ?></p></div>
            <p><?= htmlspecialchars($_SESSION['user']['age'] ?? '') ?></p>
            <p><?= htmlspecialchars($_SESSION['user']['langue'] ?? '') ?> / <?= htmlspecialchars($_SESSION['user']['styleJeu'] ?? '') ?></p>
        </div>
        <a href="modifeProfil">Modifier son profil</a>
    </div>
</div>

<div class="tabs">
    <button data-page="index.php?page=details" class="active">Détails</button>
    <button data-page="index.php?page=jeu">Jeux</button>
    <button data-page="index.php?page=event">Évènements</button>
    <button data-page="index.php?page=communaute">Communauté</button>
</div>

<div id="contenu-dynamique" class="<?= in_array($currentPage, $ajaxPages) ? 'pleinLargeur' : '' ?>">
    <!-- Le contenu AJAX va s'afficher ici -->
</div>