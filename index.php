<?php
session_start();

include 'connect.php';

if (isset($_COOKIE['chatOption']) && $_COOKIE['chatOption'] === 'logged_in') {
    $button_login = '<button class="logout" onclick="window.location.href=\'logout.php\'">Logout</button>';
}else{
    $button_login = '<button id="login">Log In</button>';
}

// Check if the user is logged in
if (isset($_SESSION['userId'])) {
    $tes = $_SESSION['userId'];

    $expiration = time() + (7 * 24 * 60 * 60); // 7 days

    // Save the userId in a cookie
    setcookie('nomor-sesi', $tes, $expiration);

    $sql = "SELECT name, photo FROM user WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$tes]);
    $user = $stmt->fetch();

    $nama = $user['name'];
    $foto = $user['photo'];
    if (filter_var($foto, FILTER_VALIDATE_URL)) {
        // $foto is a url
        $fotoprofile = "<div class='profile-image'><img src='$foto'></div>";
    } else {
        // $foto is not a url
        $fotoprofile = "<div class='profile-image'><img src='profile/$foto'></div>";
    }
} else {
    if (isset($_COOKIE['nomor-sesi'])) {
        $userIdFromCookie = $_COOKIE['nomor-sesi'];

        // Fetch user's name using the userId from the cookie
        $sql = "SELECT name , id FROM user WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userIdFromCookie]);
        $user = $stmt->fetch();

        $nama = $user['name'];

        $_SESSION['userId'] = $user['id'];
        $_SESSION['name'] = $user['name'];

        setcookie('chatOption', 'logged_in', time() + 60 * 60 * 24 * 30);
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
    <link rel="icon" type="image/png" href="components/favicon-earth.png">
    <link rel="shortcut icon" href="components/favicon-earth.png" type="image/x-icon" />
    <meta property="og:title" content="Global Chat">
    <meta property="og:description" content="A simple Global Chat for communication.">
    <meta property="og:image" content="components/thumnail.png">
    <!-- <meta property="og:url" content="https://example.com/chat-application"> -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Global Chat">
    <meta name="twitter:description" content="A simple Global Chat for communication.">
    <meta name="twitter:image" content="components/thumnail.png">
    <title>GlobalChat</title>
</head>
<style>

</style>
<body>
    <!-- Cookie Consent Banner -->
    <div class="cookie-accept" id="cookie-consent">
        <div>
            <p>This website uses cookies to ensure you get the best experience on our website.</p>
        </div>
        <div>
            <button id="accept-cookies">Accept Cookies</button>
        </div>
    </div>

    <!-- <h1>Chat Application</h1> -->

    <div class="header-wrapper">
        <div class="header-username-profile">
            <?php echo "<div class='profile-image'><a href='profile/profile.php'>$fotoprofile</a></div>";?>
            <p><?php echo $nama;?></p>
        </div>
        <div>
            <?php echo $button_login;?>
        </div>
    </div>

    
    <div class="button-chat-wrapper">
        <div id="chat-container">
            <div class="chat-messages" id="chat-messages" >
                <!-- Your chat messages will be displayed here -->
            </div>
        </div>
        <div class="newest-message">
            <button onclick="scrollChatToBottom()" tittle="scroll to bottom" id="scrollButton"><img src="components/vector/arrow-down.svg"></button>
        </div>
    </div>

    <!-- popup images -->
    <div id="imagePopup" class="modal">
    <span class="close" onclick="closeImagePopup()">&times;</span>
    <img id="popupImage" class="modal-content">
    </div>

    <!-- popup video -->
    <div id="videoPopup" class="modal">
    <span class="close" onclick="closeVideoPopup()">&times;</span>
    <video id="popupVideo" class="modal-content" controls></video>
    </div>


    <div class="login-options" id="login-options">
        <?php
        session_start();
        if (!isset($_SESSION['userId'])) {
        } else {
        }
        if (!isset($_COOKIE['chatOption']) || $_COOKIE['chatOption'] !== 'anonymous'){
            if (!isset($_SESSION['userId'])) {
                echo '<div><button id="login">Log In</button></div>';
                echo '<div><button id="chat-anonymously">Chat Anonymously</button></div>';
            }
        }
        ?>
    </div>

    <div class="<?php echo isset($_SESSION['userId']) ? 'chat-input-wrapper active' : 'chat-input-wrapper'; ?>" id="chat-input">
        <div class="chat-area-file" id="drop-area">
            <textarea id="message" placeholder="Type your message..." onkeydown="handleKeyPress(event)"></textarea>
            <div>
                <div>
                    <label for="file"><img src="components/vector/images.svg"></label>
                    <input type="file" id="file" accept="image/*, video/*, .pdf, .doc, .docx" multiple style="display: none;">
                </div>
                <div>
                    <ul id="file-list">
                        <!-- Selected files will be listed here -->
                    </ul>
                </div>
            </div>
        </div>
        <div class="send-button-wrapper">
            <button id="send">Send</button>
        </div>
    </div>



    <script>
        // JavaScript to handle the cookie consent banner
        const cookieConsent = document.getElementById("cookie-consent");
        const acceptCookiesButton = document.getElementById("accept-cookies");

        acceptCookiesButton.addEventListener("click", () => {
            // Set a cookie to remember that the user accepted cookies
            document.cookie = "cookie-consent=accepted; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";
            cookieConsent.style.display = "none";
        });

        // Check if the user has already accepted cookies
        if (document.cookie.includes("cookie-consent=accepted")) {
            cookieConsent.style.display = "none";
        }
    </script>
    <script>
        const dropArea = document.getElementById('drop-area');
        const file = document.getElementById('file');
        const fileList = document.getElementById('file-list');
        const sendButton = document.getElementById('send'); // Get the "Send" button element

        dropArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropArea.style.border = '2px dashed #333';
        });

        dropArea.addEventListener('dragleave', () => {
            dropArea.style.border = '2px dashed #ccc';
        });

        dropArea.addEventListener('drop', (e) => {
            e.preventDefault();
            dropArea.style.border = '2px dashed #ccc';

            const files = e.dataTransfer.files;
            handleFiles(files);
        });

        file.addEventListener('change', () => {
            const files = file.files;
            handleFiles(files);
        });

        sendButton.addEventListener('click', () => {
            // Clear the file list by setting its innerHTML to an empty string
            fileList.innerHTML = '';
        });

        function handleFiles(files) {
            fileList.innerHTML = ''; // Clear the file list

            for (let i = 0; i < files.length; i++) {
                const listItem = document.createElement('li');
                listItem.textContent = files[i].name;
                fileList.appendChild(listItem);
            }
        }

    </script>
    <script>
        // JavaScript to handle the chat-input-wrapper
        const chatInputWrapper = document.getElementById("chat-input");

        // Check if the user is logged in
        <?php if (isset($_SESSION['userId'])) { ?>
        chatInputWrapper.classList.add('active');
        <?php } ?>
    </script>

    <script>
    function handleKeyPress(event) {
        //shift+enter for sending
        if (event.shiftKey && event.key === "Enter") {
            event.preventDefault();
            document.getElementById("send").click();
        }
        //enter for sending
        // if (event.key === "Enter" && !event.shiftKey) {
        // event.preventDefault();
        // document.getElementById("send").click();
        // }
    }
    </script>
</body>
</html>
