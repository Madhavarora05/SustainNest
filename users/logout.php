<?php
session_start();

session_unset();

session_destroy();

if (isset($_GET['redirect_to'])) {
    $current_url = urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
    header("Location: users/login.php?redirect_to=$current_url");
} else {

    header("Location: ../blog_home.php");
}

exit();
?>
