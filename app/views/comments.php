<section class="comments">
    <?php foreach ($post['comments'] as $comment): ?>
        <div class="comment">
            <div class="avatar">
                <?= htmlspecialchars(strtoupper(substr($comment['username'], 0, 1))) ?>
            </div>
            <div class="comment-bubble">
                <p class="comment-author">
                    <?= htmlspecialchars($comment['username']) ?>
                    <span class="post-time"><?= htmlspecialchars($comment['created_at']) ?></span>
                </p>
                <p class="comment-text"><?= htmlspecialchars($comment['content']) ?></p>

                <?php if ($comment['user_id'] === $currentUser['id']): ?>
                    <div class="comment-actions">
                        <a href="comment_edit.php?id=<?= (int) $comment['id'] ?>">Edit</a>
                        <form method="POST" action="comment_delete.php" onsubmit="return confirm('Delete this comment?');">
                            <input type="hidden" name="comment_id" value="<?= (int) $comment['id'] ?>">
                            <button type="submit" class="link-btn danger">Delete</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <form class="comment-form" method="POST" action="comment_add.php">
        <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
        <input type="text" name="content" placeholder="Write a comment..." maxlength="300" required>
        <button type="submit" class="btn btn-primary">Send</button>
    </form>
</section>