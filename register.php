<?php
include "connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    $sql = "INSERT INTO user (name, password, anonym, photo) VALUES (?, ?, 'false','https://i.imgur.com/YQSxsYm.png')";
    $stmt = $pdo->prepare($sql);
    if ($stmt->execute([$name, $password])) {
        // Registration successful,
        header("Location: login.php");
        exit();
    } else {
        // Registration failed, handle the error
        echo "Registration failed. Please try again.";
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
    <meta property="og:image" content="components/thumbnail.png">
    <!-- <meta property="og:url" content="https://example.com/chat-application"> -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Global Chat">
    <meta name="twitter:description" content="A simple Global Chat for communication.">
    <meta name="twitter:image" content="components/thumbnail.png">
    <title>Registration</title>
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
            outline: none;
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
        }

        .form-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }

        .form-wrapper a {
            text-decoration: none;
            font-size: 18px;
            color: gray;
        }

        .form-wrapper a:hover {
            color: lightgray;
        }
    </style>
</head>
<body>
    <div class="form-wrapper">
        <div class="form-container">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <h2>Registration</h2>
                <div class="form-wrapper">
                    <label for="name">Name:</label>
                    <input type="text" name="name" id="name" required>
    
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
    
                    <input type="submit" value="Register">
                </div>
            </form>
        </div>
        <a href="login.php">login</a>
    </div>
</body>
</html>

