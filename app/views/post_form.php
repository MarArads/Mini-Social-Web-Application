<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URED | New Post</title>
    <link rel="stylesheet" href="layout/base.css">
    <link rel="stylesheet" href="layout/app.css">
</head>
<body>
    <?php include 'layout/navigation.php'; ?>

    <main class="page">
        <h1 class="page-title">New Post</h1>

        <form class="post-form" method="POST" action="post_form.php">
            <textarea name="content" placeholder="What's on your mind?" maxlength="500" required></textarea>

            <div class="form-actions">
                <a href="Newsfeed.php" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Post</button>
            </div>
        </form>
    </main>

    <?php include 'layout/foot.php'; ?>
</body>
</html>