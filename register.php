<?php

require_once "includes/db.php";
require_once "includes/auth.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if (!preg_match('/\A[\p{L}\p{N}_-]{3,20}\z/u',$username)) {
        $error = "Username needs to be between 3 and 20 characters and can only contain letters, numbers, underscores or hyphens.";

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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register - My Forum</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="page-container">

    <a href="index.php" class="back-link">
        Return to forum
    </a>

    <div class="form-card">

        <h1>Register Your Account</h1>

        <form method="POST">

            <input
                type="text"
                name="username"
                minlength="3"
                maxlength="20"
                pattern="[A-Za-z0-9_åäöÅÄÖ-]+"
                title="3-20 characters. Letters, numbers, underscores and hyphens only."
                placeholder="Username"
                required
            >

            <input
                type="password"
                name="password"
                minlength="8"
                placeholder="Password (at least 8 characters)"
                required
            >

            <button type="submit">
                Register
            </button>

        </form>

        <?php if ($error !== ""): ?>

            <p class="error-message">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </p>

        <?php endif; ?>

        <p>
            Already have an account?
            <a href="login.php">Login</a>
        </p>

    </div>

</div>

</body>
</html>