<body>
    <?php 
        $isConnected = isset($_SESSION['user']) && isset($_SESSION['user']['id']); 
        $disabledClass = !$isConnected ? 'disabled' : '';
        $currentPage = $_GET['page'] ?? 'accueil';
    ?>

    <div class="layout">
        <nav class="navbar">
            <div class="containerMenu">
                <img src="Site/assets/images/logo.png" class="logo" alt="Logo du Site ChatGames">
                <a href="index.php?page=accueil" class="<?= $currentPage === 'accueil' ? 'active' : '' ?>"><img src="Site/assets/images/accueil.png" class="icones" alt="Accueil"> Accueil</a>
                <a href="<?= $isConnected ? 'index.php?page=rechercheAmis' : '#' ?>" class="<?= $currentPage === 'rechercheAmis' ? 'active' : '' ?> <?= $disabledClass ?>"><img src="Site/assets/images/amis.png" class="icones" alt="Amis"> Recherche amis</a>
                <a href="index.php?page=communaute" class="<?= $currentPage === 'communaute' ? 'active' : '' ?>"><img src="Site/assets/images/communauter.png" class="icones" alt="Communauté"> Communauté</a>
                <a href="<?= $isConnected ? 'index.php?page=messagerie' : '#' ?>" class="<?= $currentPage === 'messagerie' ? 'active' : '' ?>  <?= $disabledClass ?>"><img src="Site/assets/images/messagerie.png" class="icones" alt="Messagerie"> Messagerie</a>
                <a href="index.php?page=evenements" class="<?= $currentPage === 'evenements' ? 'active' : '' ?>"><img src="Site/assets/images/evenement.png" class="icones" alt="Événements"> Événements</a>
                <a href="index.php?page=stream" class="<?= $currentPage === 'stream' ? 'active' : '' ?>"><img src="Site/assets/images/stream.png" class="icones" alt="Stream"> Stream</a>
                <a href="<?= $isConnected ? 'index.php?page=deconnexion' : '#' ?>" class="<?= $currentPage === 'deconnexion' ? 'active' : '' ?> <?= $disabledClass ?> "><img src="Site/assets/images/deconnexion.png" class="icones" alt="Déconnexion"> Déconnexion </a>
            </div>
        </nav>
        <main class="content">
    

