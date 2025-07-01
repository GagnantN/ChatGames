<?php
require_once(__DIR__ . '../../bdd/db.php');

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!$isAjax) {
    header('Location: index.php?page=profil');
    exit;
}

$jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
$moments = ['Aurore', 'Midi', 'Crepuscule', 'Nuit'];

// Récupérer les disponibilités utilisateur
$stmt = $dbh->prepare("SELECT jour, moment, disponible FROM disponibilites WHERE id_utilisateur = ?");
$stmt->execute([$_SESSION['user']['id']]);
$dispoData = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $dispoData[$row['jour']][$row['moment']] = $row['disponible'];
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
            <?php foreach ($jours as $jour): ?>
                <div class="jour-colonne">
                    <h2><?= $jour ?></h2>
                    <?php foreach ($moments as $moment): ?>
                        <?php
                            $isVisible = !empty($dispoData[$jour][$moment]) && $dispoData[$jour][$moment] == 1;
                            $style = $isVisible ? '' : 'style="visibility:hidden;"';
                        ?>
                        <img src="Site/assets/images/Button<?= $moment ?>.png" alt="<?= $moment ?>" <?= $style ?>>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
