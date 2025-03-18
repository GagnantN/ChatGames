<?php
// Connexion à la base de données avec PDO
$dsn = "mysql:host=localhost;dbname=projet_fil_rouge_gagnant;charset=utf8";
$username = "nico"; // Remplacez par votre utilisateur MySQL
$password = "wwwnico"; // Remplacez par votre mot de passe MySQL

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Échec de la connexion : " . $e->getMessage());
}

// Définition du nombre d'éléments par page
$elementsParPage = 5;

// Déterminer la page actuelle
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Calcul de l'offset
$offset = ($page - 1) * $elementsParPage;

// Récupération des éléments pour la page actuelle avec PDO
$sql = "SELECT * FROM PLAYER LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $elementsParPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll();

// Compter le nombre total d'articles pour la pagination
$sqlCount = "SELECT COUNT(*) AS total FROM PLAYER";
$totalElements = $pdo->query($sqlCount)->fetch()['total'];

// Calcul du nombre total de pages
$totalPages = ceil($totalElements / $elementsParPage);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagination avec PDO</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        .article {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            background: #f9f9f9;
        }
        .pagination {
            margin-top: 20px;
        }
        .pagination a {
            text-decoration: none;
            padding: 8px 12px;
            margin: 0 5px;
            border: 1px solid #007bff;
            color: #007bff;
            border-radius: 5px;
            transition: 0.3s;
        }
        .pagination a:hover {
            background: #007bff;
            color: white;
        }
        .disabled {
            pointer-events: none;
            color: gray;
            border-color: gray;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Liste des Articles</h2>
        
        <?php if (count($articles) > 0): ?>
            <?php foreach ($articles as $article): ?>
                <div class='article'>
                    <h3><?= htmlspecialchars($article['pseudo']) ?></h3>
                    <p><?= htmlspecialchars($article['image_profil']) ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun article trouvé.</p>
        <?php endif; ?>

        <!-- Pagination -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>">Précédent</a>
            <?php else: ?>
                <a class="disabled">Précédent</a>
            <?php endif; ?>

            <span>Page <?= $page ?> / <?= $totalPages ?></span>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>">Suivant</a>
            <?php else: ?>
                <a class="disabled">Suivant</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
