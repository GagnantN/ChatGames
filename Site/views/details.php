<?php
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if (!$isAjax) {
        header('Location: index.php?page=profil');
        exit;
    }
?>

<h1>Hello</h1>