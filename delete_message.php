<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get user ID and message datetime from the request
    $userId = $_POST['userId'];
    $datetime = $_POST['datetime'];

    // Query the database to get the media file path
    $query = "SELECT media FROM chat WHERE iduser = ? AND datetime = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userId, $datetime]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $mediaPath = $result['media'];

        $deleteQuery = "DELETE FROM chat WHERE iduser = ? AND datetime = ?";
        $deleteStmt = $pdo->prepare($deleteQuery);
        $deleteStmt->execute([$userId, $datetime]);

        if (file_exists($mediaPath)) {
            unlink($mediaPath);
        }

        echo 'Message and media deleted successfully';
    } else {
        echo 'Message not found';
    }
} else {
    // Handle invalid requests or direct access to this script
    header('HTTP/1.0 403 Forbidden');
    echo 'Access Denied';
}
?>
