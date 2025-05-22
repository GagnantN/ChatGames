<?php
require_once(__DIR__ . '../../bdd/db.php');

// Supprimer toutes les variables de session
$_SESSION = [];

// Détruire la session
session_destroy();

// Redirection vers l'accueil
header('Location: index.php?page=accueil');
exit;
?>