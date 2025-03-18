<?php
// Inclure le fichier functions.php
require_once '../bdd/functions.php';


// Vérifie si un ID est présent dans l'URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $jeux = getJeuById($id);
    $carrousels = getImageByID($id);
    

    if (!$jeux) {
        die("Jeu non trouvé !");
    }
} else {
    die("ID invalide !");
}


?>
    </br><h1 class="jeuTitre"><?= htmlspecialchars($jeux['nom']) ?></h1>
    <div class="jeuContainer">
        <p class="jeuDescription"><?= htmlspecialchars($jeux['description']) ?></p>
        <img class="jeuImage" src="<?= htmlspecialchars($jeux['image_profil']) ?>" alt="Fiche du jeu <?= htmlspecialchars($jeux['nom']) ?>">
    </div>
    <?php
    if (isset($_SESSION["user_id"])) {
    ?>
        <form action="index.php?page=ajout_favori" method="POST">
                <input type="hidden" name="jeu_id" value="<?php echo $jeux['id']; ?>">
                <button type="submit">Ajouter aux Favoris</button>
        </form>
    <?php } ?>

    <div class="carousel">
    <?php foreach ($carrousels as $carrousel): ?>
        <?php
        // Séparer les images
        $images = explode("??", $carrousel['images']);
        foreach ($images as $image): ?>
            <div class="slide">
                <img class="imgCarrousel" src="<?= trim($image) ?>" alt="Game Image">
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </div>

        <!-- Lightbox pour afficher l'image en grand -->
    <div id="lightbox" class="lightbox">
        <span class="close">&times;</span>
        <img id="lightbox-img" class="lightbox-img" src="" alt="Image en grand">
    </div>

    <h2><a href="index.php?page=Accueil">Retour à l'Accueil</a></h2>