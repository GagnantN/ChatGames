<?php
require_once(__DIR__ . '../../bdd/functions.php'); // Fichier contenant la connexion à la base

if (isset($_SESSION["id"])) {
    redirect ('index.php?page=inscription');
    exit();
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $stmt = $dbh->prepare("SELECT * FROM utilisateur WHERE email = :email");
        $stmt->execute([':email' => $_POST['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user && password_verify($_POST['password'], $user['password'])) {
            $_SESSION['id_Utilisateur'] = $user['id_Utilisateur'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['adresse'] = $user['adresse'];
            $_SESSION['adresse_postal'] = $user['adresse_postal'];
            $_SESSION['mail'] = $user['mail'];

            redirect('index.php?page=accueil');
        } else {
            $error = "Identifiants incorrects.";
        }
    }
}
?>
<div class="containerBody">
    <h1>Connexion</h1>

    <form class="formulaire" method="post">
        <div class="input-group">
            <input type="text" name="username" placeholder=" " required>
            <label>Nom utilisateur</label>
        </div>

        <div class="input-group">
            <input type="password" name="password" placeholder=" " required>
            <label>Mot de passe</label>
        </div>

        <div class="form-actions">
            <button type="submit">Connexion</button>
        </div>
    </form>

    

</div>