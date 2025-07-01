<?php
    require_once(__DIR__ . '../../bdd/db.php'); 
    $ajaxPages = ['details', 'mesJeux', 'event', 'mesCommunautes'];
    $currentPage = $_GET['page'] ?? 'details';
?>


<div class="containerBody">
    <p id="filAriane">Mon profil > Détails</p>
    <div class="containerHeaderProfil">
        <img src="Site/assets/images/<?= htmlspecialchars($_SESSION['user']['imageProfil']) ?>" alt="Profil">
        <div class="info">
            <div class="title"><p><?= htmlspecialchars($_SESSION['user']['pseudo'] ?? '') ?></p></div>
            <?php
                $ageStr = '';
                if (!empty($_SESSION['user']['age'])) {
                    $dob = new DateTime($_SESSION['user']['age']); // la date stockée
                    $now = new DateTime();
                    $age = $now->diff($dob)->y; // différence en années
                    $ageStr = $age . ' ans';
                }
            ?>
            <p><?= $ageStr ?></p>
            <p><?= htmlspecialchars($_SESSION['user']['langue'] ?? '') ?> / <?= htmlspecialchars($_SESSION['user']['styleJeu'] ?? '') ?></p>
        </div>
        <a href="index.php?page=modifierProfil">Modifier son profil</a>
    </div>
</div>

<div class="tabs">
    <button data-page="index.php?page=details" class="active">Détails</button>
    <button data-page="index.php?page=mesJeux">Jeux</button>
    <button data-page="index.php?page=mesEvenements">Évènements</button>
    <button data-page="index.php?page=mesCommunautes">Communauté</button>
</div>

<div id="contenu-dynamique" class="<?= in_array($currentPage, $ajaxPages) ? 'pleinLargeur' : '' ?>">
    <!-- Le contenu AJAX va s'afficher ici -->
</div>