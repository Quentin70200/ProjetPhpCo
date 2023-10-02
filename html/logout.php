<?php

header('X-Frame-Options: DENY');

session_start();

if (isset($_COOKIE['rememberMeCookie'])) {
    setcookie('rememberMeCookie', '', time() - 3600, '/');
}

session_destroy();

header("Location: login.php");
exit;

?>
