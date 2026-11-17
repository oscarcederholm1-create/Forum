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

    if ($content !== "") {

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

<a href="index.php">Return to forum</a>

<h1><?= htmlspecialchars($topic["title"]) ?></h1>

<p>
    Started by:
    <?= htmlspecialchars($topic["username"]) ?>
</p>


<h2>Posts</h2>

<?php foreach ($posts as $post): ?>

    <div class="post">

        <p>
            <strong>
                <?= htmlspecialchars($post["username"] ?? "Former user") ?>
            </strong>
        </p>

        <p>
            <?= nl2br(htmlspecialchars($post["content"])) ?>
        </p>

        <small>
            <?= htmlspecialchars($post["created_at"]) ?>
        </small>

        <p>
            <?= (int)$post["like_count"] ?> likes
        </p>

        <?php if (isset($_SESSION["user_id"])): ?>

            <form method="POST">
                <input type="hidden" name="action" value="like">

                <input type="hidden"
                    name="post_id"
                    value="<?= (int)$post["id"] ?>">

                <button type="submit">Like / Unlike</button>
            </form>

        <?php endif; ?>
    </div>

<?php endforeach; ?>


<?php if (isset($_SESSION["user_id"])): ?>

    <h2>Write an answer</h2>

    <form method="POST">

        <textarea name="content"
                  placeholder="Send your answer..."
                  required></textarea>

        <button type="submit">Send reply</button>

    </form>

<?php else: ?>

    <p>
        <a href="login.php">Login</a>
        You need to log in to send a message.
    </p>

<?php endif; ?>
