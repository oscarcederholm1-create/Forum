<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION["user_id"]) && (int) $_SESSION["user_id"] <= 0) {
    unset($_SESSION["user_id"], $_SESSION["username"]);
}

function require_login() {
    if (!isset($_SESSION["user_id"]) || (int) $_SESSION["user_id"] <= 0) {
        header("Location: login.php");
        exit;
    }
}