<?php
require_once(__DIR__ . '../../bdd/db.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // on démarre la session AVANT tout traitement
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Sécurisation des entrées
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $pseudo = trim($_POST['pseudo']);
    $age = (int) $_POST['age'];
    $langue = $_POST['langue'];
    $styleJeu = $_POST['styleJeu'];

    $imageProfil = 'imageProfil.png';
    $description = 'Aucun';

    //  Vérification mot de passe
    if ($password !== $password_confirm) {
        $_SESSION['alerte_inscription'] = "Les mots de passe ne correspondent pas.";
        header("Location: index.php?page=inscription");
        exit;
    }

    // Vérifie si email déjà utilisé
    $stmt = $dbh->prepare("SELECT COUNT(*) FROM utilisateur WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['alerte_inscription'] = "Cette adresse e-mail est déjà utilisée.";
        header("Location: index.php?page=inscription");
        exit;
    }

    // Vérifie si le mot de passe est déjà dans la base (pas forcément utile mais tu l’as mis)
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $dbh->prepare("SELECT COUNT(*) FROM utilisateur WHERE password = ?");
    $stmt->execute([$password_hashed]);
    if ($stmt->fetchColumn() > 0) {
        $_SESSION['alerte_inscription'] = "Attention : ce mot de passe a déjà été utilisé.";
        header("Location: index.php?page=inscription");
        exit;
    }

    // Insertion
    try {
        $stmt = $dbh->prepare("
            INSERT INTO utilisateur (email, password, pseudo, age, langue, styleJeu, imageProfil, description)
            VALUES (:email, :password, :pseudo, :age, :langue, :styleJeu, :imageProfil, :description)
        ");
        $stmt->execute([
            ':email' => $email,
            ':password' => $password_hashed,
            ':pseudo' => $pseudo,
            ':age' => $age,
            ':langue' => $langue,
            ':styleJeu' => $styleJeu,
            ':imageProfil' => $imageProfil,
            ':description' => $description
        ]);
        header('Location: index.php?page=connexion');
        exit;
    } catch (PDOException $e) {
        $_SESSION['alerte_inscription'] = "Erreur serveur : " . $e->getMessage();
        header("Location: index.php?page=inscription");
        exit;
    }
}
?>

<div class="containerBody">

    <?php if (isset($_SESSION['alerte_inscription'])): ?>
        <div id="popup-overlay" class="popup-overlay" onclick="closePopup()">
            <div class="popup-message" onclick="event.stopPropagation()">
                <?= htmlspecialchars($_SESSION['alerte_inscription']) ?>
                <br><small>(Cliquez pour fermer)</small>
            </div>
        </div>
        <?php unset($_SESSION['alerte_inscription']); ?>
    <?php endif; ?>

    <h1>Inscription</h1>

    <form class="formulaire" method="post" enctype="multipart/form-data">

        <div class="input-group">
            <label for="email" class="group-label">E-mail</label>
            <input type="email" id="email" name="email" class="input" placeholder="Mettez votre e-mail" required>
        </div>

        <div class="input-group">
            <label for="password" class="group-label">Mot de passe</label>
            <input type="password" id="password" name="password" class="input" placeholder="Mettez votre mot de passe" required>
        </div>

        <div class="input-group">
            <label for="password_confirm" class="group-label">Confirmation Mot de passe</label>
            <input type="password" id="password_confirm" name="password_confirm" class="input" placeholder="Confirmez votre mot de passe" required>
        </div>

        <div class="input-group">
            <label for="pseudo" class="group-label">Pseudo utilisateur</label>
            <input type="text" id="pseudo" name="pseudo" class="input" placeholder="Mettez votre pseudo" required>
        </div>

        <div class="input-group">
            <label for="age" class="champ-label">Date de naissance</label>
            <input type="date" id="age" name="age" class="champ-input" required
                value="<?= htmlspecialchars($_SESSION['user']['age'] ?? '') ?>">
        </div>

        <div class="input-group">
            <label for="langue" class="group-label">Langue</label>
            <select id="langue" name="langue">
                <option value="">-- Sélectionnez votre langue  --</option>
                <option value="français">Français</option>
                <option value="anglais">Anglais</option>
                <option value="espagnol">Espagnol</option>
            </select>
        </div>

        <div class="input-group">
            <label for="styleJeu" class="group-label">Style de jeu</label>
            <select id="styleJeu" name="styleJeu">
                <option value="">-- Sélectionnez votre style de jeu --</option>
                <option value="casual">Casual</option>
                <option value="regular">Regular</option>
                <option value="challenger">Challenger</option>
                <option value="hardcore">Hardcore</option>
                <option value="achivementH">Achivement Hunter</option>
                <option value="competitive">Competitive</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" name="submit" href="index.php?page=accueil">Inscription</button>
        </div>
    </form>

</div>