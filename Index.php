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
        ) AS post_count

    FROM topics

    JOIN users ON topics.user_id = users.id

    ORDER BY topics.created_at DESC
");

$topics = $stmt->fetchAll();

?>

<h1>My Forum</h1>

<?php if (isset($_SESSION["user_id"])): ?>

    <p>
        Inloggad som:
        <?= htmlspecialchars($_SESSION["username"]) ?>
    </p>

    <a href="new_topic.php">Create a new post</a>

    <form action="logout.php" method="POST">
        <button type="submit">Logout</button>
    </form>

<?php else: ?>

    <a href="login.php">Login</a>
    <a href="register.php">Register Account</a>

<?php endif; ?>


<h2>General discussions</h2>

<?php foreach ($topics as $topic): ?>

    <div class="topic">

        <h3>
            <a href="topic.php?id=<?= (int)$topic["id"] ?>">
                <?= htmlspecialchars($topic["title"]) ?>
            </a>
        </h3>

        <p>
            Started by:
            <?= htmlspecialchars($topic["username"]) ?>
        </p>

        <p>
            Total Posts: <?= $topic["post_count"] ?>
        </p>

        <p>
            <?= htmlspecialchars($topic["created_at"]) ?>
        </p>

    </div>

<?php endforeach; ?>