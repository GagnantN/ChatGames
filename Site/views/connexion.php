<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '../../bdd/db.php'); // Connexion à la BDD

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sécurisation des données
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Vérifier que l'utilisateur existe
    $stmt = $dbh->prepare("SELECT * FROM utilisateur WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    // CORRECTION ICI : utiliser $user et pas $utilisateur
    if ($user && password_verify($password, $user['password'])) {
        // Connexion réussie : stocker les infos en session
        $_SESSION['user'] = [
            'id' => $user['id_utilisateur'],
            'pseudo' => $user['pseudo'],
            'imageProfil' => $user['imageProfil'],
            'description' => $user['description'],
            'styleJeu' => $user['styleJeu']
        ];

        header('Location: index.php?page=accueil');
        exit;
    } else {
        $erreur = "E-mail ou mot de passe incorrect.";
    }
}
?>

<div class="containerBody">
    <h1>Connexion</h1>

    <?php if (!empty($erreur)) : ?>
        <p style="color: red;"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>

    <form class="formulaire" method="post">
        <div class="input-group">
            <label for="email" class="group-label">E-mail</label>
            <input type="email" id="email" name="email" class="input" placeholder="Mettez votre e-mail" required>
        </div>

        <div class="input-group">
            <label for="password" class="group-label">Mot de passe</label>
            <input type="password" id="password" name="password" class="input" placeholder="Mettez votre mot de passe" required>
        </div>

        <div class="form-actions">
            <button type="submit">Connexion</button>
        </div>
    </form>
</div>
