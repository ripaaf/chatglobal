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
    <h2>Upload Profile Photo</h2>
    <form action="upload_process.php" method="post" enctype="multipart/form-data">
        <label for="photo">Select Photo:</label>
        <input type="file" name="photo" id="photo" accept="image/*" required>
        <br>
        <input type="submit" name="upload" value="Upload">
    </form>
    <a href="../index.php">home</a>
</body>
</html>


