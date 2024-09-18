<?php
session_start();

if (isset($_SESSION['userId'])) {
    echo "logged_in";
} else {
    echo "not_logged_in";
}
?>