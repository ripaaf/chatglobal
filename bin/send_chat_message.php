<?php
session_start(); // Start the session

include 'connect.php';

if (isset($_SESSION['chatOption'])) {
    // User's chat option is already set in the session
    $chatOption = $_SESSION['chatOption'];

    if ($chatOption === 'anonymous') {
        // The user is already in anonymous mode, do not allow changes
        $userId = $_SESSION['userId'];
    } else {
        // Implement your login logic and set the user's ID accordingly
        if (isset($_SESSION['userId'])) {
            $userId = $_SESSION['userId'];
        } else {
            // Handle unauthenticated access
            die("Please log in to chat.");
        }
    }
} else {
    // User's chat option is not set in the session
    if (isset($_COOKIE['chatOption']) && $_COOKIE['chatOption'] === 'anonymous') {
        // Generate a random number for the anonymous user's name
        $randomNumber = mt_rand(1000, 9999); // Adjust the range as needed

        // Create the anonymous user's name
        $userName = 'anonym' . $randomNumber;
        //https://imgur.com/YQSxsYm
        // Insert the anonymous user into the `user` table with the generated name
        $insertUserQuery = "INSERT INTO user (name, photo) VALUES (:name, :photo)";
        $stmtUser = $pdo->prepare($insertUserQuery);
        $stmtUser->execute(array(':name' => $userName, ':photo' => 'https://i.imgur.com/YQSxsYm.png'));

        // Retrieve the user's ID
        $userId = $pdo->lastInsertId();

        // Set the user's chat option in the session to prevent future changes
        $_SESSION['chatOption'] = 'anonymous';
        $_SESSION['userId'] = $userId;
    } else {
        // Implement your login logic and set the user's ID accordingly
        if (isset($_SESSION['userId'])) {
            $userId = $_SESSION['userId'];
        } else {
            // Handle unauthenticated access
            die("Please log in to chat.");
        }
    }
}

if (isset($_POST['message'])) {
    $message = $_POST['message'];
    $file = $_FILES['file'];

    // Check if a file was uploaded
    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        // Handle file upload
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $fileExtension;
        $uploadDirectory = 'uploads/' . $fileExtension . '/'; // Specify your folder path
        $targetPath = $uploadDirectory . $newFileName;

        // Check if the folder for this file extension exists, if not, create it
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true); // Create the folder with proper permissions
        }

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // File upload was successful
            $mediapath = $targetPath;
        } else {
            // File upload failed
            // Handle the error as needed
        }
    }

    // Get the current date and time
    $currentDateTime = date('Y-m-d H:i:s');

    $insertQuery = "INSERT INTO chat (iduser, message, media, datetime) VALUES (:iduser, :message, :media, :datetime)";
    $stmt = $pdo->prepare($insertQuery);
    $stmt->execute(array(':iduser' => $userId, ':message' => $message, ':media' => $mediapath, ':datetime' => $currentDateTime));
}



?>
