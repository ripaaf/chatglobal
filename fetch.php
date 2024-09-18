<?php
include 'connect.php';
session_start();

function formatText($message) {
    $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

    // Bold: **text**
    $message = preg_replace("/\*\*(.*?)\*\*/", '<strong>$1</strong>', $message);
    
    // Italic: --text--
    $message = preg_replace("/--(.*?)--/", '<em>$1</em>', $message);

    // Strikethrough: ~~text~~
    $message = preg_replace("/~~(.*?)~~/", '<del>$1</del>', $message);

    // Underline: __text__
    $message = preg_replace("/__(.*?)__/", '<u>$1</u>', $message);

    // clickable link
    $message = preg_replace("/\bhttps?:\/\/\S+\b/", '<a style="margin-left: 0;" href="$0" target="_blank">$0</a>', $message);

    return $message;
}

function generateFileAttachment($media) {
    // Extract the file name from the media URL or identifier
    // Modify this logic according to your database schema
    $fileName = basename($media);  // Example: 'my_file.txt'

    // Generate the file attachment HTML
    return '<div class="file-attachment">
              <i class="fa fa-file-text"></i> <span>' . $fileName . ' </span>
              <a href="' . $media . '" download><img class="profile-image" src="components/vector/download.svg"></a>
            </div>';
}


// Function to process user mentions //failed..
function processMentions($message) {
    $pattern = "/@(\w+)/";
}

//all message
$query = "SELECT c.iduser, c.message, c.media, u.name, u.photo, c.datetime
          FROM chat c
          JOIN user u ON c.iduser = u.id
          ORDER BY c.datetime ASC";

$stmt = $pdo->query($query);

$firstMessageOnDate = true;
$prevDate = null;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $message = $row['message'];
    $media = $row['media'];
    $userId = $row['iduser'];
    $userName = $row['name'];
    $datetime = $row['datetime'];
    $foto = $row['photo'];

    $isImage = false;
    $isVideo = false;

    $imageExtensions = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
    $videoExtensions = array('mp4', 'avi', 'mov', 'wmv', 'flv');

    $fileExtension = strtolower(pathinfo($media, PATHINFO_EXTENSION));

    if (in_array($fileExtension, $imageExtensions)) {
        $isImage = true;
    } elseif (in_array($fileExtension, $videoExtensions)) {
        $isVideo = true;
    }


    // Check if the date has changed and display it at the beginning of the chat messages
    if ($prevDate === null || date('Y-m-d', strtotime($datetime)) !== date('Y-m-d', strtotime($prevDate)) || $firstMessageOnDate) {
        echo "<div class='date-divider'><p>" . date('j F Y', strtotime($datetime)) . "</p></div>";
        $firstMessageOnDate = false;
    }

    $prevDate = $datetime;

    $messageId = "message-copy-$userId-$datetime";

    if (filter_var($foto, FILTER_VALIDATE_URL)) {
        // $foto is url
        $fotoprofile = "<div class='profile-image'><img src='$foto'></div>";
    } else {
        // $foto is not a url
        $fotoprofile = "<div class='profile-image'><img src='profile/$foto'></div>";
    }

    $deletemessage = '';
    if (isset($_SESSION['userId'])) {
        $currentUserId = $_SESSION['userId'];
        $isUserMessage = ($userId == $currentUserId); 
        // change bg-color of current user
        
        if ($isUserMessage) {
            $deletemessage = "<button onclick='deleteMessage($userId, \"$datetime\")' title='delete your message'>Delete</button>";
        }

        $messageWrapperClass = $isUserMessage ? 'user-message' : 'other-message';
        echo "<div class='message-wrapper $messageWrapperClass'>";
    } else {
        echo "<div class='message-wrapper'>";
    }

    $timenodate = date('H:i', strtotime($datetime)); //pake ini buat waktu saja
    $datetimenosecs = date('d/m/Y H:i', strtotime($datetime)); //pake ini buat waktu ama tgl

    echo "<div class='user-wrapper'>";
        echo "<div class='message-photonuser'>$fotoprofile
                 <p>$userName \n </p>
              </div>";
        echo "<div class='message-date-copy'>
                 <span>$datetimenosecs</span>";
        echo "   <button onclick='copymessage(\"$messageId\")' title='copy message'>copy</button>
                 $deletemessage
              </div>";
    echo "</div>";

    echo "<p>";

    // Display the media and the message if media field is not empty
    $fileAttachment = '';
    if (!empty($media)) {
        $fileAttachment = generateFileAttachment($media);
        if ($isImage === true) {
            echo "<img onclick=\"openImagePopup('$media');\" width='auto' height='300' src='$media' alt='Image'>";
        } elseif ($isVideo === true) {
            echo "<div class='video-preview'><a href='javascript:void(0);' onclick=\"openVideoPopup('$media');\"><img src='components/vector/play.svg'><p>play</p></p></a></div>";
        } else {
            if (!empty($fileAttachment)) {
                echo $fileAttachment;
            }
            // echo "<a href='$media' target='_blank'>[Download File] </a>";
        }
        echo "<span class='span-with-img' id='$messageId'>" . formatText($message) . "</span>";
    } else {
        echo "<span id='$messageId'>" . formatText($message) . "</span>";
    }
    echo "</p>";

    echo "</div>";
}
?>


<script>
function copymessage(messageId) {
  var messageElement = document.getElementById(messageId);
  var message = messageElement.innerText; // Use innerText to keep newline characters

  var tempInput = document.createElement("textarea");
  tempInput.value = message;
  document.body.appendChild(tempInput);
  tempInput.select();
  document.execCommand("copy");
  document.body.removeChild(tempInput);
}


// const messageInput = document.getElementById('message');

// messageInput.addEventListener('input', () => {
//     // Detect user mentions and format them
//     const messageText = messageInput.value;
//     const formattedMessage = formatMentions(messageText);
//     messageInput.value = formattedMessage;
// });

function deleteMessage(userId, datetime) {
    if (confirm("Are you sure you want to delete this message?")) {
        // Send an AJAX request to the server to delete the message
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'delete_message.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Handle the response, e.g., remove the deleted message from the UI
                const messageElement = document.getElementById(`message-copy-${userId}-${datetime}`);
                if (messageElement) {
                    messageElement.parentNode.remove();
                }
            }
        };
        xhr.send(`userId=${userId}&datetime=${datetime}`);
    }
}


</script>

