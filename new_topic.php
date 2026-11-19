<?php

require_once "includes/db.php";
require_once "includes/auth.php";

require_login();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $content = trim($_POST["content"] ?? "");

    if ($title === "" || $content === "") {

        $error = "You need to write both title and content.";

    } else {

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare(
                "INSERT INTO topics (user_id, title)
                 VALUES (?, ?)"
            );

            $stmt->execute([
                $_SESSION["user_id"],
                $title
            ]);

            $topic_id = $pdo->lastInsertId();

            $stmt = $pdo->prepare(
                "INSERT INTO posts (topic_id, user_id, content)
                 VALUES (?, ?, ?)"
            );

            $stmt->execute([
                $topic_id,
                $_SESSION["user_id"],
                $content
            ]);

            $pdo->commit();

            header("Location: topic.php?id=" . $topic_id);
            exit;

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
            $pdo->rollBack();
            }
            $error = "Couldn't create discussion.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Discussion - My Forum</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="page-container">

    <a href="index.php" class="back-link">
        Return to forum
    </a>

    <div class="form-card">

        <h1>Create New Discussion</h1>

        <form method="POST">

            <input
                type="text"
                name="title"
                placeholder="Enter the discussion title"
                required
            >

            <textarea
                name="content"
                placeholder="Write your post..."
                required
            ></textarea>

            <button type="submit">
                Submit
            </button>

        </form>

        <?php if ($error !== ""): ?>

            <p class="error-message">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </p>

        <?php endif; ?>

    </div>

</div>

</body>
</html>