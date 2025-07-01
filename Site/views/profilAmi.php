<?php
require_once(__DIR__ . '../../bdd/db.php'); 

// Sécurité de base : vérifier si un id est fourni
if (!isset($_GET['id'])) {
    echo "Aucun utilisateur sélectionné.";
    exit;
}

$idAmi = (int) $_GET['id'];

// Récupération des infos de l'utilisateur ami
$stmt = $dbh->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = ?");
$stmt->execute([$idAmi]);
$ami = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ami) {
    echo "Utilisateur introuvable.";
    exit;
}

$ajaxPages = ['details', 'jeu', 'event', 'communaute'];
$currentPage = $_GET['page'] ?? 'details';
?>

<div class="containerBody">
    <p id="filAriane">Profil de <?= htmlspecialchars($ami['pseudo']) ?> > Détails</p>
    <div class="containerHeaderProfil">
        <img src="Site/assets/images/<?= htmlspecialchars($ami['imageProfil']) ?>" alt="Profil">
        <div class="info">
            <div class="title"><p><?= htmlspecialchars($ami['pseudo']) ?></p></div>
            <?php
                $ageStr = '';
                if (!empty($ami['age'])) {
                    $dob = new DateTime($ami['age']);
                    $now = new DateTime();
                    $age = $now->diff($dob)->y;
                    $ageStr = $age . ' ans';
                }
            ?>
            <p><?= $ageStr ?></p>
            <p><?= htmlspecialchars($ami['langue']) ?> / <?= htmlspecialchars($ami['styleJeu']) ?></p>
        </div>
    </div>
</div>

<div class="tabs">
    <button data-page="index.php?page=detailUtilisateur&id=<?= $idAmi ?>" class="active">Détails</button>
    <button data-page="index.php?page=jeuUtilisateur&id=<?= $idAmi ?>">Jeux</button>
    <button data-page="index.php?page=eventUtilisateur&id=<?= $idAmi ?>">Évènements</button>
    <button data-page="index.php?page=communauteUtilisateur&id=<?= $idAmi ?>">Communauté</button>
</div>

<div id="contenu-dynamique" class="<?= in_array($currentPage, $ajaxPages) ? 'pleinLargeur' : '' ?>">
    <!-- Le contenu AJAX s’affichera ici selon les onglets -->
</div>
