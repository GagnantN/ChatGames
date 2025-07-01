<?php
require_once(__DIR__ . '/../bdd/db.php');
$id = (int)($_GET['id'] ?? 0);

// Récupérer tous les événements triés par date de création DESC
$stmt = $dbh->prepare("SELECT * FROM evenements WHERE id_communaute = ? ORDER BY created_at DESC");
$stmt->execute([$id]);
$evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="containerBody">
    <h2>Évènements de la communauté</h2>
    <ul class="evenement-list">
        <div class="event-grid">
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
                            <a href="index.php?page=participerEvenement&id=<?= $event['id_evenement'] ?>" class="join-btn">Participe !</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    </ul>
</div>