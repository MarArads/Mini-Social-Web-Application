<section class="comments">
    <?php if (!empty($post['comments'])): ?>
        <?php foreach ($post['comments'] as $comment): ?>
            <div class="comment">
                <?php if (!empty($comment['profile_image'])): ?>
                    <img src="<?= htmlspecialchars($comment['profile_image']) ?>" class="avatar" alt="Avatar">
                <?php else: ?>
                    <div class="avatar">
                        <?= htmlspecialchars(strtoupper(substr($comment['username'], 0, 1))) ?>
                    </div>
                <?php endif; ?>
                <div class="comment-bubble">
                    <p class="comment-author">
                        <a href="Profile.php?id=<?= (int) $comment['user_id'] ?>" class="comment-author-link">
                            <?= htmlspecialchars(!empty($comment['full_name']) ? $comment['full_name'] : $comment['username']) ?>
                        </a>
                        <span class="post-time"><?= htmlspecialchars($comment['created_at']) ?></span>
                    </p>
                    <p class="comment-text"><?= htmlspecialchars($comment['content']) ?></p>

                    <?php if ((int) $comment['user_id'] === (int) $currentUser['id']): ?>
                        <div class="comment-actions">
                            <a href="comment_edit.php?id=<?= (int) $comment['id'] ?>"><i class="bi bi-pencil"></i> Edit</a>
                            <form method="POST" action="comment_delete.php" onsubmit="return confirm('Delete this comment?');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="comment_id" value="<?= (int) $comment['id'] ?>">
                                <button type="submit" class="link-btn danger"><i class="bi bi-trash"></i> Delete</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <form class="comment-form" method="POST" action="comment_add.php" data-validate="comment" novalidate>
        <?= csrf_field() ?>
        <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
        <input type="text" name="content" placeholder="Write a comment..." maxlength="300" required aria-label="Write a comment">
        <button type="submit" class="btn btn-primary" aria-label="Send comment"><i class="bi bi-send"></i> <span class="btn-text">Send</span></button>
    </form>
</section>