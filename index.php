<?php

$page = $_GET['page'] ?? 'accueil';

// Inclusion de la sidebar
include('Site/includes/menu.php');

// Chemin du fichier à inclure
$pageFile = __DIR__ . '/Site/views/' . $page . '.php';

// Vérifier si le fichier de la page demandée existe
if (file_exists($pageFile)) {
    include($pageFile); // Charger le contenu de la page demandée
} else {
    echo "<p>La page demandée n'existe pas.</p>"; // Message d'erreur si la page n'existe pas
}

// Inclusion du footer non apparant
include('Site/includes/footer.php');
?>