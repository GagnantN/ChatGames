<?php
ob_start();
require_once __DIR__ . '/../bdd/db.php'; 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id_user = $_SESSION["id_user"];

// Récupérer les amis acceptés
$stmtFriend = $dbh->prepare("
    SELECT PLAYER.id, PLAYER.pseudo, PLAYER.image_profil 
    FROM FRIENDS 
    JOIN PLAYER ON FRIENDS.id_receveur = PLAYER.id_user
    WHERE FRIENDS.id_envoyeur = :id_user 
    AND FRIENDS.status = 'Accepter'
");
$stmtFriend->execute(['id_user' => $id_user]);
$friends = $stmtFriend->fetchAll(PDO::FETCH_ASSOC);

// Compter le nombre total de joueurs non amis pour la pagination

$limit = 4; // Nombre d'éléments par page
$page = isset($_GET['pagination']) ? (int)$_GET['pagination'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Requête pour compter les joueurs non amis
$count_stmt = $dbh->prepare("
        SELECT COUNT(*) AS total
        FROM (
        SELECT P.id_user, P.pseudo, P.image_profil
        FROM PLAYER P
        WHERE P.id_user <> :id_user
        EXCEPT
        SELECT P.id_user, P.pseudo, P.image_profil
        FROM PLAYER P
        JOIN FRIENDS F
        ON F.id_receveur = P.id_user ) AS toto
");

$count_stmt->execute(['id_user' => $id_user]);

// Récupération du résultat
$result = $count_stmt->fetch(PDO::FETCH_ASSOC);

// Vérification et définition de $total_players
if ($result && isset($result['total'])) {
    $total_players = (float) $result['total'];
} else {
    $total_players = 0; // Valeur par défaut si la requête échoue
}

// Définition de $total_pages
$total_pages = ($total_players > 0) ? ceil($total_players / $limit) : 1;

// Debugging (supprime ceci après vérification)
if (!isset($total_players)) {
    echo "<p style='color:red;'>Erreur : total_players non défini.</p>";
}

    $sql = "
        SELECT P.id_user, P.pseudo, P.image_profil
        FROM PLAYER P
        WHERE P.id_user <> :id_user
        EXCEPT
        SELECT P.id_user, P.pseudo, P.image_profil
        FROM PLAYER P
        JOIN FRIENDS F
        ON F.id_receveur = P.id_user
        LIMIT :limit
        OFFSET :offset
    ";
$stmt = $dbh->prepare($sql);
$stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$players = $stmt->fetchAll();

?>

<h2>Joueurs disponibles</h2>
    <div class="contain">
        <?php if(count($players) > 0):
        foreach ($players as $player) : ?>
            <div class="player-card">
                <img src="<?= htmlspecialchars($player['image_profil']) ?>" alt="Avatar">
                <h3 class="pseudo"><?= htmlspecialchars($player['pseudo']) ?></h3>
                <button class="add-button" onclick="window.location.href='index.php?page=Ajout_Amis&friend_id=<?= $player['id_user'] ?>'">Ajouter Ami</button>
            </div>
        <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun article trouvé.</p>
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
                <h3 class="pseudo"><?= htmlspecialchars($friend['pseudo']) ?></h3>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p>Vous n'avez pas encore d'amis.</p>
    <?php endif; ?>
</div>