<?php
require_once '../bdd/functions.php';
// On vide les données de session
// On redirige vers la page de connexion
echo "<section>";
    session_destroy();
    redirect('index.php?page=Login');
    exit;
echo "</section>";

?>
