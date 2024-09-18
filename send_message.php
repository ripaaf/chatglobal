<?php
session_start(); // Start the session
include 'connect.php';

if (isset($_SESSION['chatOption'])) {
    $chatOption = $_SESSION['chatOption'];

    if ($chatOption === 'anonymous') {
        $userId = $_SESSION['userId'];
    } else {
        if (isset($_SESSION['userId'])) {
            $userId = $_SESSION['userId'];
        } else {
            // Handle unauthenticated access
            die("Please log in to chat.");
        }
    }
} else {
    if (isset($_SESSION['userId'])) {
        $userId = $_SESSION['userId'];
    } else {
        die("Please log in to chat.");
    }
}

if (isset($_POST['message'])) {
    $message = $_POST['message'];
    $file = $_FILES['file'];


    if ($file && $file['error'] === UPLOAD_ERR_OK) {

        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        //$newFileName = uniqid() . '.' . $fileExtension;
        $newFileName = $file['name'];
        $uploadDirectory = 'uploads/' . $fileExtension . '/'; // Specify your folder path
        $targetPath = $uploadDirectory . $newFileName;


        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true); // Create the folder with proper permissions
        }

        $counter = 1;
        while (file_exists($targetPath)) {
            $newFileName = pathinfo($file['name'], PATHINFO_FILENAME) . "_$counter.$fileExtension";
            $targetPath = $uploadDirectory . $newFileName;
            $counter++;
        }

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {

            removeExifData($targetPath);
            $mediapath = $targetPath;
        } else {
            echo '<p>Erorr file not uploaded.</p>';
        }
    }


    $currentDateTime = date('Y-m-d H:i:s');

    $insertQuery = "INSERT INTO chat (iduser, message, media, datetime) VALUES (:iduser, :message, :media, :datetime)";
    $stmt = $pdo->prepare($insertQuery);
    $stmt->execute(array(':iduser' => $userId, ':message' => $message, ':media' => $mediapath, ':datetime' => $currentDateTime));
}


// Function to remove EXIF data from an image file
function removeExifData($imagePath) {
    if (function_exists('exif_read_data') && function_exists('exif_imagetype')) {
        $imageType = exif_imagetype($imagePath);
        if ($imageType === IMAGETYPE_JPEG) {
            $exif = exif_read_data($imagePath);
            if ($exif !== false) {
                // Remove EXIF data
                $image = imagecreatefromjpeg($imagePath);
                imagejpeg($image, $imagePath, 100);  // Overwrite the original image
                imagedestroy($image);
            }
        }
    }
}
