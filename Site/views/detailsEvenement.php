<?php
require_once(__DIR__ . '/../bdd/db.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['id'])) {
    echo "Aucun événement sélectionné.";
    exit;
}

$idEvenement = $_GET['id'];

// Récupère l'événement avec la communauté et le maître
$stmt = $dbh->prepare("
    SELECT e.*, c.nom, u.pseudo AS id_maitre
    FROM evenements e
    JOIN communaute c ON e.id_communaute = c.id_communaute
    JOIN utilisateur u ON c.id_maitre = u.id_utilisateur
    WHERE e.id_evenement = ?
");
$stmt->execute([$idEvenement]);
$evenement = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$evenement) {
    echo "Événement introuvable.";
    exit;
}
?>

<div class="onglet">
    <div class="encadrement">

        <div class="detail-event">
            <h1><?= htmlspecialchars($evenement['nom']) ?></h1>

            <img src="Site/assets/images/<?= htmlspecialchars($evenement['imageProfil']) ?>" alt="<?= htmlspecialchars($evenement['nom']) ?>" class="image-jeu-detail">

            <div class="description-bloc">
                <h2>Description</h2>
                <p><?= nl2br(htmlspecialchars($evenement['description'])) ?></p>
            </div>

            <div class="info-bloc">
                <p><strong>Date :</strong> <?= date('d/m/Y', strtotime($evenement['date_event'])) ?></p>
                <p><strong>Heure :</strong> <?= date('H:i', strtotime($evenement['heure_event'])) ?></p>
                <p><strong>Communauté :</strong> <?= htmlspecialchars($evenement['nom']) ?></p>
                <p><strong>Maître du jeu :</strong> <?= htmlspecialchars($evenement['id_maitre']) ?></p>
            </div>

            <form method="POST" action="index.php?page=participerEvenement">
                <input type="hidden" name="event_id" value="<?= $evenement['id_evenement'] ?>">
                <button type="submit" class="btn-ajouter-detail">Participer à l'événement</button>
            </form>
        </div>

    </div>
</div>
