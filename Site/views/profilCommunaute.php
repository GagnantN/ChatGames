<?php
require_once(__DIR__ . '../../bdd/db.php');

if (!isset($_GET['id'])) {
    echo "Aucune communauté sélectionnée.";
    exit;
}

$idCommunaute = (int) $_GET['id'];

$stmt = $dbh->prepare("SELECT * FROM communaute WHERE id_communaute = ?");
$stmt->execute([$idCommunaute]);
$communaute = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$communaute) {
    echo "Communauté introuvable.";
    exit;
}

$ajaxPages = ['chat', 'membres', 'evenements', 'informations'];
$currentPage = $_GET['page'] ?? 'chat';
?>

<div class="containerBody">
    <p id="filAriane">Communauté : <?= htmlspecialchars($communaute['nom']) ?> > Chat</p>

    <div class="containerHeaderProfil">
        <img src="Site/assets/images/<?= htmlspecialchars($communaute['imageProfil']) ?>" alt="Image communauté">
        <div class="info">
            <div class="title"><p><?= htmlspecialchars($communaute['nom']) ?></p></div>
            <p><?= htmlspecialchars($communaute['description']) ?></p>
        </div>
    </div>
</div>

<div class="tabs">
    <button data-page="index.php?page=chatCommunaute&id=<?= $idCommunaute ?>" class="active">Chat Group</button>
    <button data-page="index.php?page=membresCommunaute&id=<?= $idCommunaute ?>">Listes Membres</button>
    <button data-page="index.php?page=evenementsCommunaute&id=<?= $idCommunaute ?>">Events</button>
    <button data-page="index.php?page=detailCommunaute&id=<?= $idCommunaute ?>">Description</button>
</div>

<div id="contenu-dynamique" class="<?= in_array($currentPage, $ajaxPages) ? 'pleinLargeur' : '' ?>">
    <!-- Contenu AJAX -->
</div>
