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
                    echo "Accueil du site Gaming Gagnant";
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
            echo "Accueil - Mon Site";
        }
        ?></title>
    <link rel="stylesheet" href="../assets/css/stylePC.css">
   

    <!-- Fonts Atkinson Import de Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Atkinson+Hyperlegible:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class ="container">
            <a href="index.php?page=Accueil"><img src="../assets/images/logo.png" class="logo" alt="Photo du Site Direction Accueil"></a>
            <a> <img src="../assets/images/recherche.png" class="recherche" alt="Barre de recherche"></a>
            <a href="index.php?page=Login"><img src="../assets/images/profil.png" class="profil" alt="Profil Utilisateur pour se connecter ou s'inscrire."></a>
        </div>
    </header>
    <navbar>
        <div class ="container">
            <ul>
                <li><a href="index.php?page=ListePC"><img src="../assets/images/ordi.png" class="logoElement" alt="Liste de Jeu Ordinateur">PC</a></li>
                <li><a href="index.php?page=ListeConsole"><img src="../assets/images/console.png" class="logoElement" alt="Liste de Jeu Ordinateur">Console</a></li>
                <li><a href="index.php?page=ListeMobile"><img src="../assets/images/mobile.png" class="logoElement" alt="Liste de Jeu Ordinateur">Mobile</a></li>
                <li><a href="index.php?page=ListePhysique"><img src="../assets/images/de.png" class="logoElement" alt="Liste de Jeu Ordinateur">Jeux Physique</a></li>
            </ul>
        </div>
    </navbar>

    <section>