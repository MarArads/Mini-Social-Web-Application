<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/images/URED.png?v=1">
    <title>URED | Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="layout/base.css">
    <link rel="stylesheet" href="layout/app.css">
</head>
<body>
    <?php include __DIR__ . '/layout/navigation.php'; ?>

    <main class="page">
        <section class="profile-header">
            <?php if (!empty($user['profile_image'])): ?>
                <img src="<?= htmlspecialchars($user['profile_image']) ?>" class="profile-avatar" alt="Avatar">
            <?php else: ?>
                <div class="profile-avatar">
                    <?= htmlspecialchars(strtoupper(substr($user['username'], 0, 1))) ?>
                </div>
            <?php endif; ?>
            <div>
                <h1 class="profile-name"><?= htmlspecialchars(!empty($user['full_name']) ? $user['full_name'] : $user['username']) ?></h1>
                <p class="profile-username">@<?= htmlspecialchars($user['username']) ?></p>
                <p class="profile-bio"><?= htmlspecialchars(!empty($user['bio']) ? $user['bio'] : 'No bio yet.') ?></p>
                <div class="profile-stats">
                    <span><strong><?= (int) $postCount ?></strong> Posts</span>
                    <span>Joined <strong><?= htmlspecialchars($user['joined'] ?? 'Recently') ?></strong></span>
                </div>
            <?php if ((int) $user['id'] === (int) $currentUser['id']): ?>
            <div class="profile-actions">
                <a href="profile_edit.php" class="btn btn-ghost btn-small"><i class="bi bi-pencil-square"></i> Edit Profile</a>
                <form method="POST" action="Logout.php" class="logout-form">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-ghost btn-small"><i class="bi bi-box-arrow-right"></i> Log out</button>
               </form>
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
                                <img src="<?= htmlspecialchars($user['profile_image']) ?>" class="avatar" alt="Avatar">
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
                                    <a href="post_form.php?id=<?= (int) $post['id'] ?>"><i class="bi bi-pencil"></i> Edit</a>
                                    <form method="POST" action="post_delete.php" onsubmit="return confirm('Delete this post?');">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
                                        <button type="submit" class="link-btn danger"><i class="bi bi-trash"></i> Delete</button>
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
                                <?= csrf_field() ?>
                                <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
                                <button type="submit" class="btn btn-ghost like-btn <?= !empty($post['liked']) ? 'liked' : '' ?>" aria-label="Like this post">
                                    <i class="bi <?= !empty($post['liked']) ? 'bi-heart-fill' : 'bi-heart' ?>"></i> <?= (int) ($post['likes'] ?? 0) ?>
                                </button>
                            </form>
                            <?php $commentCount = count($post['comments'] ?? []); ?>
                            <span class="post-meta"><i class="bi bi-chat"></i> <?= $commentCount ?> <?= $commentCount === 1 ? 'comment' : 'comments' ?></span>
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