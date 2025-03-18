<?php

require_once '../bdd/functions.php';

// Si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $dbh->prepare("INSERT INTO USER (nom, prenom, mail, password) VALUES (:nom, :prenom, :mail, :password)");
    $stmt->execute([
        ':nom' => $_POST['nom'],
        ':prenom' => $_POST['prenom'],
        ':mail' => $_POST['mail'],
        ':password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
    ]);

    // Récupérer l'ID de l'utilisateur nouvellement inséré
    $stmt = $dbh->prepare("SELECT id FROM USER WHERE mail = :mail");
    $stmt->execute([':mail' => $_POST['mail']]);
    $userID = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si l'utilisateur a été trouvé
    if ($userID) {
        // Insérer le joueur dans la table PLAYER en reliant avec l'ID utilisateur
        $stmt = $dbh->prepare("INSERT INTO PLAYER (pseudo, id_user, image_profil) VALUES (:pseudo, :id_user, :image_profil)");
        $stmt->execute([
            ':pseudo' => $_POST['pseudo'],
            ':id_user' => $userID["id"],  
            ':image_profil' => $_POST['image_profil'],
        ]);
    }
    
    redirect('index.php?page=Login');
}
?>

<section>
    <br />
    <h1>Inscription</h1>
    <br />

    <div class="inscription">
        <form method="POST">
            <label>Entrez votre nom :<input type="text" name="nom" placeholder="Nom"></label>
            <label>Entrez votre prenom :<input type="text" name="prenom" placeholder="Prénom"></label>
            <label>Entrez votre email :<input type="email" name="mail" placeholder="Email (obligatoire)" required></label>
            <label>Entrez votre pseudo :<input type="text" name="pseudo" placeholder="Pseudo (obligatoire)" required></label>
            <label>Entrez votre mot de passe :<input type="password" name="password" placeholder="Mot de passe (obligatoire)" required></label>
            <label>Entrez votre image de profil :<input type="text" name="image_profil" placeholder="Url de l'Image de Profil"></label>
            <button type="submit">S'inscrire</button>
        </form>
    </div>
</section>