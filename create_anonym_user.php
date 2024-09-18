<?php
session_start(); // Start the session
include 'connect.php';

if (isset($_COOKIE['chatOption']) && $_COOKIE['chatOption'] === 'anonymous') {
    // Generate a random number for the anonym
    $randomNumber = mt_rand(1000, 9999);

    // nama anonym
    $userName = 'anonym' . $randomNumber;

    $insertUserQuery = "INSERT INTO user (name, photo) VALUES (:name, :photo)";
    $stmtUser = $pdo->prepare($insertUserQuery);
    $stmtUser->execute(array(':name' => $userName, ':photo' => 'https://i.imgur.com/YQSxsYm.png'));

    $userId = $pdo->lastInsertId();

    $_SESSION['chatOption'] = 'anonymous';
    $_SESSION['userId'] = $userId;
}else {
    if (isset($_SESSION['userId'])) {
        $userId = $_SESSION['userId'];
    } else {
        die("Please log in to chat.");
    }
}