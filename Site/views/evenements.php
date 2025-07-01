<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '../../bdd/db.php');

$isConnected = isset($_SESSION['user']);

// Récupérer tous les événements triés par date de création DESC
$stmt = $dbh->prepare("SELECT * FROM evenements ORDER BY created_at DESC");
$stmt->execute();
$evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['message'])) {
    if ($_GET['message'] === 'inscription_succes') {
        echo '<div class="success-message">Inscription réussie à l\'événement !</div>';
    } elseif ($_GET['message'] === 'deja_inscrit') {
        echo '<div class="error-message">Vous êtes déjà inscrit à cet événement.</div>';
    }
}

?>

<div class="containerBody">


    <?php if (isset($_SESSION['alerte_evenement'])): ?>
        <div id="popup-overlay" class="popup-overlay" onclick="closePopup()">
            <div class="popup-message">
                <?= htmlspecialchars($_SESSION['alerte_evenement']) ?>
                <br><small>(Cliquez pour fermer)</small>
            </div>
        </div>
        <?php unset($_SESSION['alerte_evenement']); ?>
    <?php endif; ?>


    <div class="headerAccueil">
        <h1>Evénements</h1>
        <div class="buttonAccueil">
            <?php if (!$isConnected): ?>
                <a href="index.php?page=inscription">Inscription</a>
                <a href="index.php?page=connexion">Connexion</a>
            <?php else: ?>
                <a href="index.php?page=profil">
                    <img src="Site/assets/images/<?= htmlspecialchars($_SESSION['user']['imageProfil']) ?>">
                    <?= htmlspecialchars($_SESSION['user']['pseudo']) ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <?php
        $idUser = $_SESSION['user']['id'] ?? null;

        $isMaitre = false;
        $stmt = $dbh->prepare("SELECT COUNT(*) FROM communaute WHERE id_maitre = ?");
        $stmt->execute([$idUser]);
        $isMaitre = $stmt->fetchColumn() > 0;
        ?>

        <?php if ($isMaitre): ?>
            <div style="text-align: right; margin-bottom: 20px;">
                <a href="index.php?page=creerEvenement" class="btn-ajouter">Créer un événement</a>
            </div>
    <?php endif; ?>

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
                        <?php if (!$isConnected): ?>
                            <a href="#" class="join-btn" onclick="openPopup(); return false;">Participe !</a>
                        <?php else: ?>
                            <a href="index.php?page=participerEvenement&id=<?= $event['id_evenement'] ?>" class="join-btn">Participe !</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="popup-connexion" class="popup-overlay" style="display:none;" onclick="closePopup()">
        <div class="popup-message" onclick="event.stopPropagation()">
            <p>Vous devez être connecté pour effectuer cette action.</p>
            <div class="popup-buttons">
            <a href="index.php?page=inscription" class="popup-btn popup-btn-grey">S'inscrire</a>
            <a href="index.php?page=connexion" class="popup-btn popup-btn-blue">Se connecter</a>
            </div>
            <small>(Cliquez en dehors pour fermer)</small>
        </div>
    </div>
</div>
