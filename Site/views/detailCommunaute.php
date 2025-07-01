<?php
    require_once(__DIR__ . '/../bdd/db.php');

    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    // Empêche l'accès direct si ce n'est pas une requête AJAX
    if (!$isAjax) {
        header('Location: index.php?page=profilCommunaute');
        exit;
    }

    // Vérifie que l'ID de la communaute est passé
    if (!isset($_GET['id'])) {
        echo "Aucun utilisateur spécifié.";
        exit;
    }

    $idCommunaute = (int)($_GET['id'] ?? 0);

    $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
    $moments = ['Aurore', 'Midi', 'Crepuscule', 'Nuit'];

    // Description + styles
    $stmt = $dbh->prepare("SELECT * FROM communaute WHERE id_communaute = ?");
    $stmt->execute([$idCommunaute]);
    $commu = $stmt->fetch(PDO::FETCH_ASSOC);

    // Disponibilités
    $stmt = $dbh->prepare("SELECT jour, moment, disponible FROM disponibilites_communaute WHERE id_communaute = ?");
    $stmt->execute([$idCommunaute]);
    $dispoData = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $dispoData[$row['jour']][$row['moment']] = $row['disponible'];
        }
?>

<div class="onglet">
    <div class="encadrement">
        <h2>À propos de la communauté</h2>
        <p><?= htmlspecialchars($commu['description']) ?></p>
    </div>

    <div class="encadrement">
        <h1>Disponibilités</h1>
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

