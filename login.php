<?php
session_start();

include "connect.php";

// Check if the user is already logged in
if (isset($_SESSION['userId'])) {

    session_start();
    $id = $_SESSION['userId'];

    $sql = "SELECT anonym FROM user WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    // Check if the user is not anonymous (anonym = false)
    if ($user['anonym'] === 'false') {
        // Redirect to index.php
        setcookie('chatOption', 'logged_in', time() + 60 * 60 * 24 * 30);
        header("Location: index.php");
        exit();
    }
} else {
    // header("Location: index.php");
    // exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize user input
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    $sql = "SELECT id, name, password FROM user WHERE name = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Login successful, set session variables
        $_SESSION['userId'] = $user['id'];
        $_SESSION['name'] = $user['name'];

        // Set a cookie to indicate the user is logged in
        setcookie('chatOption', 'logged_in', time() - 3600); // 30 days expiration

        header("Location: index.php");
        exit();
    } else {
        $login_error = "Invalid name or password.";
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
    <link rel="icon" type="image/png" href="components/favicon.png">
    <link rel="shortcut icon" href="components/favicon.png" type="image/x-icon" />
    <meta property="og:title" content="Global Chat">
    <meta property="og:description" content="A simple Global Chat for communication.">
    <meta property="og:image" content="components/thumnail.png">
    <!-- <meta property="og:url" content="https://example.com/chat-application"> -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Global Chat">
    <meta name="twitter:description" content="A simple Global Chat for communication.">
    <meta name="twitter:image" content="components/thumnail.png">
    <title>Login</title>
    <style>
        /* Reset default styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #b3b3b3;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        /* Form container styles */
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            /* box-shadow: 0 0 10px rgba(0, 0, 0, 0.2); */
            text-align: center;
        }

        /* Heading styles */
        h2 {
            margin: 0 0 20px;
        }

        /* Label styles */
        label {
            display: block;
            /* font-weight: bold; */
            margin-bottom: 10px;
        }

        /* Input styles */
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline:none;
        }

        /* Submit button styles */
        input[type="submit"] {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #555;
        }

        /* Error message styles */
        .error-message {
            color: #ff3838;
            /* font-weight: bold; */
        }
        
        .form-wrapper{
            display:flex;
            flex-direction:column;
            align-items: center;
            justify-content: center;
            gap:20px;
        }
        .form-wrapper a{
            text-decoration:none;
            font-size:18px;
            color:gray;
        }
        .form-wrapper a:hover{
            color:lightgray;
        }
    </style>
</head>
<body>
    <div class="form-wrapper">
        <a href="index.php">home</a>
        <div class="form-container">
            <h2>Login</h2>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="name">name</label>
                <input type="text" name="name" id="name" required autocomplete="off">
                <br>
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
                <br>
                <input type="submit" value="Login">
            </form>
            <?php
            if (isset($login_error)) {
                echo "<p class='error-message'>$login_error</p>";
            }
            ?>
        </div>
        <a href="register.php">register here</a>
    </div>
</body>
</html>

