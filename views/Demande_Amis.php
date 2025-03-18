<?php
ob_start();
require_once __DIR__ . '/../bdd/db.php'; 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_user = $_SESSION["id_user"];

// Récupérer les demandes d'amis en attente
$stmtDemande = $dbh->prepare("
    SELECT PLAYER.id_user, PLAYER.id, PLAYER.pseudo, PLAYER.image_profil 
    FROM FRIENDS
    JOIN PLAYER ON FRIENDS.id_envoyeur = PLAYER.id_user
    WHERE FRIENDS.id_receveur = :id_user AND FRIENDS.status = 'Attente'
");
$stmtDemande->execute(['id_user' => $id_user]);
$demandes = $stmtDemande->fetchAll(PDO::FETCH_ASSOC);

?>

<h2>Demandes d'amis reçues</h2>
<div class="contain">
    <?php if (count($demandes) > 0) : ?>
        <?php foreach ($demandes as $demande) : ?>
            <div class="player-card">
                <img src="<?= htmlspecialchars($demande['image_profil']) ?>" alt="Avatar">
                <h3 class="pseudo"><?= htmlspecialchars($demande['pseudo']) ?></h3>
                <button class="accept-button" onclick="window.location.href='index.php?page=Accept_Amis&friend_id=<?= $demande['id_user'] ?>'">Accepter</button>
                <button class="decline-button" onclick="window.location.href='index.php?page=Decline_Amis&friend_id=<?= $demande['id_user'] ?>'">Refuser</button>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p>Aucune demande d'ami en attente.</p>
    <?php endif; ?>
</div>