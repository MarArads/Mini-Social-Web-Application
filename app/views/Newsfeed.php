<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URED | Newsfeed</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="layout/base.css">
    <link rel="stylesheet" href="layout/app.css">
</head>
<body>
    <?php include __DIR__ . '/layout/navigation.php'; ?>

    <main class="page">
        <form class="search-bar" method="GET" action="Newsfeed.php">
            <input type="search" name="q" placeholder="Search posts or users..." maxlength="100" aria-label="Search" value="<?= htmlspecialchars($search ?? '') ?>">
            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> <span class="btn-text">Search</span></button>
            <?php if (!empty($search)): ?>
                <a href="Newsfeed.php" class="btn btn-ghost">Clear</a>
            <?php endif; ?>
        </form>

        <form class="post-form" method="POST" action="post_form.php" enctype="multipart/form-data" data-validate="post" novalidate>
            <?= csrf_field() ?>
            <textarea name="content" placeholder="What's on your mind?" maxlength="500" required aria-label="Write a post"></textarea>
            <div class="form-actions">
                <input type="file" name="image" accept="image/png,image/jpeg,image/gif,image/webp" data-max-mb="5">
                <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Post</button>
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
                                <img src="<?= htmlspecialchars($post['profile_image']) ?>" class="avatar" alt="Avatar">
                            <?php else: ?>
                                <div class="avatar">
                                    <?= htmlspecialchars(strtoupper(substr($post['username'], 0, 1))) ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <a href="Profile.php?id=<?= (int) $post['user_id'] ?>" class="post-author">
                                    <?= htmlspecialchars(!empty($post['full_name']) ? $post['full_name'] : $post['username']) ?>
                                </a>
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