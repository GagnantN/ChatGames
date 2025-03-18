<?php
ob_start();
session_start();
require_once __DIR__ . '/../bdd/db.php'; 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION["id_user"])) {
    die("Erreur : utilisateur non connecté.");
}

$id_user = $_SESSION["id_user"];
$limit = 2; // Nombre d'éléments par page
$page = isset($_GET['pagination']) ? (int)$_GET['pagination'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Récupération des amis (dans les deux sens)
$stmtFriend = $dbh->prepare("  
    SELECT DISTINCT P.id_user, P.pseudo, P.image_profil  
    FROM PLAYER P  
    JOIN FRIENDS F ON (F.id_envoyeur = P.id_user OR F.id_receveur = P.id_user)  
    WHERE (F.id_envoyeur = :id_user OR F.id_receveur = :id_user)   
    AND F.status = 'Accepter'  
    AND P.id_user <> :id_user
");
$stmtFriend->execute(['id_user' => $id_user]);
$friends = $stmtFriend->fetchAll(PDO::FETCH_ASSOC);

// Compter le nombre total de joueurs non amis
$count_stmt = $dbh->prepare("  
    SELECT COUNT(*) AS total  
    FROM PLAYER P  
    WHERE P.id_user <> :id_user  
    AND NOT EXISTS (  
        SELECT 1 FROM FRIENDS F  
        WHERE (F.id_envoyeur = P.id_user AND F.id_receveur = :id_user)  
        OR (F.id_receveur = P.id_user AND F.id_envoyeur = :id_user)  
    )");
$count_stmt->execute(['id_user' => $id_user]);
$result = $count_stmt->fetch(PDO::FETCH_ASSOC);
$total_players = $result ? (int) $result['total'] : 0;
$total_pages = ($total_players > 0) ? ceil($total_players / $limit) : 1;

// Vérifier que l'offset ne dépasse pas le nombre total
if ($offset >= $total_players) {
    $offset = max(0, $total_players - $limit);
}

// Récupération des joueurs non amis
$stmt = $dbh->prepare("
    SELECT P.id_user, P.pseudo, P.image_profil
    FROM PLAYER P
    WHERE P.id_user <> ?
    AND NOT EXISTS (
        SELECT 1 FROM FRIENDS F
        WHERE (F.id_envoyeur = P.id_user AND F.id_receveur = ?)
        OR (F.id_receveur = P.id_user AND F.id_envoyeur = ?)
    )
    LIMIT ? OFFSET ?
");

$stmt->bindValue(1, $id_user, PDO::PARAM_INT);
$stmt->bindValue(2, $id_user, PDO::PARAM_INT);
$stmt->bindValue(3, $id_user, PDO::PARAM_INT);
$stmt->bindValue(4, $limit, PDO::PARAM_INT);
$stmt->bindValue(5, $offset, PDO::PARAM_INT);
$stmt->execute();
$players = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Joueurs disponibles</h2>
<div class="contain">
    <?php if (count($players) > 0): ?>
        <?php foreach ($players as $player) : ?>
            <div class="player-card">
                <img src="<?= htmlspecialchars($player['image_profil']) ?>" alt="Avatar">
                <h3 class="pseudo"> <?= htmlspecialchars($player['pseudo']) ?> </h3>
                <button class="add-button" onclick="window.location.href='index.php?page=Ajout_Amis&friend_id=<?= $player['id_user'] ?>'">Ajouter Ami</button>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucun joueur trouvé.</p>
    <?php endif; ?>
</div>

<!-- Pagination -->
<div class="pagination">
    <?php if ($page > 1) : ?>
        <a href="index.php?page=Matchmaking&pagination=<?= $page - 1 ?>" class="prev">Précédent</a>
    <?php endif; ?>

    <span>Page <?= $page ?> sur <?= $total_pages ?></span>

    <?php if ($page < $total_pages) : ?>
        <a href="index.php?page=Matchmaking&pagination=<?= $page + 1 ?>" class="next">Suivant</a>
    <?php endif; ?>
</div>

<h2>Mes Amis</h2>
<div class="contain">
    <?php if (count($friends) > 0) : ?>
        <?php foreach ($friends as $friend) : ?>
            <div class="player-card">
                <img src="<?= htmlspecialchars($friend['image_profil']) ?>" alt="Avatar">
                <h3 class="pseudo"> <?= htmlspecialchars($friend['pseudo']) ?> </h3>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p>Vous n'avez pas encore d'amis.</p>
    <?php endif; ?>
</div>