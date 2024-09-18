<?php
session_start();

include "../connect.php";

// Check if the user is already logged in
if (isset($_SESSION['userId'])) {


    session_start();
    $id = $_SESSION['userId'];

    $sql = "SELECT anonym FROM user WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    // Check if the user is not anonymous (anonym = false)
    if ($user['anonym'] === 'true') {

        setcookie('chatOption', 'logged_in', time() + 60 * 60 * 24 * 30);
        header("Location: upload_photo.php");
        exit();
    }
} else {
    header("Location: upload_photo.php");
    exit();
}
?>


<?php
include '../connect.php'; // Include your database connection script

session_start();
$id = $_SESSION['userId'];

// Fetch user data for the profile
$sql = "SELECT name, photo, password FROM user WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$user = $stmt->fetch();

$previousPassword = $user['password'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {

    $updateData = array();
    $passwordChanged = false;

    if (!empty($_POST['new_name'])) {
        $newName = $_POST['new_name'];
        $updateData['name'] = $newName;
    }

    $newPassword = $_POST['new_password'];
    $currentPassword = $_POST['current_password'];

    if (!empty($newPassword)) {

        if (password_verify($currentPassword, $previousPassword)) {
            $rightCurrentPassword = "Password updated.";
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateData['password'] = $hashedPassword;
            $passwordChanged = true;
        } else {
            $wrongCurrentPassword = "Current password is incorrect. Password not updated.";
            // echo "Current password is incorrect. Password not updated.";
        }
    }


    if (!empty($_FILES['new_photo']['name'])) {
        $targetDirectory = 'profile_img/';
        if (!is_dir($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        $imageFileType = strtolower(pathinfo($_FILES['new_photo']['name'], PATHINFO_EXTENSION));
        $randomFilename = uniqid() . '.' . $imageFileType;
        $targetFile = $targetDirectory . $randomFilename;

        // Delete the previous photo, if it exists
        if ($user['photo'] && file_exists($user['photo'])) {
            unlink($user['photo']);
        }

        if (move_uploaded_file($_FILES['new_photo']['tmp_name'], $targetFile)) {
            $updateData['photo'] = $targetFile;
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }


    if (!empty($updateData)) {
        $updateProfileSql = "UPDATE user SET ";
        $params = array();
        foreach ($updateData as $key => $value) {
            $updateProfileSql .= "$key = ?, ";
            $params[] = $value;
        }
        $updateProfileSql = rtrim($updateProfileSql, ', '); // Remove the trailing comma

        $updateProfileSql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $pdo->prepare($updateProfileSql);
        $stmt->execute($params);

        if ($passwordChanged) {

            header("Location: profile.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel='stylesheet' type='text/css' media='screen' href='main.css'>
    <script src="jquery-3.6.0.js"></script>
    <script src='main.js'></script>
    <link rel="icon" type="image/png" href="/components/favicon.png">
    <link rel="shortcut icon" href="/components/favicon.png" type="image/x-icon" />
    <meta property="og:title" content="Global Chat">
    <meta property="og:description" content="A simple Global Chat for communication.">
    <meta property="og:image" content="/components/thumnail.png">
    <!-- <meta property="og:url" content="https://example.com/chat-application"> -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Global Chat">
    <meta name="twitter:description" content="A simple Global Chat for communication.">
    <meta name="twitter:image" content="/components/thumnail.png">
    <title>edit profile</title>
</head>
<style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            text-align: center;
        }

        h2 {
            color: #333;
        }

        form {
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 20px;
            width: 300px;
            margin: 0 auto;
            border-radius: 5px;
        }

        label {
            font-weight: bold;
            display: block;
        }

        input[type="file"] {
            display: block;
            margin: 10px 0;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        a {
            text-decoration: none;
            color: #4CAF50;
        }
    </style>
<body>
    <h2>Edit Your Profile</h2>
    <h3>Profile Photo:</h3>
    <img src="<?php echo $user['photo']; ?>" alt="Profile Photo" width="100">
    
    <form method="post" enctype="multipart/form-data">
        <label for="new_photo">Change Photo:</label>
        <input type="file" name="new_photo" id="new_photo">
        <br>
        <h3>Edit Name:</h3>
        <input type="text" name="new_name" value="<?php echo $user['name']; ?>">
        <br>
        <h3>Change Password:</h3>
        <input type="password" name="current_password" placeholder="Current Password" required>
        <input type="password" name="new_password" placeholder="New Password">
        <br>
        <input type="submit" name="update_profile" value="Update Profile">
    </form>
        <p><?php echo $wrongCurrentPassword; echo $rightCurrentPassword;?></p>
        <a href="../index.php">home</a>
</body>
</html>
