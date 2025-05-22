<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '../../bdd/db.php'); 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> <?php
        if (isset($_GET['page'])) {
            $page = htmlspecialchars($_GET['page']);

            switch ($page) {
                case 'Accueil':
                    echo "Accueil du site ChatGames";
                    break;
                case 'inscription':
                    echo "Inscription d'Utilisateur";
                    break;
                case 'deconnexion':
                    echo "Déconnexion";
                    break;
                case 'ListeConsole':
                    echo "Listes des Jeux Vidéos Console";
                    break;  
                case 'ListeMobile':
                    echo "Listes des Jeux Vidéos Mobile";
                    break;  
                case 'ListePhysique':
                    echo "Listes des Jeux Vidéos Physique";
                    break;      
                case 'jeu':
                    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                        require_once "../bdd/functions.php";
                        $jeu = getJeuById($_GET['id']);
                        echo $jeu ? "Fiche du Jeu : ".htmlspecialchars($jeu['nom']) : "Jeu introuvable - Mon Site";
                    } else {
                        echo "Fiche de Jeu : Vide";
                    }
                    break;
                case 'connexion':
                    echo "Connexion Utilisateur";
                    break;
                case 'PageUser':
                    echo "Profil Utilisateur";
                    break;
                case 'tendances':
                    echo "Tendances Futur mis à jour";
                    break;
                case 'precommandes':
                    echo "Précommandes Futur mis à jour";
                    break;
                case 'prochainSort':
                    echo "Prochaines sorties Futur mis à jour";
                    break;
                default:
                    echo "Accueil du site ChatGames";
                    break;
            }
        } else {
            echo "Accueil du site ChatGames";
        }
        ?></title>
    <link rel="stylesheet" href="Site/assets/css/style.css">
   

    <!-- Fonts Atkinson Import de Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
</head>
<body>
    <?php 
        $isConnected = isset($_SESSION['user']) && isset($_SESSION['user']['id_utilisateur']); 
        $disabledClass = !$isConnected ? 'disabled' : '';
    ?>

    <div class="layout">
        <nav class="navbar">
            <div class="containerMenu">
                <?php $currentPage = $_GET['page'] ?? 'accueil'; ?>
                <img src="Site/assets/images/logo.png" class="logo" alt="Logo du Site ChatGames">
                <a href="index.php?page=accueil" class="<?= $currentPage === 'accueil' ? 'active' : '' ?>"><img src="Site/assets/images/accueil.png" class="icones" alt="Accueil"> Accueil</a>
                <a href="<?= $isConnected ? 'index.php?page=rechercheAmis' : '#' ?>" class="<?= $currentPage === 'rechercheAmis' ? 'active' : '' ?> <?= $disabledClass ?>"><img src="Site/assets/images/amis.png" class="icones" alt="Amis"> Recherche amis</a>
                <a href="index.php?page=communaute" class="<?= $currentPage === 'communaute' ? 'active' : '' ?>"><img src="Site/assets/images/communauter.png" class="icones" alt="Communauté"> Communauté</a>
                <a href="<?= $isConnected ? 'index.php?page=messagerie' : '#' ?>" class="<?= $currentPage === 'messagerie' ? 'active' : '' ?>  <?= $disabledClass ?>"><img src="Site/assets/images/messagerie.png" class="icones" alt="Messagerie"> Messagerie</a>
                <a href="index.php?page=evenements" class="<?= $currentPage === 'evenements' ? 'active' : '' ?>"><img src="Site/assets/images/evenement.png" class="icones" alt="Événements"> Événements</a>
                <a href="index.php?page=stream" class="<?= $currentPage === 'stream' ? 'active' : '' ?>"><img src="Site/assets/images/stream.png" class="icones" alt="Stream"> Stream</a>
                <a href="<?= $isConnected ? 'index.php?page=deconnexion' : '#' ?>" class="<?= $currentPage === 'deconnexion' ? 'active' : '' ?> <?= $disabledClass ?>"><img src="Site/assets/images/deconnexion.png" class="icones" alt="Déconnexion"> Déconnexion </a>
            </div>
        </nav>
        <main class="content">
    

