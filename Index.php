<?php

require_once "includes/db.php";
require_once "includes/auth.php";

$stmt = $pdo->query("
    SELECT
        topics.id,
        topics.title,
        topics.created_at,
        users.username,
        (
            SELECT COUNT(*)
            FROM posts
            WHERE posts.topic_id = topics.id
        ) AS post_count,
        (
            SELECT COUNT(*)
            FROM post_likes
            JOIN posts ON post_likes.post_id = posts.id
            WHERE posts.topic_id = topics.id
        ) AS like_count

    FROM topics

    JOIN users ON topics.user_id = users.id

    ORDER BY topics.created_at DESC
");

$topics = $stmt->fetchAll();

?>

<h1>My Forum</h1>

<?php if (isset($_SESSION["user_id"])): ?>

    <p>
        Logged in as:
        <?= htmlspecialchars($_SESSION["username"], ENT_QUOTES, 'UTF-8') ?>
    </p>

    <a href="new_topic.php">Create a new post</a>

    <form action="logout.php" method="POST">
        <button type="submit">Logout</button>
    </form>

<?php else: ?>

    <a href="login.php">||Login|[----------]| </a>
    <a href="register.php">Register Account||</a>

<?php endif; ?>


<h1 class="forum-title">My Forum</h1>

<h2 class="section-title">General discussions</h2>

<?php foreach ($topics as $topic): ?>

    <div class="topic-card">

        <h3 class="topic-title">
            <a href="topic.php?id=<?= (int)$topic["id"] ?>">
                <?= htmlspecialchars($topic["title"], ENT_QUOTES, 'UTF-8') ?>
            </a>
        </h3>

        <p class="topic-info">
            Started by:
            <?= htmlspecialchars($topic["username"], ENT_QUOTES, 'UTF-8') ?>
        </p>

        <p class="topic-info">
            Total Posts: <?= (int)$topic["post_count"] ?>
        </p>

        <p class="topic-info">
            Total Likes: <?= (int)$topic["like_count"] ?>
        </p>

        <p class="topic-date">
            <?= htmlspecialchars($topic["created_at"], ENT_QUOTES, 'UTF-8') ?>
        </p>

    </div>

<?php endforeach; ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Forum</title>

    <link rel="stylesheet" href="css/style.css">
</head>