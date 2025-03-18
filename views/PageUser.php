<?php

require_once __DIR__ . '/../bdd/db.php';

if (!isset($_SESSION['id_user'])){
    redirect('index.php?page=Accueil');
    exit();
}

$id_user =$_SESSION['id_user'];

$stmt = $dbh->prepare("
    SELECT G.id, G.nom, G.description, G.image_profil
    FROM GAME G
    INNER JOIN PLAYER_GAME P 
    ON G.id = P.id_jeu
    WHERE P.id_player = :id_user
");

$stmt->bindValue(':id_user', $id_user, PDO::PARAM_INT);
$stmt->execute();

$jeux = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt_player = $dbh->prepare("
    SELECT pseudo, image_profil
    FROM PLAYER
    WHERE id_user = :id_user
");

$stmt_player->bindValue(':id_user', $id_user, PDO::PARAM_INT);
$stmt_player->execute();

$player = $stmt_player->fetch(PDO::FETCH_ASSOC);

$nom = $_SESSION["nom"];
$prenom = $_SESSION["prenom"];
$mail = $_SESSION["mail"];
$pseudo = $player["pseudo"];
$image = $player["image_profil"];

?>

<h1> Bienvenue : <?php echo htmlspecialchars($pseudo); ?> !</h1>
<button class ="deconnecxion"><a href="index.php?page=Logout">Déconnexion</a></button>
<img class = "img_profil" src="<?php if(isset($image)== null){echo htmlspecialchars("profil.png");} else {echo htmlspecialchars($image);} ?>" alt="Photo de profil" width = "150">
<h2><?php echo htmlspecialchars($nom); ?> </h2>
<h2><?php echo htmlspecialchars($prenom); ?> </h2>
<h2><?php echo htmlspecialchars($mail); ?> </h2>

<h2>Listes des Jeux en Favoris</h2>
<ul class="image-list">
        <?php
        $compteur = 0;
        foreach ($jeux as $jeu): 
        ?>
            <li>
                <div class="image-container">
                    <img src="<?= htmlspecialchars($jeu['image']) ?>" alt="Fiche du jeu : <?= htmlspecialchars($jeu['nom']) ?>">
                    <div class="overlay"><?= htmlspecialchars($jeu['nom']) ?>
                        <form action="index.php?page=supprim_favori" method="POST">
                            <input type="hidden" name="jeu_id" value="<?php echo $jeu['id']; ?>">
                            <button type="submit">Retirer des Favoris</button>
                        </form>
                    </div>
                    
                </div>
            </li>
        <?php
            $compteur++;
        endforeach;
        ?>
    </ul>

<h2>Amis</h2>
<button class="ajout_amis"> <a href="index.php?page=Matchmaking&pagination=1">Ajout Amis</a></button>
<h3>Retirez amis</h3>
<button class="retirer_amis"> <a href="index.php?page=Retirer_Amis">Retirez Amis</a></button>
<button class="demande_amis"> <a href="index.php?page=Demande_Amis">Demande Amis</a></button>


<ul class="amis-list">
        <?php
        $compteur = 0;
        foreach ($amis as $ami): 
        ?>
            <li>
                <div class="image-container">
                    <img src="<?= htmlspecialchars($ami['image']) ?>" alt="Fiche du jeu : <?= htmlspecialchars($ami['nom']) ?>">
                    <div class="overlay"><?= htmlspecialchars($ami['nom']) ?>
                        <form action="index.php?page=supprim_favori" method="POST">
                            <input type="hidden" name="jeu_id" value="<?php echo $ami['id']; ?>">
                            <button type="submit">Retirer des Favoris</button>
                        </form>
                    </div>
                    
                </div>
            </li>
        <?php
            $compteur++;
        endforeach;
        ?>
    </ul>
