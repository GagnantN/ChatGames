<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '../../bdd/db.php');

// Vérifie si un ID d'ami est fourni
$idAmi = $_GET['id'] ?? null;

if (!$idAmi || !is_numeric($idAmi)) {
    die("Utilisateur non trouvé.");
}

// Récupère les événements de l'utilisateur
$stmt = $dbh->prepare("
  SELECT e.* FROM evenements e
  JOIN inscription_evenement ie ON e.id_evenement = ie.id_evenement
  WHERE ie.id_utilisateur = ?
  ORDER BY e.date_event DESC, e.heure_event DESC
");
$stmt->execute([$idAmi]);
$evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="containerBody">
    <h1>Événements de l'utilisateur</h1>

    <div class="event-grid">
        <?php if (count($evenements) === 0): ?>
            <p style="text-align:center;">Aucun événement trouvé pour cet utilisateur.</p>
        <?php endif; ?>

        <?php foreach ($evenements as $event): ?>
            <?php
                $isPast = strtotime("{$event['date_event']} {$event['heure_event']}") < time();
                $cls = $isPast ? 'event-card past' : 'event-card';
            ?>
            <div class="<?= $cls ?>">
                <?php if ($isPast): ?>
                    <div class="overlay-fini">Événement terminé</div>
                <?php endif; ?>
                <img src="Site/assets/images/<?= htmlspecialchars($event['imageProfil']) ?>" alt="Événement">
                <div class="event-info">
                    <div class="event-savoir">
                        <div class="event-date">
                            <span><img src="Site/assets/images/calendar.png"><?= date('d M.', strtotime($event['date_event'])) ?></span>
                            <span><img src="Site/assets/images/clock.png"><?= date('H \h', strtotime($event['heure_event'])) ?></span>
                        </div>
                        <div class="event-separator"></div>
                        <div class="event-text">
                            <h2><?= htmlspecialchars($event['theme']) ?></h2>
                            <p><?= htmlspecialchars($event['nom']) ?></p>
                        </div>
                    </div>
                    <div class="event-buttons">
                        <a href="index.php?page=detailsEvenement&id=<?= $event['id_evenement'] ?>" class="details-btn">Détails</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
