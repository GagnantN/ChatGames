<?php
require_once(__DIR__ . '../../bdd/db.php'); // Connexion à la BDD

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Sécurisation des entrées
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $pseudo = trim($_POST['pseudo']);
    $age = (int) $_POST['age'];
    $langue = $_POST['langue'];
    $styleJeu = $_POST['styleJeu'];

    // Valeurs par défaut
    $imageProfil = 'imageProfil.png';
    $description = 'Aucun';

    // Vérification mot de passe
    if ($password !== $password_confirm) {
        echo "Les mots de passe ne correspondent pas.";
        exit;
    }

    // Hash du mot de passe
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Préparation et insertion dans la base
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
        echo "Erreur : " . $e->getMessage();
    }
}
?>

<div class="containerBody">
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
            <label for="age" class="group-label">Age</label>
            <input type="number" id="age" name="age" class="input" placeholder="Mettez votre age" required>
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