<?php

require_once "includes/db.php";
require_once "includes/auth.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if (mb_strlen($username) < 3 || mb_strlen($username) > 50) {
        $error = "Username needs to be between 3 and 50 characters.";

    } elseif (strlen($password) < 8) {
        $error = "Password needs to be at least 8 characters.";

    } else {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo->prepare(
                "INSERT INTO users (username, password)
                 VALUES (?, ?)"
            );

            $stmt->execute([$username, $hash]);

            header("Location: login.php");
            exit;

        } catch (PDOException $e) {
            $error = "Username is already taken.";
        }
    }
}
?>

<h1>Registrera konto</h1>

<form method="POST">
    <input type="text" name="username"
           placeholder="Username" required>

    <input type="password" name="password"
           placeholder="Password" required>

    <button type="submit">Register</button>
</form>

<p><?= htmlspecialchars($error) ?></p>