<?php
    session_start();
    $page = $_GET['page'] ?? 'accueil';

    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if (!$isAjax) {
        include('Site/includes/head.php');
        include('Site/includes/menu.php');
        echo '<main class="content">';
    }

    $pageFile = __DIR__ . '/Site/views/' . $page . '.php';

    if (file_exists($pageFile)) {
        include($pageFile);
    } else {
        echo "<p>La page demandée n'existe pas.</p>";
    }

    if (!$isAjax) {
        echo '</main>';
        include('Site/includes/footer.php');
    }
?>
