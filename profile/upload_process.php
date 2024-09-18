<?php
include '../connect.php'; // Include your database connection script

session_start();
$id = $_SESSION['userId'];

if (isset($_POST['upload'])) {
    $targetDirectory = 'profile_img/'; // Create a directory to store uploaded photos

    if (!is_dir($targetDirectory)) {
        // Create the directory if it doesn't exist
        mkdir($targetDirectory, 0755, true);
    }

    $imageFileType = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    $randomFilename = uniqid() . '.' . $imageFileType; // Generate a random filename

    $targetFile = $targetDirectory . $randomFilename;

    // Retrieve the previous file path from the database
    $sql = "SELECT photo FROM user WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $previousPhoto = $stmt->fetchColumn();

    // Check if a previous file exists and delete it
    if ($previousPhoto && file_exists($previousPhoto)) {
        unlink($previousPhoto); // Delete the previous file
    }

    // Check if the file is an image
    $check = getimagesize($_FILES['photo']['tmp_name']);
    if ($check !== false) {
        // Check file size (adjust as needed)
        if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
            echo "Sorry, your file is too large.";
        } else {
            // Upload the file to the server
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                // Insert the new file path into the database
                $sql = "UPDATE user SET photo = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$targetFile, $id]);
                echo "Profile photo uploaded successfully.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "File is not an image.";
    }
}
?>
