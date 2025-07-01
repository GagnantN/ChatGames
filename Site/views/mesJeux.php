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

$stmt = $dbh->prepare("
    SELECT j.* FROM jeu j
    INNER JOIN utilisateur_jeu uj ON j.Id_Jeu = uj.Id_Jeu
    WHERE uj.id_utilisateur = ?
");
$stmt->execute([$_SESSION['user']['id']]);
$mesJeux = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="contenu">
    <h1>Mes jeux</h1>
    <a href="#" class="btn-en-savoir-plus" data-page="index.php?page=tousLesJeux">+ Ajouter un nouveau jeu</a>
    <div class="grid-cartes">
        <?php foreach ($mesJeux as $jeu): ?>
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
