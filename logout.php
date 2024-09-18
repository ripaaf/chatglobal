<?php

session_start();


session_unset();
session_destroy();


if (isset($_COOKIE['chatOption'])) {
    setcookie('chatOption', '', time() - 3600, '/');
}


if (isset($_COOKIE['nomor-sesi'])) {
    setcookie('nomor-sesi', '', time() - 3600, '/');
}

// Redirect to the login page
header("Location: index.php");
exit();
?>
