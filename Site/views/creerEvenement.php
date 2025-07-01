<?php
require_once(__DIR__ . '../../bdd/db.php'); 
if (session_status() === PHP_SESSION_NONE) session_start();

$idUser = $_SESSION['user']['id'] ?? null;

// Vérifie que l'utilisateur est maître
$stmt = $dbh->prepare("SELECT COUNT(*) FROM communaute WHERE id_maitre = ?");
$stmt->execute([$idUser]);
$isMaitre = $stmt->fetchColumn() > 0;

if (!$isMaitre) {
    header('Location: index.php?page=evenements');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $nom = $_POST['nom'];
    $theme = $_POST['theme'];
    $date = $_POST['date'];
    $heure = $_POST['heure'];
    $description = $_POST['description'];
    $imageName = null;

    if (isset($_FILES['imageProfil']) && $_FILES['imageProfil']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../assets/images/';
        $fileTmpPath = $_FILES['imageProfil']['tmp_name'];
        $originalName = basename($_FILES['imageProfil']['name']);
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $imageName = time() . '_' . uniqid() . '.' . $extension;
        $destPath = $uploadDir . $imageName;
        move_uploaded_file($fileTmpPath, $destPath);
    }

        // Récupère la communauté de l'utilisateur (maître)
    $stmt = $dbh->prepare("SELECT id_communaute FROM communaute WHERE id_maitre = ?");
    $stmt->execute([$idUser]);
    $idCommunaute = $stmt->fetchColumn();

    // L'utilisateur connecté est le maître, donc :
    $idMaitre = $idUser;

    $stmt = $dbh->prepare("INSERT INTO evenements (nom, theme, date_event, heure_event, imageProfil, description, id_maitre, id_communaute, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");

    $stmt->execute([$nom, $theme, $date, $heure, $imageName, $description, $idMaitre, $idCommunaute]);


    header('Location: index.php?page=evenements');
    exit;
}
?>

<div class="profil-modifier">
    <p>Événement > Créer un événement</p>

    <form class="profil-formulaire" method="post" enctype="multipart/form-data">
        <div class="profil-photo">
            <img src="Site/assets/images/defaultEvent.png"
                class="photo-image" 
                alt="Image événement" 
                style="width: 200px; height: 200px; object-fit: cover;">
            <input type="file" name="imageProfil" id="imageProfil" accept="image/*" style="display: none;">
            <button type="button" class="photo-modifier-btn" onclick="document.getElementById('imageProfil').click();">
                <img src="Site/assets/images/buttonImage.png" alt="Ajouter l'image">
            </button>
        </div>

        <div class="profil-champs">
            <div class="champ">
                <label for="nom" class="champ-label">Nom de l'événement</label>
                <input type="text" id="nom" name="nom" class="champ-input" required>
            </div>

            <div class="champ">
                <label for="theme" class="champ-label">Thème</label>
                <select id="theme" name="theme" class="champ-input" required>
                    <option value="Tournois">Tournois</option>
                    <option value="Compétition">Compétition</option>
                    <option value="Escarmouche">Escarmouche</option>
                </select>
            </div>

            <div class="champ">
                <label for="date" class="champ-label">Date</label>
                <input type="date" id="date" name="date" class="champ-input" required>
            </div>

            <div class="champ">
                <label for="heure" class="champ-label">Heure</label>
                <input type="time" id="heure" name="heure" class="champ-input" required>
            </div>
        </div>

        <div class="profil-description">
            <label for="description" class="champ-label">Description</label>
            <textarea id="description" name="description" class="description-text" rows="6" required></textarea>
        </div>

        <div class="profil-submit">
            <button type="submit" name="submit">Créer l'événement</button>
        </div>
    </form>
</div>
