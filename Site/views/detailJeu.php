<?php
require_once(__DIR__ . '/../bdd/db.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['id'])) {
    echo "Aucun jeu sélectionné.";
    exit;
}

$idJeu = $_GET['id'];
$stmt = $dbh->prepare("SELECT * FROM jeu WHERE Id_Jeu = ?");
$stmt->execute([$idJeu]);
$jeu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$jeu) {
    echo "Jeu introuvable.";
    exit;
}
?>

<div class="onglet">
    <div class="encadrement">

        <div class="detail-jeu">
            <h1><?= htmlspecialchars($jeu['nom']) ?></h1>

            <img src="<?= htmlspecialchars($jeu['images']) ?>" alt="<?= htmlspecialchars($jeu['nom']) ?>" class="image-jeu-detail">

            <div class="description-bloc">
                <h2>Description</h2>
                <p><?= nl2br(htmlspecialchars($jeu['description'])) ?></p>
            </div>

            <form method="POST" action="index.php?page=ajouterJeu">
                <input type="hidden" name="Id_Jeu" value="<?= $jeu['Id_Jeu'] ?>">
                <button type="submit" class="btn-ajouter-detail">Ajouter</button>
            </form>
        </div>
        
    </div>
</div>