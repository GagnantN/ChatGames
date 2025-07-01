<?php
require_once(__DIR__ . '/../bdd/db.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!$isAjax) {
    header('Location: index.php?page=profil');
    exit;
}

// Récupérer l'ID de l'ami depuis l'URL (et le valider)
$idAmi = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($idAmi <= 0) {
    echo "<p>ID utilisateur invalide.</p>";
    exit;
}

// Récupérer les jeux de l'ami
$stmt = $dbh->prepare("
    SELECT j.* FROM jeu j
    INNER JOIN utilisateur_jeu uj ON j.Id_Jeu = uj.Id_Jeu
    WHERE uj.id_utilisateur = ?
");
$stmt->execute([$idAmi]);
$jeuxAmi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="contenu">
    <h1>Jeux de l'ami</h1>
    <div class="grid-cartes">
        <?php foreach ($jeuxAmi as $jeu): ?>
            <div class="carte-jeu">
                <img src="<?= htmlspecialchars($jeu['images']) ?>" alt="<?= $jeu['nom'] ?>">
                <div class="contenu-carte">
                    <h2><?= htmlspecialchars($jeu['nom']) ?></h2>
                    <a href="index.php?page=detailJeu&id=<?= $jeu['Id_Jeu'] ?>" class="btn-en-savoir-plus">En savoir plus</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>
