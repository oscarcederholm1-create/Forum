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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - My Forum</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="page-container">

    <a href="index.php" class="back-link">
        Return to forum
    </a>

    <div class="form-card">

        <h1>Log in to your account</h1>

        <form method="POST">

            <input
                type="text"
                name="username"
                placeholder="Username"
                required
            >

            <input
                type="password"
                name="password"
                placeholder="Password"
                required
            >

            <button type="submit">
                Login
            </button>

        </form>

        <?php if ($error !== ""): ?>

            <p class="error-message">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </p>

        <?php endif; ?>

        <p>
            Don't have an account?
            <a href="register.php">Register</a>
        </p>

    </div>

</div>

</body>
</html>