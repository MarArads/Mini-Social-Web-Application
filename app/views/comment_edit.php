<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URED | Edit Comment</title>
    <link rel="stylesheet" href="layout/base.css">
    <link rel="stylesheet" href="layout/app.css">
</head>
<body>
    <?php include __DIR__ . '/layout/navigation.php'; ?>

    <main class="page">
        <h1 class="page-title">Edit Comment</h1>

        <form class="post-form" method="POST" action="comment_edit.php">
            <input type="hidden" name="id" value="<?= (int) $comment['id'] ?>">
            <textarea name="content" maxlength="300" required><?= htmlspecialchars($comment['content']) ?></textarea>

            <div class="form-actions">
                <a href="Newsfeed.php" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Comment</button>
            </div>
        </form>
    </main>

    <?php include __DIR__ . '/layout/foot.php'; ?>
</body>
</html>
