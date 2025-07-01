<?php
require_once(__DIR__ . '../../bdd/db.php'); 

$jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
$moments = ['Aurore', 'Midi', 'Crepuscule', 'Nuit'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $pseudo = $_POST['pseudo'];
    $langue = $_POST['langue'];
    $age = $_POST['age'];
    $styleJeu = $_POST['styleJeu'];
    $genreJeu = $_POST['genreJeu'];
    $support = $_POST['support'];
    $description = $_POST['description'];
    $userId = $_SESSION['user']['id'];

    // Valeur par défaut si aucune image n’est uploadée
    $imageName = $_SESSION['user']['imageProfil'] ?? null;
    $hasNewImage = false;

    if (isset($_FILES['imageProfil']) && $_FILES['imageProfil']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../assets/images/';
        $fileTmpPath = $_FILES['imageProfil']['tmp_name'];
        $originalName = basename($_FILES['imageProfil']['name']);

        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $imageName = time() . '_' . uniqid() . '.' . $extension;

        $destPath = $uploadDir . $imageName;

        // Supprimer ancienne image si existante
        if (!empty($_SESSION['user']['imageProfil'])) {
            $oldPath = $uploadDir . $_SESSION['user']['imageProfil'];
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        echo "Upload Dir: $uploadDir<br>";
        echo "Temp File: $fileTmpPath<br>";
        echo "Dest Path: $destPath<br>";

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $hasNewImage = true;
            $_SESSION['user']['imageProfil'] = $imageName;
        }
    }

    // Mise à jour globale, y compris l’image
        if ($hasNewImage) {
        $stmt = $dbh->prepare("UPDATE utilisateur SET pseudo = ?, langue = ?, age = ?, styleJeu = ?, genreJeu = ?, support = ?, description = ?, imageProfil = ? WHERE id_utilisateur = ?");
        $stmt->execute([$pseudo, $langue, $age, $styleJeu, $genreJeu, $support, $description, $imageName, $userId]);
    } else {
        $stmt = $dbh->prepare("UPDATE utilisateur SET pseudo = ?, langue = ?, age = ?, styleJeu = ?, genreJeu = ?, support = ?, description = ? WHERE id_utilisateur = ?");
        $stmt->execute([$pseudo, $langue, $age, $styleJeu, $genreJeu, $support, $description, $userId]);
    }

    // Met à jour la session
    $_SESSION['user']['pseudo'] = $pseudo;
    $_SESSION['user']['langue'] = $langue;
    $_SESSION['user']['age'] = $age;
    $_SESSION['user']['styleJeu'] = $styleJeu;
    $_SESSION['user']['genreJeu'] = $genreJeu;
    $_SESSION['user']['support'] = $support;
    $_SESSION['user']['description'] = $description;
    $_SESSION['user']['imageProfil'] = $imageName;

    // Mise à jour des disponibilités
    $dispos = $_POST['disponibilites'] ?? [];
    foreach ($jours as $jour) {
        foreach ($moments as $moment) {
            $value = isset($dispos[$jour][$moment]) && $dispos[$jour][$moment] == '1' ? 1 : 0;
            $stmt = $dbh->prepare("
                INSERT INTO disponibilites (id_utilisateur, jour, moment, disponible)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE disponible = VALUES(disponible)
            ");
            $stmt->execute([$userId, $jour, $moment, $value]);
        }
    }

    header('Location: index.php?page=profil');
    exit;
}
?>


<div class="profil-modifier">
    <p>Mon profil > Modifier mon profil</p>

    <form class="profil-formulaire" method="post" enctype="multipart/form-data">
        <!-- Partie image dans le formulaire -->
        <div class="profil-photo">
            <img src="Site/assets/images/<?= htmlspecialchars($_SESSION['user']['imageProfil']) ?>" 
                class="photo-image" 
                alt="Image de profil" 
                style="width: 200px; height: 200px; object-fit: cover; display: block;">

            <!-- Champ file masqué -->
            <input type="file" name="imageProfil" id="imageProfil" accept="image/*" style="display: none;">

            <!-- Bouton stylisé pour déclencher -->
            <button type="button" class="photo-modifier-btn" onclick="document.getElementById('imageProfil').click();">
                <img src="Site/assets/images/buttonImage.png" alt="Modifier l'image">
            </button>
        </div>

        <div class="profil-champs">
            <div class="champ">
                <label for="pseudo" class="champ-label">Pseudo d'utilisateur</label>
                <input type="text" id="pseudo" name="pseudo" class="champ-input" required
                    value="<?= htmlspecialchars($_SESSION['user']['pseudo'] ?? '') ?>">
            </div>

            <div class="champ">
                <label for="langue" class="champ-label">Langue</label>
                <select id="langue" name="langue" class="champ-input">
                <option value="Français" <?= ($_SESSION['user']['langue'] ?? '') === 'Français' ? 'selected' : '' ?>>Français</option>
                <option value="Anglais" <?= ($_SESSION['user']['langue'] ?? '') === 'Anglais' ? 'selected' : '' ?>>Anglais</option>
                <option value="Espagnol" <?= ($_SESSION['user']['langue'] ?? '') === 'Espagnol' ? 'selected' : '' ?>>Espagnol</option>
                </select>
            </div>

            <div class="champ">
                <label for="age" class="champ-label">Date de naissance</label>
                <input type="date" id="age" name="age" class="champ-input" required
                    value="<?= htmlspecialchars($_SESSION['user']['age'] ?? '') ?>">
            </div>

            <div class="champ">
                <label for="styleJeu" class="champ-label">Style de jeu</label>
                <select id="styleJeu" name="styleJeu" class="champ-input">
                <?php
                $styles = ['Casual', 'Regular', 'Challenger', 'Hardcore', 'Achivement', 'Competitive'];
                foreach ($styles as $style) {
                    $selected = ($_SESSION['user']['style_jeu'] ?? '') === $style ? 'selected' : '';
                    echo "<option value=\"$style\" $selected>$style</option>";
                }
                ?>
                </select>
            </div>

            <div class="champ">
                <label for="genreJeu" class="champ-label">Genre de jeu joué</label>
                <select id="genreJeu" name="genreJeu" class="champ-input">
                <?php
                $genres = ['Action', 'Aventure', 'FPS', 'RPG', 'Familliale', 'Course'];
                foreach ($genres as $genre) {
                    $selected = ($_SESSION['user']['genre_jeu'] ?? '') === $genre ? 'selected' : '';
                    echo "<option value=\"$genre\" $selected>$genre</option>";
                }
                ?>
                </select>
            </div>

            <div class="champ">
                <label for="support" class="champ-label">Genre de jeu joué</label>
                <select id="support" name="support" class="champ-input">
                <?php
                $supports = ['PC', 'XBOX', 'PS5', 'PS4', 'Switch', 'Switch 2'];
                foreach ($supports as $support) {
                    $selected = ($_SESSION['user']['support'] ?? '') === $support ? 'selected' : '';
                    echo "<option value=\"$support\" $selected>$support</option>";
                }
                ?>
                </select>
            </div>
        </div>

        <div class="profil-description">
        <label for="description" class="champ-label">Description</label>
        <textarea id="description" name="description" class="description-text" rows="6" required><?= htmlspecialchars($_SESSION['user']['description'] ?? '') ?></textarea>
        </div>

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


        <div class="profil-submit">
        <button type="submit" name="submit">Enregistrer</button>
        </div>
    </form>
</div>
