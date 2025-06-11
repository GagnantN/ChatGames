<?php

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