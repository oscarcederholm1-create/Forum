<?php

require_once "includes/db.php";
require_once "includes/auth.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $pdo->prepare(
        "SELECT * FROM users WHERE username = ?"
    );

    $stmt->execute([$username]);

    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {

        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];

        header("Location: index.php");
        exit;

    } else {
        $error = "Wrong password or username.";
    }
}
?>

<h1>Log in to your account</h1>

<form method="POST">
    <input type="text" name="username"
           placeholder="username" required>

    <input type="password" name="password"
           placeholder="Password" required>

    <button type="submit">Login</button>
</form>

<p><?= htmlspecialchars($error) ?></p>