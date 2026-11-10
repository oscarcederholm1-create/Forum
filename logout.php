<?php

require_once "includes/auth.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$_SESSION = [];

session_destroy();

header("Location: login.php");
exit;