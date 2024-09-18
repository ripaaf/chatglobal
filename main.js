$(function () {
    var chatMessages = $("#chat-messages");
    var chatContainer = document.getElementById("chat-container");

    function updateChat() {
        var isAtBottom = chatContainer.scrollHeight - chatContainer.scrollTop === chatContainer.clientHeight;

        // Fetch and display chat messages
        $.get("fetch.php", function (data) {
            var scrollNeeded = isAtBottom; // Check if scrolling is needed

            chatMessages.html(data);

            if (scrollNeeded) {
                scrollChatToBottom();
            }

            setTimeout(updateChat, 1000);
        });
    }
    updateChat();
    const timeoutId = setTimeout(scrollChatToBottom, 300);

    setTimeout(() => {
    clearTimeout(timeoutId);
    }, 600);

    //send message and handle file uploading
    $("#send").on("click", function () {
        var message = $("#message").val();
        var fileInput = document.getElementById('file');
        var file = fileInput.files[0];
    
        if (message.trim() !== "" || file) {
            var formData = new FormData();
            formData.append('message', message);
            formData.append('file', file);
    
            $.ajax({
                // url: "send_chat_message.php",
                url: "send_message.php",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function () {
                    updateChat();
                    $("#message").val("");
                    $("#file").val("");
                    setTimeout(scrollChatToBottom, 100);
                }
            });
        }
    });
    

    // Log In
    $("#login").on("click", function () {
        window.location.href = 'login.php';
    });

    // Chat Anonymously
    // $("#chat-anonymously").on("click", function () {
    //     var expirationDate = new Date();
    //     expirationDate.setDate(expirationDate.getDate() + 30); // Set the expiration date to 30 days from the current date
    //     var expires = "expires=" + expirationDate.toUTCString();
    //     document.cookie = "chatOption=anonymous; " + expires + "; path=/";
    //     location.reload();
    // });

    $("#chat-anonymously").on("click", function () {
        var expirationDate = new Date();
        expirationDate.setDate(expirationDate.getDate() + 30);
        var expires = "expires=" + expirationDate.toUTCString();
        document.cookie = "chatOption=anonymous; " + expires + "; path=/";
    
        $.ajax({
            url: 'create_anonym_user.php',
            type: 'POST', 
            success: function (data) {
                location.reload();
            },
            error: function (xhr, status, error) {
                console.error("Error creating anonymous user: " + error);
            }
        });
    });
    

});


function scrollChatToBottom() {
    var chatMessages = document.getElementById("chat-container");
    chatMessages.scrollTop = chatMessages.scrollHeight;
}




// function openVideoPopup(videoUrl) {
//     var width = 800; // Set the desired width for the popup
//     var height = 600; // Set the desired height for the popup

//     var left = (screen.width - width) / 2;
//     var top = (screen.height - height) / 2;

//     // Open a new popup window with the video player
//     var videoPopup = window.open('', 'VideoPopup', 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top);
//     videoPopup.document.write("<video controls width='100%' height='100%'><source src='" + videoUrl + "' type='video/mp4'></video>");
// }



function openVideoPopup(videoUrl) {
    var modal = document.getElementById("videoPopup");
    var modalVideo = document.getElementById("popupVideo");
    modal.style.display = "block";
    modalVideo.src = videoUrl;
    modalVideo.load();
    modalVideo.play();
  }
  
  // Function to close the video popup
  function closeVideoPopup() {
    var modal = document.getElementById("videoPopup");
    var modalVideo = document.getElementById("popupVideo");
    modal.style.display = "none";
    modalVideo.src = "";
  }
  
  // Function to close the video popup when clicking outside
  window.addEventListener("click", function (event) {
    var modal = document.getElementById("videoPopup");
    if (event.target == modal) {
      closeVideoPopup();
    }
  });






// Function to open the image popup
function openImagePopup(imageUrl) {
    var modal = document.getElementById("imagePopup");
    var modalImg = document.getElementById("popupImage");
    modal.style.display = "block";
    modalImg.src = imageUrl;
  }
  
  // Function to close the image popup
  function closeImagePopup() {
    var modal = document.getElementById("imagePopup");
    modal.style.display = "none";
  }
  

    // Function to close the image popup when clicking outside
    window.addEventListener("click", function (event) {
        var modal = document.getElementById("imagePopup");
        if (event.target == modal) {
        closeImagePopup();
        }
    });
  




// const resizeableDiv = document.getElementById("chat-containe");
// let isResizing = false;

// resizeableDiv.addEventListener("mousedown", (e) => {
//   isResizing = true;
//   e.preventDefault();

//   const initialHeight = resizeableDiv.offsetHeight;
//   const initialY = e.clientY;

//   document.addEventListener("mousemove", resize);
//   document.addEventListener("mouseup", stopResize);

//   function resize(e) {
//     if (isResizing) {
//       const height = initialHeight + (e.clientY - initialY);
//       resizeableDiv.style.height = `${height}px`;
//     }
//   }

//   function stopResize() {
//     isResizing = false;
//     document.removeEventListener("mousemove", resize);
//     document.removeEventListener("mouseup", stopResize);
//   }
// });
