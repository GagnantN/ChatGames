<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once(__DIR__ . '../../bdd/db.php');

    $isConnected = isset($_SESSION['user']);
    $search = $_GET['search'] ?? '';

    // Variables pour les filtres
    $genre = $_GET['genre'] ?? '';
    $membres_min = $_GET['membres_min'] ?? '';
    $membres_max = $_GET['membres_max'] ?? '';

    $communautes = [];
    $debugInfo = []; // Pour le débogage

    // AJOUT DE DEBUG : Vérifier si c'est une requête AJAX
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    
    // DEBUG : Logger les informations
    error_log("DEBUG COMMUNAUTE - AJAX: " . ($isAjax ? 'OUI' : 'NON'));
    error_log("DEBUG COMMUNAUTE - Search: '$search'");
    error_log("DEBUG COMMUNAUTE - Headers: " . print_r($_SERVER, true));

    // Si c'est une requête AJAX pour la recherche en temps réel
    if ($isAjax && !empty($search)) {
        
        header('Content-Type: application/json');
        
        try {
            // Requête pour rechercher les communautés
            $query = "SELECT id_communaute, nom, description, imageProfil, styleGenreUn, styleGenreDeux, styleGenreTrois,
                        (SELECT COUNT(*) FROM utilisateur_communaute WHERE id_communaute = communaute.id_communaute) as membres
                FROM communaute 
                WHERE nom LIKE :search 
                   OR description LIKE :search
                   OR styleGenreUn LIKE :search
                   OR styleGenreDeux LIKE :search
                   OR styleGenreTrois LIKE :search
                ORDER BY nom
                LIMIT 20";

            $stmt = $dbh->prepare($query);
            $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("DEBUG COMMUNAUTE - Résultats trouvés: " . count($results));
            
            echo json_encode($results);
            exit;
            
        } catch (Exception $e) {
            error_log("DEBUG COMMUNAUTE - Erreur SQL: " . $e->getMessage());
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }

    // Logique normale pour l'affichage de la page
    try {
        if (!empty($search)) {
            // Recherche avec terme de recherche
            $query = "SELECT id_communaute, nom, description, imageProfil, styleGenreUn, styleGenreDeux, styleGenreTrois,
                        (SELECT COUNT(*) FROM utilisateur_communaute WHERE id_communaute = communaute.id_communaute) as membres
                FROM communaute 
                WHERE nom LIKE :search 
                   OR description LIKE :search
                   OR styleGenreUn LIKE :search
                   OR styleGenreDeux LIKE :search
                   OR styleGenreTrois LIKE :search
                ORDER BY nom";

            $stmt = $dbh->prepare($query);
            $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            $stmt->execute();
            $communautes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("DEBUG COMMUNAUTE - Recherche normale, résultats: " . count($communautes));
            
        } else {
            // Affichage de toutes les communautés (logique existante)
            $conditions = [];
            $params = [];

            // Ajouter des filtres si nécessaire
            if (!empty($genre)) {
                $conditions[] = "(styleGenreUn LIKE ? OR styleGenreDeux LIKE ? OR styleGenreTrois LIKE ?)";
                $params[] = "%$genre%";
                $params[] = "%$genre%";
                $params[] = "%$genre%";
            }

            if (!empty($membres_min) && is_numeric($membres_min)) {
                $conditions[] = "(SELECT COUNT(*) FROM utilisateur_communaute WHERE id_communaute = communaute.id_communaute) >= ?";
                $params[] = (int)$membres_min;
            }

            if (!empty($membres_max) && is_numeric($membres_max)) {
                $conditions[] = "(SELECT COUNT(*) FROM utilisateur_communaute WHERE id_communaute = communaute.id_communaute) <= ?";
                $params[] = (int)$membres_max;
            }

            $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
            
            $sql = "SELECT id_communaute, nom, description, imageProfil, styleGenreUn, styleGenreDeux, styleGenreTrois,
                        (SELECT COUNT(*) FROM utilisateur_communaute WHERE id_communaute = communaute.id_communaute) as membres
                    FROM communaute 
                    $whereClause
                    ORDER BY nom";

            $stmt = $dbh->prepare($sql);
            $stmt->execute($params);
            $communautes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("DEBUG COMMUNAUTE - Affichage normal, résultats: " . count($communautes));
        }
        
    } catch (Exception $e) {
        error_log("DEBUG COMMUNAUTE - Erreur générale: " . $e->getMessage());
        $communautes = [];
    }

    // Fonction pour obtenir les disponibilités
    function getDisponibilitesCommunaute($dbh, $idCommunaute) {
        return [];
    }
?>

<div class="containerBody">
    <?php if (isset($_SESSION['alerte_communaute'])): ?>
        <div id="popup-overlay" class="popup-overlay" onclick="closePopup()">
            <div class="popup-message">
                <?= htmlspecialchars($_SESSION['alerte_communaute']) ?>
                <br><small>(Cliquez pour fermer)</small>
            </div>
        </div>
        <?php unset($_SESSION['alerte_communaute']); ?>
    <?php endif; ?>

    <div class="headerAccueil">
        <h1>Communauté</h1>
        <div class="buttonAccueil">
            <?php if (!$isConnected): ?>
                <a href="index.php?page=inscription">Inscription</a>
                <a href="index.php?page=connexion">Connexion</a>
            <?php else: ?>
                <a href="index.php?page=profil">
                    <img src="Site/assets/images/<?= htmlspecialchars($_SESSION['user']['imageProfil']) ?>">
                    <?= htmlspecialchars($_SESSION['user']['pseudo']) ?>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="top-bar">
        <button class="join-btn"><a href="index.php?page=creerCommunaute">Créer +</a></button>
        <button class="filter-btn" onclick="ouvrirFiltreCommunaute()">Filtres</button>
    </div>

    <!-- Popup filtre -->
    <div id="overlay-filtre-communaute" class="overlay hidden"></div>

    <div id="modal-filtre-communaute" class="modal-filtre hidden">
        <div class="modal-header">
            <h2>Filtrer les communautés</h2>
            <button class="btn-close" onclick="fermerFiltreCommunaute()">×</button>
        </div>
        <form method="get" action="index.php">
            <input type="hidden" name="page" value="communaute">

            <label>Genre de jeu :
                <input type="text" name="genre" placeholder="FPS, RPG, Action..." value="<?= htmlspecialchars($genre) ?>">
            </label>

            <label>Nombre de membres minimum :
                <input type="number" name="membres_min" placeholder="5" value="<?= htmlspecialchars($membres_min) ?>">
            </label>

            <label>Nombre de membres maximum :
                <input type="number" name="membres_max" placeholder="50" value="<?= htmlspecialchars($membres_max) ?>">
            </label>

            <button type="submit" class="btn-enregistrer">Appliquer les filtres</button>
        </form>
    </div>

    <div class="team-grid" id="resultats-communautes">
        <?php if (!empty($communautes)): ?>
            <?php foreach ($communautes as $commu): 
                $disponibilites = getDisponibilitesCommunaute($dbh, $commu['id_communaute']);
            ?>
                <div class="team-card">
                    <img src="Site/assets/images/<?= htmlspecialchars($commu['imageProfil']) ?>" alt="Image" class="team-img">
                    <div class="team-content">
                        <div class="team-header">
                            <h2><?= htmlspecialchars($commu['nom']) ?></h2>
                            <span class="member-count"><img src="Site/assets/images/users-group.png"><?= (int)$commu['membres'] ?></span>
                        </div>
                        <p class="slogan">" <?= htmlspecialchars($commu['description']) ?> "</p>
                        <div class="tags">
                            <span><?= htmlspecialchars($commu['styleGenreUn']) ?></span>
                            <span><?= htmlspecialchars($commu['styleGenreDeux']) ?></span>
                            <span><?= htmlspecialchars($commu['styleGenreTrois']) ?></span>
                        </div>
                        
                        <div class="buttons">
                            <a href="index.php?page=profilCommunaute&id=<?= $commu['id_communaute'] ?>" class="details-btn">Détails</a>
                            <?php if (!$isConnected): ?>
                                <a href="#" class="join-btn" onclick="openPopup(); return false;">Rejoins-nous !</a>
                            <?php else: ?>
                                <a href="index.php?page=rejoindreCommunaute&id=<?= $commu['id_communaute'] ?>" class="join-btn">Rejoins-nous !</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php elseif (!empty($search)): ?>
            <p>Aucune communauté trouvée pour "<?= htmlspecialchars($search) ?>"</p>
        <?php else: ?>
            <p>Aucune communauté disponible pour le moment.</p>
        <?php endif; ?>
    </div>

    <div id="popup-connexion" class="popup-overlay" style="display:none;" onclick="closePopup()">
        <div class="popup-message" onclick="event.stopPropagation()">
            <p>Vous devez être connecté pour effectuer cette action.</p>
            <div class="popup-buttons">
                <a href="index.php?page=inscription" class="popup-btn popup-btn-grey">S'inscrire</a>
                <a href="index.php?page=connexion" class="popup-btn popup-btn-blue">Se connecter</a>
            </div>
            <small>(Cliquez en dehors pour fermer)</small>
        </div>
    </div>
</div>