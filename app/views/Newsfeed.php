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
    <?php include __DIR__ . '/layout/navigation.php'; ?>

    <main class="page">
        <form class="search-bar" method="GET" action="Newsfeed.php">
            <input type="text" name="q" placeholder="Search posts or users..." value="<?= htmlspecialchars($search ?? '') ?>">
            <button type="submit" class="btn btn-primary">Search</button>
            <?php if (!empty($search)): ?>
                <a href="Newsfeed.php" class="btn btn-ghost" style="text-decoration: none;">Clear</a>
            <?php endif; ?>
        </form>

        <form class="post-form" method="POST" action="post_form.php" enctype="multipart/form-data">
            <textarea name="content" placeholder="What's on your mind?" maxlength="500" required></textarea>
            <div class="form-actions">
                <input type="file" name="image" accept="image/png,image/jpeg,image/gif,image/webp">
                <button type="submit" class="btn btn-primary">Post</button>
            </div>
        </form>

        <h1 class="page-title"><?= !empty($search) ? 'Search Results' : 'Newsfeed' ?></h1>

        <div class="feed">
            <?php if (empty($posts)): ?>
                <p class="empty-state">No posts found.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <article class="post-card">
                        <div class="post-header">
                            <?php if (!empty($post['profile_image'])): ?>
                                <img src="<?= htmlspecialchars($post['profile_image']) ?>" class="avatar" style="object-fit: cover;" alt="Avatar">
                            <?php else: ?>
                                <div class="avatar">
                                    <?= htmlspecialchars(strtoupper(substr($post['username'], 0, 1))) ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <a href="Profile.php?id=<?= (int) $post['user_id'] ?>" class="post-author" style="text-decoration: none; color: inherit;">
                                    <?= htmlspecialchars(!empty($post['full_name']) ? $post['full_name'] : $post['username']) ?>
                                </a>
                                <p class="post-time"><?= htmlspecialchars($post['created_at']) ?></p>
                            </div>

                            <?php if ((int) $post['user_id'] === (int) $currentUser['id']): ?>
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
                                <button type="submit" class="btn btn-ghost like-btn <?= !empty($post['liked']) ? 'liked' : '' ?>">
                                    ♥ <?= (int) ($post['likes'] ?? 0) ?>
                                </button>
                            </form>
                            <span class="post-meta"><?= count($post['comments'] ?? []) ?> comments</span>
                        </div>

                        <?php include __DIR__ . '/comments.php'; ?>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/layout/foot.php'; ?>
</body>
</html>