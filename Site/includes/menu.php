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
                case 'Formulaire':
                    echo "Inscription d'Utilisateur";
                    break;
                case 'ListePC':
                    echo "Listes des Jeux Vidéos PC";
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
                case 'Login':
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
                    echo "Page inconnue - Mon Site";
                    break;
            }
        } else {
            echo "Accueil du site ChatGames";
        }
        ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
   

    <!-- Fonts Atkinson Import de Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">
</head>
<body>
    <navbar>
        <div class ="container">
            <a><img src="../assets/images/logo.png" class="logo" alt="Logo du Site ChatGames"></a>
            <a href="index.php?page=accueil"><img src="../assets/images/accueil.png" class="icones" alt="Logo direction accueil">> Accueil</a>
            <a href="index.php?page=rechercheAmis"><img src="../assets/images/amis.png" class="icones" alt="Logo direction la recherche d'amis">> Recherche amis</a>
            <a href="index.php?page=communaute"><img src="../assets/images/communauter.png" class="icones" alt="Logo direction la communauté">> Communauté</a>
            <a href="index.php?page=messagerie"><img src="../assets/images/messagerie.png" class="icones" alt="Logo direction la messagerie">> Messagerie</a>
            <a href="index.php?page=evenements"><img src="../assets/images/evenement.png" class="icones" alt="Logo direction les évenements">> Évenements</a>
            <a href="index.php?page=stream"><img src="../assets/images/stream.png" class="icones" alt="Logo direction sur le stream">> Stream</a>
        </div>
    </navbar>
    

    <section>