<?php
// Start the session
session_start();

// Unset and destroy the session
session_unset();
session_destroy();

// Remove the 'chatOption' cookie, if it exists
if (isset($_COOKIE['chatOption'])) {
    setcookie('chatOption', '', time() - 3600, '/');
}

// Remove the 'nomor-sesi' cookie, if it exists
if (isset($_COOKIE['nomor-sesi'])) {
    setcookie('nomor-sesi', '', time() - 3600, '/');
}

// Redirect to the login page
header("Location: index.php");
exit();
?>
