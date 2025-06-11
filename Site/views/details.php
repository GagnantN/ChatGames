<?php
    require_once(__DIR__ . '../../bdd/db.php'); 
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if (!$isAjax) {
        header('Location: index.php?page=profil');
        exit;
    }
?>

<div class="onglet">
    <div class="encadrement">
        <h1>Description</h1>
        <p><?= htmlspecialchars($_SESSION['user']['description'] ?? '') ?></p>
    </div>
    <div class="encadrement">
        <h1>Disponibilité</h1>
        <div class="jours-groupes">
            <?php
            $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
            foreach ($jours as $jour) :
            ?>
                <div class="jour-colonne">
                    <h2><?= $jour ?></h2>
                    <img src="Site/assets/images/ButtonAurore.png" alt="Aurore">
                    <img src="Site/assets/images/ButtonMidi.png" alt="Midi">
                    <img src="Site/assets/images/ButtonCrepuscule.png" alt="Crépuscule">
                    <img src="Site/assets/images/ButtonNuit.png" alt="Nuit">
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>