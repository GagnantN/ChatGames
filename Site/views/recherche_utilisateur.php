<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '/../bdd/db.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode([]);
    exit;
}

$userId = $_SESSION['user']['id'];
$search = $_GET['search'] ?? '';

if (empty($search)) {
    echo json_encode([]);
    exit;
}

$query = "SELECT id_utilisateur, pseudo, imageProfil, genreJeu, support 
          FROM utilisateur 
          WHERE id_utilisateur != :userId 
          AND pseudo LIKE :search
          ORDER BY RAND()
          LIMIT 20";

$stmt = $dbh->prepare($query);
$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);
?>