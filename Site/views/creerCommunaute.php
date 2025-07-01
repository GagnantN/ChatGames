<?php
require_once(__DIR__ . '../../bdd/db.php'); 
    // connexion à la base de données
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
    $moments = ['Aurore', 'Midi', 'Crepuscule', 'Nuit'];

    $idUser = $_SESSION['user']['id'];
    $idMaitre = $_SESSION['user']['id'];

    // Vérifie s'il a déjà créé une communauté
    $stmt = $dbh->prepare("SELECT COUNT(*) FROM communaute WHERE id_maitre = ?");
    $stmt->execute([$idUser]);
    $dejaCree = $stmt->fetchColumn();

    if ($dejaCree > 0) {
        $_SESSION['alerte_communaute'] = "Vous avez déjà créé une communauté.";
        header('Location: index.php?page=communaute');
        exit;
    }


    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        $nom = $_POST['nom'];
        $langue = $_POST['langue'];
        $styleCommunautaire = $_POST['styleCommunautaire'];
        $styleGenreUn = $_POST['styleGenreUn'];
        $styleGenreDeux = $_POST['styleGenreDeux'];
        $styleGenreTrois = $_POST['styleGenreTrois'];
        $description = $_POST['description'];

        // Image de profil de la communauté
        $imageName = null;

        if (isset($_FILES['imageProfil']) && $_FILES['imageProfil']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../assets/images/';
            $fileTmpPath = $_FILES['imageProfil']['tmp_name'];
            $originalName = basename($_FILES['imageProfil']['name']);

            $extension = pathinfo($originalName, PATHINFO_EXTENSION);
            $imageName = time() . '_' . uniqid() . '.' . $extension;

            $destPath = $uploadDir . $imageName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // Image bien enregistrée
            }
        }

    // Insérer la communauté dans la base
    $stmt = $dbh->prepare("INSERT INTO communaute (nom, langue, styleCommunautaire, styleGenreUn, styleGenreDeux, styleGenreTrois, membres, description, imageProfil, id_maitre) VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?, ?)");
    $stmt->execute([$nom, $langue, $styleCommunautaire, $styleGenreUn, $styleGenreDeux, $styleGenreTrois, $description, $imageName, $idUser]);

    $idCommunaute = $dbh->lastInsertId(); 

    $stmt = $dbh->prepare("INSERT INTO utilisateur_communaute (id_utilisateur, id_communaute) VALUES (?, ?)");
    $stmt->execute([$idUser, $idCommunaute]);


    // Mise à jour des disponibilités

    $dispos = $_POST['disponibilites'] ?? [];
    foreach ($jours as $jour) {
        foreach ($moments as $moment) {
            $value = isset($dispos[$jour][$moment]) && $dispos[$jour][$moment] == '1' ? 1 : 0;
            $stmt = $dbh->prepare("
                INSERT INTO disponibilites_communaute (id_communaute, jour, moment, disponible)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE disponible = VALUES(disponible)
            ");
            $stmt->execute([$idCommunaute, $jour, $moment, $value]);
        }
    }

    header('Location: index.php?page=communaute');
    exit;
}
?>
<div class="profil-modifier">
    <p>Communauté > Créer une communauté</p>

    <form class="profil-formulaire" method="post" enctype="multipart/form-data">
        <!-- Image -->
        <div class="profil-photo">
            <img src="Site/assets/images/defaultCommunity.png" 
                class="photo-image" 
                alt="Image communauté" 
                style="width: 200px; height: 200px; object-fit: cover; display: block;">
            <input type="file" name="imageProfil" id="imageProfil" accept="image/*" style="display: none;">
            <button type="button" class="photo-modifier-btn" onclick="document.getElementById('imageProfil').click();">
                <img src="Site/assets/images/buttonImage.png" alt="Ajouter l'image">
            </button>
        </div>

        <!-- Champs -->
        <div class="profil-champs">
            <div class="champ">
                <label for="nom" class="champ-label">Nom de la communauté</label>
                <input type="text" id="nom" name="nom" class="champ-input" required>
            </div>

            <div class="champ">
                <label for="langue" class="champ-label">Langue</label>
                <select id="langue" name="langue" class="champ-input">
                    <option value="Français">Français</option>
                    <option value="Anglais">Anglais</option>
                    <option value="Espagnol">Espagnol</option>
                </select>
            </div>

            <div class="champ">
                <label for="styleCommunautaire" class="champ-label">Style communautaire</label>
                <select id="styleCommunautaire" name="styleCommunautaire" class="champ-input">
                    <?php
                    $styles = ['Casual', 'Challenger', 'Hardcore', 'Compétitif'];
                    foreach ($styles as $style) {
                        echo "<option value=\"$style\">$style</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="champ">
                <label for="styleGenreUn" class="champ-label">Genre principal</label>
                <select id="styleGenreUn" name="styleGenreUn" class="champ-input">
                    <?php
                    $genres = ['Action', 'Aventure', 'FPS', 'RPG', 'Culture', 'Course', 'Gestion', 'MMORPG'];
                    foreach ($genres as $genre) {
                        echo "<option value=\"$genre\">$genre</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="champ">
                <label for="styleGenreDeux" class="champ-label">Genre secondaire</label>
                <select id="styleGenreDeux" name="styleGenreDeux" class="champ-input">
                    <?php foreach ($genres as $genre) {
                        echo "<option value=\"$genre\">$genre</option>";
                    } ?>
                </select>
            </div>

            <div class="champ">
                <label for="styleGenreTrois" class="champ-label">Autre genre</label>
                <select id="styleGenreTrois" name="styleGenreTrois" class="champ-input">
                    <?php foreach ($genres as $genre) {
                        echo "<option value=\"$genre\">$genre</option>";
                    } ?>
                </select>
            </div>
        </div>

        <!-- Description -->
        <div class="profil-description">
            <label for="description" class="champ-label">Description</label>
            <textarea id="description" name="description" class="description-text" rows="6" required></textarea>
        </div>

        <!-- Disponibilité -->
        <div class="profil-disponibilite">
            <h2>Disponibilité</h2>
            <h4>Change les disponibilités en cliquant sur les icônes</h4>
            <div class="dispo-jours">
                <?php foreach ($jours as $jour): ?>
                <div class="dispo-jour">
                    <h3><?= $jour ?></h3>
                    <?php foreach ($moments as $moment): ?>
                        <?php
                            $isChecked = false;
                            // Récupération en BDD si nécessaire ici
                        ?>
                        <button type="button" class="dispo-btn" data-jour="<?= $jour ?>" data-moment="<?= $moment ?>">
                            <img src="Site/assets/images/Button<?= $moment ?><?= $isChecked ? '' : 'Invisible' ?>.png" alt="<?= $moment ?>">
                        </button>
                        <input type="hidden" name="disponibilites[<?= $jour ?>][<?= $moment ?>]" value="<?= $isChecked ? 1 : 0 ?>">
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Soumission -->
        <div class="profil-submit">
            <button type="submit" name="submit">Créer la communauté</button>
        </div>
    </form>
</div>
