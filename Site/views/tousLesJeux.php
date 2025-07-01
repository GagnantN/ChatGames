<?php
require_once(__DIR__ . '../../bdd/db.php');

$search = $_GET['search'] ?? '';
$jeux = [];

// Si c'est une requête AJAX pour la recherche en temps réel
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' &&
    !empty($search)) {
    
    header('Content-Type: application/json');
    
    $query = "SELECT * FROM jeu WHERE nom LIKE :search ORDER BY nom ASC LIMIT 20";
    $stmt = $dbh->prepare($query);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($results);
    exit;
}

// Affichage standard (pas AJAX)
if (!empty($search)) {
    // Si on a une recherche, on filtre
    $stmt = $dbh->prepare("SELECT * FROM jeu WHERE nom LIKE :search ORDER BY nom ASC");
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
} else {
    // Sinon on affiche tous les jeux
    $stmt = $dbh->prepare("SELECT * FROM jeu ORDER BY nom ASC");
}

$stmt->execute();
$jeux = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="containerBody">
    <h1>Jeux disponibles</h1>
    
    <div class="recherche-bar">
        <form method="get" action="index.php">
            <input type="hidden" name="page" value="rechercheJeu">
            <input type="text" name="search" id="search-jeu" placeholder="Rechercher un jeu..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Rechercher</button>
        </form>
    </div>
    
    <div class="grid-cartes" id="resultats-jeux">
        <?php if (!empty($jeux)): ?>
            <?php foreach ($jeux as $jeu): ?>
                <div class="carte-jeu" data-nom="<?= strtolower(htmlspecialchars($jeu['nom'])) ?>">
                    <img src="<?= htmlspecialchars($jeu['images']) ?>" alt="<?= htmlspecialchars($jeu['nom']) ?>" class="image-jeu">
                    <div class="contenu-carte">
                        <h2><?= htmlspecialchars($jeu['nom']) ?></h2>
                        <a href="index.php?page=detailJeu&id=<?= $jeu['Id_Jeu'] ?>" class="btn-en-savoir-plus">En savoir plus sur le jeu</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php elseif (!empty($search)): ?>
            <p>Aucun jeu trouvé pour "<?= htmlspecialchars($search) ?>"</p>
        <?php endif; ?>
    </div>
</div>