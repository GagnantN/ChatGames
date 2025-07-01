<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '../../bdd/db.php');

$stmt = $dbh->prepare("
  SELECT e.* FROM evenements e
  JOIN inscription_evenement ie ON e.id_evenement = ie.id_evenement
  WHERE ie.id_utilisateur = ?
  ORDER BY e.date_event DESC, e.heure_event DESC
");
$stmt->execute([$_SESSION['user']['id']]);
$mesEvenements = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="containerBody">
    <h1>Événements disponibles</h1>
    <?php
        $idUser = $_SESSION['user']['id'] ?? null;

        $isMaitre = false;
        $stmt = $dbh->prepare("SELECT COUNT(*) FROM communaute WHERE id_maitre = ?");
        $stmt->execute([$idUser]);
        $isMaitre = $stmt->fetchColumn() > 0;
        ?>

        <?php if ($isMaitre): ?>
            <div style="text-align: right; margin-bottom: 20px;">
                <a href="index.php?page=creerEvenement" class="btn-creer-evenement">Créer un événement</a>
            </div>
    <?php endif; ?>

    <div class="event-grid">
        <?php foreach ($mesEvenements as $event): ?>
            <div class="event-card">
                <?php
                    $isPast = strtotime("{$event['date_event']} {$event['heure_event']}") < time();
                    $cls = $isPast ? 'event-card past' : 'event-card';
                    ?>
                    <div class="<?= $cls ?>">
                    <?php if ($isPast): ?>
                        <div class="overlay-fini">Événement terminé</div>
                    <?php endif; ?>
                    ...
                </div>
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
                        <a href="index.php?page=participerEvenement&id=<?= $event['id_evenement'] ?>" class="join-btn">Participe !</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
