<?php
// On inclut le fichier functions.php pour obtenir les fonctions
require_once '../bdd/functions.php';
$jeux = getJeu();
?>


    <!-- On affiche la liste des jeux -->
    <br />
    <h2>Liste des Jeux</h2>

    <br />

    <ul class="image-list">
        <?php
        $compteur = 0;
        foreach ($jeux as $jeu): 
        ?>
            <li>
                <div class="image-container">
                    <img src="<?= htmlspecialchars($jeu['image_profil']) ?>" alt="Fiche du jeu <?= htmlspecialchars($jeu['nom']) ?>">
                    <a href="index.php?page=PageJeu&id=<?= $jeu['id'] ?>">
                        <div class="overlay"><?= htmlspecialchars($jeu['nom']) ?></div>
                    </a>
                </div>
            </li>
        <?php
            $compteur++;
        endforeach;
        ?>
    </ul>


