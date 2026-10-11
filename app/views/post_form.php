<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URED | <?= !empty($post) ? 'Edit Post' : 'New Post' ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="layout/base.css">
    <link rel="stylesheet" href="layout/app.css">
</head>
<body>
    <?php include __DIR__ . '/layout/navigation.php'; ?>

    <main class="page">
        <h1 class="page-title"><?= !empty($post) ? 'Edit Post' : 'New Post' ?></h1>

        <form class="post-form" method="POST" action="post_form.php" enctype="multipart/form-data" data-validate="post" novalidate>
            <?= csrf_field() ?>
            <?php if (!empty($post)): ?>
                <input type="hidden" name="id" value="<?= (int) $post['id'] ?>">
            <?php endif; ?>

            <textarea name="content" placeholder="What's on your mind?" maxlength="500" required aria-label="Post content"><?= htmlspecialchars($post['content'] ?? '') ?></textarea>

            <?php if (!empty($post['image'])): ?>
                <div class="current-image">
                    <p>Current Image:</p>
                    <img src="<?= htmlspecialchars($post['image']) ?>" alt="Current image">
                </div>
            <?php endif; ?>

            <div class="form-actions">
                <input type="file" name="image" accept="image/png,image/jpeg,image/gif,image/webp" data-max-mb="5">
                <a href="Newsfeed.php" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi <?= !empty($post) ? 'bi-check-lg' : 'bi-send' ?>"></i> <?= !empty($post) ? 'Update Post' : 'Post' ?></button>
            </div>
        </form>
    </main>

    <?php include __DIR__ . '/layout/foot.php'; ?>
</body>
</html>