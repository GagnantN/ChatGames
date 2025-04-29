<?php
// Inclusion de l'Header
include ('../includes/menu.php');


$page = $_GET['page'] ?? 'accueil';


// Chemin du fichier à inclure
$pageFile = $page . '.php';

// Vérifier si le fichier de la page demandée existe
if (file_exists($pageFile)) {
    include($pageFile); // Charger le contenu de la page demandée
} else {
    echo "<p>La page demandée n'existe pas.</p>"; // Message d'erreur si la page n'existe pas
}

// Inclusion du footer
//include ('../includes/footer.php');

?>