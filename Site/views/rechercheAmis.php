<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    require_once(__DIR__ . '../../bdd/db.php');

    $isConnected = isset($_SESSION['user']);
    $search = $_GET['search'] ?? '';

    $support = $_GET['support'] ?? '';
    $ligne = $_GET['ligne'] ?? '';
    $langue = $_GET['langue'] ?? '';
    $implication = $_GET['implication'] ?? '';
    $age = $_GET['age'] ?? '';

    $suggestedUsers = [];
    $debugInfo = []; // Pour le débogage

    if ($isConnected) {
        $userId = $_SESSION['user']['id'];
        
        // Si c'est une requête AJAX pour la recherche en temps réel
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest' &&
            !empty($search)) {
            
            header('Content-Type: application/json');
            
            // Version simplifiée de la requête pour le debug
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
            exit;
        }
        
        // Logique normale pour l'affichage de la page
        if (!empty($search)) {
            // DEBUG : Ajoutons des informations de débogage
            $debugInfo[] = "Recherche pour: '$search'";
            $debugInfo[] = "ID utilisateur connecté: $userId";
            
            // Test 1: Requête simple sans exclusions
            $simpleQuery = "SELECT id_utilisateur, pseudo, imageProfil, genreJeu, support 
                FROM utilisateur 
                WHERE pseudo LIKE :search";
            
            $stmt = $dbh->prepare($simpleQuery);
            $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            $stmt->execute();
            $allResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $debugInfo[] = "Résultats sans filtre utilisateur: " . count($allResults);
            
            // Test 2: Requête avec exclusion de l'utilisateur connecté seulement
            $query = "SELECT id_utilisateur, pseudo, imageProfil, genreJeu, support 
                FROM utilisateur 
                WHERE id_utilisateur != :userId 
                AND pseudo LIKE :search
                ORDER BY RAND()";

            $stmt = $dbh->prepare($query);
            $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
            $stmt->execute();
            $suggestedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $debugInfo[] = "Résultats après exclusion utilisateur connecté: " . count($suggestedUsers);
            
            // Test 3: Vérification de la table conversations et messages
            try {
                $convQuery = "SELECT COUNT(*) as count FROM conversations c
                            JOIN messages m ON m.conversation_id = c.id
                            WHERE m.type = 'system' AND m.response = 'oui'";
                $stmt = $dbh->prepare($convQuery);
                $stmt->execute();
                $convCount = $stmt->fetch(PDO::FETCH_ASSOC);
                $debugInfo[] = "Messages système avec réponse 'oui': " . $convCount['count'];
            } catch (Exception $e) {
                $debugInfo[] = "Erreur avec la table conversations/messages: " . $e->getMessage();
            }
            
        } else {
            // Logique pour les filtres (votre code existant)
            $conditions = ["id_utilisateur != ?"];
            $params = [$userId];

            if (!empty($support)) {
                $conditions[] = "support = ?";
                $params[] = $support;
            }

            if (!empty($ligne)) {
                $conditions[] = "styleJeu LIKE ?";
                $params[] = "%$ligne%";
            }

            if (!empty($age) && is_numeric($age)) {
                $year = date('Y') - (int)$age;
                $birthDate = $year . '-01-01';
                $conditions[] = "date_naissance <= ?";
                $params[] = $birthDate;
            }

            if (!empty($langue)) {
                $conditions[] = "langue LIKE ?";
                $params[] = "%$langue%";
            }

            if (!empty($implication)) {
                $conditions[] = "implication = ?";
                $params[] = $implication;
            }

            $whereClause = implode(' AND ', $conditions);
            $sql = "SELECT id_utilisateur, pseudo, imageProfil, genreJeu, support 
                    FROM utilisateur 
                    WHERE $whereClause 
                    ORDER BY RAND() 
                    LIMIT 100";

            $stmt = $dbh->prepare($sql);
            $stmt->execute($params);
            $suggestedUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
?>

<div class="containerBody">
    <div class="headerAccueil">
        <h1>Rechercher d'amis</h1>
        <a href="index.php?page=profil">
            <img src="Site/assets/images/<?= htmlspecialchars($_SESSION['user']['imageProfil']) ?>" >
            <?= htmlspecialchars($_SESSION['user']['pseudo'] ?? '') ?>
        </a>
    </div>

    <div class="recherche-bar">
        <form method="get" action="index.php">
            <input type="hidden" name="page" value="rechercheAmis">
            <input type="text" name="search" id="search-input" placeholder="Rechercher un pseudo..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            <button type="button" onclick="ouvrirFiltre()">Filtrer</button>
        </form>
    </div>

    <!-- Popup filtre -->
    <div id="overlay-filtre" class="overlay hidden"></div>

    <div id="modal-filtre" class="modal-filtre hidden">
        <div class="modal-header">
            <h2>Filtrer les utilisateurs</h2>
            <button class="btn-close" onclick="fermerFiltre()">×</button>
        </div>
        <form method="get" action="index.php">
            <input type="hidden" name="page" value="rechercheAmis">

            <label>Plateformes :
                <select name="support">
                    <option value="">-- Choisir --</option>
                    <option value="PC" <?= $support === 'PC' ? 'selected' : '' ?>>PC</option>
                    <option value="PS5" <?= $support === 'PS5' ? 'selected' : '' ?>>PS5</option>
                    <option value="Xbox" <?= $support === 'Xbox' ? 'selected' : '' ?>>Xbox</option>
                    <option value="Switch" <?= $support === 'Switch' ? 'selected' : '' ?>>Switch</option>
                </select>
            </label>

            <label>Implication :
                <select name="implication">
                    <option value="">-- Choisir --</option>
                    <option value="occasionnel" <?= $implication === 'occasionnel' ? 'selected' : '' ?>>Casual</option>
                    <option value="régulier" <?= $implication === 'régulier' ? 'selected' : '' ?>>Régulier</option>
                    <option value="hardcore" <?= $implication === 'hardcore' ? 'selected' : '' ?>>Hardcore</option>
                </select>
            </label>

            <label>Langue :
                <select name="langue">
                    <option value="">-- Choisir --</option>
                    <option value="Français" <?= $implication === 'Français' ? 'selected' : '' ?>>Français</option>
                    <option value="Anglais" <?= $implication === 'Anglais' ? 'selected' : '' ?>>Anglais</option>
                    <option value="Espagnol" <?= $implication === 'Espagnol' ? 'selected' : '' ?>>Espagnol</option>
                </select>
            </label>

            <button type="submit" class="btn-enregistrer">Appliquer les filtres</button>
        </form>
    </div>

    <div class="suggestions-utilisateurs" id="resultats-utilisateurs">
        <?php if (!empty($suggestedUsers)): ?>
            <?php foreach ($suggestedUsers as $user): ?>
                <div class="carte-utilisateur">
                    <img src="Site/assets/images/<?= htmlspecialchars($user['imageProfil']) ?>" alt="Profil de <?= htmlspecialchars($user['pseudo']) ?>" class="carte-image">
                    <h3><?= htmlspecialchars($user['pseudo']) ?></h3>
                    <p><?= htmlspecialchars($user['genreJeu'] ?? '') ?></p>
                    <p><?= htmlspecialchars($user['support'] ?? '') ?></p>

                    <div class="carte-boutons">
                        <a href="index.php?page=profilAmi&id=<?= $user['id_utilisateur'] ?>" class="btn-detail">
                            <img src="Site/assets/images/user-square.png" alt="">Détails
                        </a>
                        <a href="index.php?page=ajoutAmi&id=<?= $user['id_utilisateur'] ?>" class="btn-ajouter">
                            <img src="Site/assets/images/plus-circle.png" alt="">Ajoute
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php elseif (!empty($search)): ?>
            <p>Aucun résultat trouvé pour "<?= htmlspecialchars($search) ?>"</p>
        <?php endif; ?>
    </div>
</div>