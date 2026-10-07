<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URED | Profile</title>
    <link rel="stylesheet" href="layout/base.css">
    <link rel="stylesheet" href="layout/app.css">
</head>
<body>
    <?php include __DIR__ . '/layout/navigation.php'; ?>

    <main class="page">
        <section class="profile-header">
            <?php if (!empty($user['profile_image'])): ?>
                <img src="<?= htmlspecialchars($user['profile_image']) ?>" class="profile-avatar" style="object-fit: cover;" alt="Avatar">
            <?php else: ?>
                <div class="profile-avatar">
                    <?= htmlspecialchars(strtoupper(substr($user['username'], 0, 1))) ?>
                </div>
            <?php endif; ?>
            <div>
                <h1 class="profile-name"><?= htmlspecialchars(!empty($user['full_name']) ? $user['full_name'] : $user['username']) ?></h1>
                <p style="margin: 0; color: var(--text-muted); font-size: 0.95rem;">@<?= htmlspecialchars($user['username']) ?></p>
                <p class="profile-bio"><?= htmlspecialchars(!empty($user['bio']) ? $user['bio'] : 'No bio yet.') ?></p>
                <div class="profile-stats">
                    <span><strong><?= (int) $postCount ?></strong> Posts</span>
                    <span>Joined <strong><?= htmlspecialchars($user['joined'] ?? 'Recently') ?></strong></span>
                </div>
                <?php if ((int) $user['id'] === (int) $currentUser['id']): ?>
                    <div style="margin-top: 0.8rem;">
                        <a href="profile_edit.php" class="btn btn-ghost" style="padding: 0.4rem 1rem; font-size: 0.85rem; text-decoration: none; display: inline-block;">Edit Profile</a>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <h2 class="page-title">Posts</h2>

        <div class="feed">
            <?php if (empty($posts)): ?>
                <p class="empty-state">No posts yet.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <article class="post-card">
                        <div class="post-header">
                            <?php if (!empty($user['profile_image'])): ?>
                                <img src="<?= htmlspecialchars($user['profile_image']) ?>" class="avatar" style="object-fit: cover;" alt="Avatar">
                            <?php else: ?>
                                <div class="avatar">
                                    <?= htmlspecialchars(strtoupper(substr($user['username'], 0, 1))) ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <p class="post-author"><?= htmlspecialchars($user['username']) ?></p>
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