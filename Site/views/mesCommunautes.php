<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '../../bdd/db.php');

// ID utilisateur connecté
$utilisateur_id = $_SESSION['user']['id'] ?? null;

if (!$utilisateur_id) {
    die("Utilisateur non connecté.");
}

// Récupère les communautés auxquelles l'utilisateur est inscrit
$stmt = $dbh->prepare("
    SELECT c.* 
    FROM communaute c
    JOIN utilisateur_communaute uc ON uc.id_communaute = c.id_communaute
    WHERE uc.id_utilisateur = ?
");
$stmt->execute([$utilisateur_id]);
$communautes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Reprise de ta fonction dispo
function getDisponibilitesCommunaute($dbh, $idCommunaute) {
    $stmt = $dbh->prepare("SELECT jour, moment FROM disponibilites_communaute WHERE id_communaute = ? AND disponible = 1");
    $stmt->execute([$idCommunaute]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $dispoFormatted = [];

    foreach ($result as $row) {
        $dispoFormatted[$row['jour']][] = $row['moment'];
    }

    return $dispoFormatted;
}
?>

<div class="containerBody">
    <div class="headerAccueil">
        <h1>Mes communautés</h1>
    </div>

    <div class="team-grid">
        <?php foreach ($communautes as $commu): 
            $disponibilites = getDisponibilitesCommunaute($dbh, $commu['id_communaute']);
        ?>
            <div class="team-card">
                <img src="Site/assets/images/<?= htmlspecialchars($commu['imageProfil']) ?>" alt="Image" class="team-img">
                <div class="team-content">
                    <div class="team-header">
                        <h2><?= htmlspecialchars($commu['nom']) ?></h2>
                        <span class="member-count">
                            <img src="Site/assets/images/users-group.png">
                            <?= (int)$commu['membres'] ?>
                        </span>
                    </div>
                    <p class="slogan">“ <?= htmlspecialchars($commu['description']) ?> “</p>
                    <div class="tags">
                        <span><?= htmlspecialchars($commu['styleCommunautaire']) ?></span>
                        <span><?= htmlspecialchars($commu['styleGenreUn']) ?></span>
                        <span><?= htmlspecialchars($commu['styleGenreDeux']) ?></span>
                        <span><?= htmlspecialchars($commu['styleGenreTrois']) ?></span>
                    </div>
                    <div class="buttons">
                        <a href="index.php?page=profilCommunaute&id=<?= $commu['id_communaute'] ?>" class="details-btn">Détails</a>
                        <a href="index.php?page=quitterCommunaute&id=<?= $commu['id_communaute'] ?>" class="join-btn leave">Quitter</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
