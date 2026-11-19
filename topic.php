<?php

require_once "includes/db.php";
require_once "includes/auth.php";

$topic_id = (int)($_GET["id"] ?? 0);

if ($topic_id <= 0) {
    die("Invalid post.");
}

$stmt = $pdo->prepare("
    SELECT topics.*, users.username
    FROM topics
    JOIN users ON topics.user_id = users.id
    WHERE topics.id = ?
");

$stmt->execute([$topic_id]);

$topic = $stmt->fetch();

if (!$topic) {
    http_response_code(404);
    die("Post was not found.");
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    require_login();

    if (($_POST["action"] ?? "") === "like") {

    $post_id = (int)($_POST["post_id"] ?? 0);

    $stmt = $pdo->prepare(
        "SELECT id FROM posts WHERE id = ? AND topic_id = ?"
    );
    $stmt->execute([$post_id, $topic_id]);

    if ($stmt->fetch()) {

        $stmt = $pdo->prepare(
            "SELECT 1 FROM post_likes
             WHERE post_id = ? AND user_id = ?"
        );
        $stmt->execute([$post_id, $_SESSION["user_id"]]);

        if ($stmt->fetch()) {
            $stmt = $pdo->prepare(
                "DELETE FROM post_likes
                 WHERE post_id = ? AND user_id = ?"
            );
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO post_likes (post_id, user_id)
                 VALUES (?, ?)"
            );
        }

        $stmt->execute([$post_id, $_SESSION["user_id"]]);
    }

    header("Location: topic.php?id=" . $topic_id);
    exit;
    }

    $content = trim($_POST["content"] ?? "");

    if (mb_strlen($content) >= 2 && mb_strlen($content) <= 50) {

        $stmt = $pdo->prepare("
            INSERT INTO posts (topic_id, user_id, content)
            VALUES (?, ?, ?)
        ");

        $stmt->execute([
            $topic_id,
            $_SESSION["user_id"],
            $content
        ]);

        header("Location: topic.php?id=" . $topic_id);
        exit;
    }
}


$stmt = $pdo->prepare("
    SELECT posts.*, users.username,
        (
            SELECT COUNT(*)
            FROM post_likes
            WHERE post_likes.post_id = posts.id
        ) AS like_count

    FROM posts

    LEFT JOIN users ON posts.user_id = users.id

    WHERE posts.topic_id = ?

    ORDER BY posts.created_at ASC
");

$stmt->execute([$topic_id]);

$posts = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($topic["title"], ENT_QUOTES, 'UTF-8') ?>
    </title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="page-container">

    <a href="index.php" class="back-link">
        Return to forum
    </a>
<h1>
        <?= htmlspecialchars($topic["title"], ENT_QUOTES, 'UTF-8') ?>
    </h1>

    <p class="topic-info">
        Started by:
        <?= htmlspecialchars($topic["username"], ENT_QUOTES, 'UTF-8') ?>
    </p>


    <h2 class="section-title">Posts</h2>


    <?php foreach ($posts as $post): ?>

        <div class="post">

            <p>
                <strong>
                    <?= htmlspecialchars(
                        $post["username"] ?? "Former user",
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>
                </strong>
            </p>

            <p>
                <?= nl2br(
                    htmlspecialchars($post["content"], ENT_QUOTES, 'UTF-8')
                ) ?>
            </p>

            <small>
                <?= htmlspecialchars($post["created_at"], ENT_QUOTES, 'UTF-8') ?>
            </small>

            <p>
                <?= (int)$post["like_count"] ?> likes
            </p>


            <?php if (isset($_SESSION["user_id"])): ?>

                <form method="POST" class="post-actions">

                    <input
                        type="hidden"
                        name="action"
                        value="like"
                    >

                    <input
                        type="hidden"
                        name="post_id"
                        value="<?= (int)$post["id"] ?>"
                    >

                    <button type="submit">
                        Like / Unlike
                    </button>

                </form>

            <?php endif; ?>

        </div>

    <?php endforeach; ?>


    <?php if (isset($_SESSION["user_id"])): ?>

        <div class="form-card">

            <h2>Write an reply</h2>

            <form method="POST">

                <textarea
                    name="content"
                    placeholder="Send your reply..."
                    minlength="2"
                    maxlength="50"
                    required
                ></textarea>

                <button type="submit">
                    Send reply
                </button>

            </form>

        </div>

    <?php else: ?>

        <div class="form-card">

            <p>
                <a href="login.php">
                    Login
                </a>
                You need to log in to send a message
            </p>

        </div>

    <?php endif; ?>

</div>

</body>
</html>