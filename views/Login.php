<?php
require_once '../bdd/functions.php'; // Fichier contenant la connexion à la base

if (isset($_SESSION["id_user"])) {
    redirect ('index.php?page=PageUser');
    exit();
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $stmt = $dbh->prepare("SELECT * FROM USER WHERE mail = :mail");
        $stmt->execute([':mail' => $_POST['mail']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user && password_verify($_POST['password'], $user['password'])) {
            $_SESSION['id_user'] = $user['id'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['mail'] = $user['mail'];
            $_SESSION['pseudo'] = $user['pseudo'];
            $_SESSION['image_profil'] = $user['image_profil'];

            redirect('index.php?page=Accueil');
        } else {
            $error = "Identifiants incorrects.";
        }
    }
}

?>

<section>
    <h1>Connexion au compte utilisateur.</h1>
    <div class="login">
        <form method="POST">
            <label>Entrez votre email : <input type="text" name="mail" placeholder="Email (obligatoire)" required></label>
            <label>Entrez votre mot de passe : <input type="password" name="password" placeholder="Mot de passe (obligatoire)" required></label>
            <button type="submit">Se connecter</button>
            <button><a href="index.php?page=Formulaire">Inscription</a></button> 
        </form>
        <?php if (!empty($error)) { echo "<h2 style='color:red;'>$error</h2>"; } ?>
    </div>
</section>
