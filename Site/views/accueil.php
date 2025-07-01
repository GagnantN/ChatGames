<?php
require_once(__DIR__ . '../../bdd/db.php');

$isConnected = isset($_SESSION['user']);
$userId = $isConnected ? $_SESSION['user']['id'] : null;

$suggestedUsers = [];
$evenementsActifs = [];
$randomCommunautes = [];

// Récupérer les événements actifs (accessibles à tous)
$stmt = $dbh->prepare("SELECT * FROM evenements 
                       WHERE CONCAT(date_event, ' ', heure_event) >= NOW() 
                       ORDER BY created_at DESC");
$stmt->execute();
$evenementsActifs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer des communautés aléatoires (accessibles à tous)
$stmt = $dbh->prepare("SELECT * FROM communaute ORDER BY RAND() LIMIT 4");
$stmt->execute();
$randomCommunautes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Suggestions d'utilisateurs uniquement si connecté
if ($isConnected) {
    $stmt = $dbh->prepare("SELECT id_utilisateur, pseudo, imageProfil, genreJeu, support 
                           FROM utilisateur 
                           WHERE id_utilisateur != ? 
                           ORDER BY RAND() 
                           LIMIT 4");
    $stmt->execute([$userId]);
    $suggestedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="containerBody">
    <div class="headerAccueil">
        <h1>Accueil</h1>
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

    <!-- POPUP CONNEXION -->
    <div id="popup-connexion" class="popup-overlay" style="display:none;" onclick="closePopupConnexion()">
        <div class="popup-message" onclick="event.stopPropagation()">
            <p>Vous devez être connecté pour effectuer cette action.</p>
            <div class="popup-buttons">
            <a href="index.php?page=inscription" class="popup-btn popup-btn-grey">S'inscrire</a>
            <a href="index.php?page=connexion" class="popup-btn popup-btn-blue">Se connecter</a>
            </div>
            <small>(Cliquez en dehors pour fermer)</small>
        </div>
    </div>

    <?php if (!$isConnected): ?>
        <!-- Communautés -->
        <h2>Suggestion des Communautés</h2>
        <div class="team-grid">
            <?php foreach ($randomCommunautes as $commu): ?>
                <div class="team-card">
                    <img src="Site/assets/images/<?= htmlspecialchars($commu['imageProfil']) ?>" alt="Image" class="team-img">
                    <div class="team-content">
                        <div class="team-header">
                            <h2><?= htmlspecialchars($commu['nom']) ?></h2>
                            <span class="member-count"><img src="Site/assets/images/users-group.png"><?= (int)$commu['membres'] ?></span>
                        </div>
                        <p class="slogan">“ <?= htmlspecialchars($commu['description']) ?> “</p>
                        <div class="tags">
                            <span><?= htmlspecialchars($commu['styleGenreUn']) ?></span>
                            <span><?= htmlspecialchars($commu['styleGenreDeux']) ?></span>
                            <span><?= htmlspecialchars($commu['styleGenreTrois']) ?></span>
                        </div>
                        <div class="buttons">
                            <a href="index.php?page=profilCommunaute&id=<?= $commu['id_communaute'] ?>" class="details-btn">Détails</a>
                            <a href="#" class="join-btn" onclick="openPopupConnexion();">Rejoins-nous !</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Événements actifs -->
        <h2>Événements en cours</h2>
        <div class="event-grid">
            <?php foreach ($evenementsActifs as $event): ?>
                <div class="event-card">
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
                            <a href="javascript:void(0);" class="join-btn" onclick="openPopupConnexion();">Participe !</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <!-- Suggestions d'Amis -->
        <h2>Suggestion d'Amis</h2>
        <div class="suggestions-utilisateurs">
            <?php foreach ($suggestedUsers as $user): ?>
                <div class="carte-utilisateur">
                    <img src="Site/assets/images/<?= htmlspecialchars($user['imageProfil']) ?>" alt="Profil de <?= htmlspecialchars($user['pseudo']) ?>" class="carte-image">
                    <h3><?= htmlspecialchars($user['pseudo']) ?></h3>
                    <p><?= htmlspecialchars($user['genreJeu']) ?></p>
                    <p><?= htmlspecialchars($user['support']) ?></p>
                    <div class="carte-boutons">
                        <a href="index.php?page=profilAmi&id=<?= $user['id_utilisateur'] ?>" class="btn-detail"><img src="Site/assets/images/user-square.png"> Détails</a>
                        <a href="index.php?page=ajoutAmi&id=<?= $user['id_utilisateur'] ?>" class="btn-ajouter"><img src="Site/assets/images/plus-circle.png"> Ajoute</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Événements actifs -->
        <h2>Événements en cours</h2>
        <div class="event-grid">
            <?php foreach ($evenementsActifs as $event): ?>
                <div class="event-card">
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
    <?php endif; ?>


</div>
