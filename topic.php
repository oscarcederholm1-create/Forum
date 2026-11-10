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


// Hantera nya svar
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    require_login();

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

        // Ladda om sidan efter att svaret sparats
        header("Location: topic.php?id=" . $topic_id);
        exit;
    }
}


$stmt = $pdo->prepare("
    SELECT posts.*, users.username
    FROM posts
    LEFT JOIN users ON posts.user_id = users.id
    WHERE posts.topic_id = ?
    ORDER BY posts.created_at ASC
");

$stmt->execute([$topic_id]);

$posts = $stmt->fetchAll();

?>

<a href="index.php">Return to post</a>

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