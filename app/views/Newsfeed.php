<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URED | Newsfeed</title>
    <link rel="stylesheet" href="layout/base.css">
    <link rel="stylesheet" href="layout/app.css">
</head>
<body>
    <?php include 'layout/navigation.php'; ?>

    <main class="page">
        <form class="search-bar" method="GET" action="Newsfeed.php">
            <input type="text" name="q" placeholder="Search posts..." value="<?= htmlspecialchars($search ?? '') ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <form class="post-form" method="POST" action="post_form.php" enctype="multipart/form-data">
            <textarea name="content" placeholder="What's on your mind?" maxlength="500" required></textarea>
            <div class="form-actions">
                <input type="file" name="image" accept="image/*">
                <button type="submit" class="btn btn-primary">Post</button>
            </div>
        </form>

        <h1 class="page-title">Newsfeed</h1>

        <div class="feed">
            <?php if (empty($posts)): ?>
                <p class="empty-state">No posts yet. Be the first to post.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <article class="post-card">
                        <div class="post-header">
                            <div class="avatar">
                                <?= htmlspecialchars(strtoupper(substr($post['username'], 0, 1))) ?>
                            </div>
                            <div>
                                <p class="post-author"><?= htmlspecialchars($post['username']) ?></p>
                                <p class="post-time"><?= htmlspecialchars($post['created_at']) ?></p>
                            </div>

                            <?php if ($post['user_id'] === $currentUser['id']): ?>
                                <div class="post-owner-actions">
                                    <a href="post_form.php?id=<?= (int) $post['id'] ?>">Edit</a>
                                    <form method="POST" action="post_delete.php" onsubmit="return confirm('Delete this post?');">
                                        <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
                                        <button type="submit" class="link-btn danger">Delete</button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>

                        <p class="post-body"><?= nl2br(htmlspecialchars($post['content'])) ?></p>

                        <?php if (!empty($post['image'])): ?>
                            <img class="post-image" src="<?= htmlspecialchars($post['image']) ?>" alt="Post image">
                        <?php endif; ?>

                        <div class="post-actions">
                            <form method="POST" action="like.php">
                                <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
                                <button type="submit" class="btn btn-ghost like-btn <?= $post['liked'] ? 'liked' : '' ?>">
                                    ♥ <?= (int) $post['likes'] ?>
                                </button>
                            </form>
                            <span class="post-meta"><?= count($post['comments']) ?> comments</span>
                        </div>

                        <?php include 'comments.php'; ?>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'layout/foot.php'; ?>
</body>
</html>