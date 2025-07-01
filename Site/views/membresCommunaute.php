<?php
require_once(__DIR__ . '/../bdd/db.php');
$id = (int)($_GET['id'] ?? 0);

$stmt = $dbh->prepare("
    SELECT u.pseudo, u.imageProfil, u.id_utilisateur, u.genreJeu, u.support FROM utilisateur u
    JOIN utilisateur_communaute uc ON u.id_utilisateur = uc.id_utilisateur
    WHERE uc.id_communaute = ?
");
$stmt->execute([$id]);
$membres = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="containerBody">

    <div class="headerAccueil">
        <h2>Membres de la communauté</h2>
    </div>

    <div class="recherche-bar">
        <form method="get" action="index.php">
            <input type="hidden" name="page" value="rechercheAmis">
            <input type="text" name="search" placeholder="Rechercher un pseudo..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="submit">Rechercher</button>
        </form>
    </div>

    <div class="suggestions-utilisateurs">
        <?php foreach ($membres as $m): ?>
            <div class="carte-utilisateur">
                <img src="Site/assets/images/<?= htmlspecialchars($m['imageProfil']) ?>" alt="Profil de <?= htmlspecialchars($m['pseudo']) ?>" class="carte-image">
                <h3><?= htmlspecialchars($m['pseudo']) ?></h3>
                <p><?= htmlspecialchars($m['genreJeu']) ?></p>
                <p><?= htmlspecialchars($m['support']) ?></p>

                <div class="carte-boutons">
                    <a href="index.php?page=profilAmi&id=<?= $m['id_utilisateur'] ?>" class="btn-detail">
                        <img src="Site/assets/images/user-square.png" alt="">Détails
                    </a>
                    <a href="index.php?page=ajoutAmi&id=<?= $m['id_utilisateur'] ?>" class="btn-ajouter">
                        <img src="Site/assets/images/plus-circle.png" alt="">Ajoute
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>
