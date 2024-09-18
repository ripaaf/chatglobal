<?php
include '../connect.php';

session_start();
$id = $_SESSION['userId'];

if (isset($_POST['upload'])) {
    $targetDirectory = 'profile_img/'; 

    if (!is_dir($targetDirectory)) {

        mkdir($targetDirectory, 0755, true);
    }

    $imageFileType = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    $randomFilename = uniqid() . '.' . $imageFileType; // Generate a random filename

    $targetFile = $targetDirectory . $randomFilename;


    $sql = "SELECT photo FROM user WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $previousPhoto = $stmt->fetchColumn();

    if ($previousPhoto && file_exists($previousPhoto)) {
        unlink($previousPhoto); // Delete the previous file
    }

    $check = getimagesize($_FILES['photo']['tmp_name']);
    if ($check !== false) {

        if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
            echo "Sorry, your file is too large.";
        } else {
            // Upload the file to the server
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {

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
