<?php
require_once __DIR__ . '/../../helpers/Security.php';
$flash = pull_flash();
?>
<nav class="header">
    <a href="Newsfeed.php" class="logo"><i class="bi bi-people-fill"></i> URED</a>

    <div class="display-wrapper">
        <a href="Newsfeed.php" class="nav-link" title="Newsfeed"><i class="bi bi-house-door"></i><span class="nav-text">Newsfeed</span></a>
        <a href="post_form.php" class="nav-link" title="New Post"><i class="bi bi-plus-square"></i><span class="nav-text">New Post</span></a>
        <a href="Profile.php" class="nav-link" title="Profile"><i class="bi bi-person-circle"></i><span class="nav-text">Profile</span></a>
        <button class="toggle-theme" id="themeToggle" type="button" aria-label="Toggle dark mode">🌙</button>
    </div>
</nav>

<?php if (!empty($flash)): ?>
    <div class="flash flash-<?= $flash['type'] === 'success' ? 'success' : 'error' ?>" role="alert">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>