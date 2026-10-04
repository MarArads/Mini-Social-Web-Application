<?php error_reporting(E_ALL & ~E_WARNING); ?>

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
    <?php include 'layout/navigation.php'; ?>

    <main class="page">
        <section class="profile-header">
            <div class="profile-avatar">
                <?= htmlspecialchars(strtoupper(substr($user['username'], 0, 1))) ?>
            </div>
            <div>
                <h1 class="profile-name"><?= htmlspecialchars($user['username']) ?></h1>
                <p class="profile-bio"><?= htmlspecialchars($user['bio'] ?? 'No bio yet.') ?></p>
                <div class="profile-stats">
                    <span><strong><?= (int) $postCount ?></strong> Posts</span>
                    <span>Joined <strong><?= htmlspecialchars($user['joined']) ?></strong></span>
                </div>
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
                            <div class="avatar">
                                <?= htmlspecialchars(strtoupper(substr($user['username'], 0, 1))) ?>
                            </div>
                            <div>
                                <p class="post-author"><?= htmlspecialchars($user['username']) ?></p>
                                <p class="post-time"><?= htmlspecialchars($post['created_at']) ?></p>
                            </div>
                        </div>
                        <p class="post-body"><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'layout/foot.php'; ?>
</body>
</html>