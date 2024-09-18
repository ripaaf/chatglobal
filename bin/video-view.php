<?php
if (isset($_GET['video'])) {
    $videoUrl = $_GET['video'];
    echo "<video controls width='640' height='360'>
              <source src='$videoUrl' type='video/mp4'>
              Your browser does not support the video tag.
          </video>";
} else {
    echo "Video URL not provided.";
}
?>



