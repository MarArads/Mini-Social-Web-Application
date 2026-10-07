<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URED | Edit Profile</title>
    <link rel="stylesheet" href="layout/base.css">
    <link rel="stylesheet" href="layout/app.css">
</head>
<body>
    <?php include __DIR__ . '/layout/navigation.php'; ?>

    <main class="page">
        <h1 class="page-title">Edit Profile</h1>

        <form class="post-form" method="POST" action="profile_edit.php" enctype="multipart/form-data">
            <label style="font-weight: 700; font-size: 0.9rem;">Full Name</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required style="width: 100%; padding: 0.8rem 1rem; border-radius: var(--radius); border: 1px solid var(--border); background: var(--bg); color: var(--text);">

            <label style="font-weight: 700; font-size: 0.9rem; margin-top: 0.5rem;">Bio</label>
            <textarea name="bio" placeholder="Tell us about yourself..." style="min-height: 90px;"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>

            <label style="font-weight: 700; font-size: 0.9rem; margin-top: 0.5rem;">Profile Picture</label>
            <?php if (!empty($user['profile_image'])): ?>
                <div style="margin-bottom: 0.5rem;">
                    <img src="<?= htmlspecialchars($user['profile_image']) ?>" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;" alt="Current photo">
                </div>
            <?php endif; ?>
            <input type="file" name="profile_image" accept="image/png,image/jpeg,image/gif,image/webp">

            <div class="form-actions" style="margin-top: 1rem;">
                <a href="Profile.php?id=<?= (int) $user['id'] ?>" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </main>

    <?php include __DIR__ . '/layout/foot.php'; ?>
</body>
</html>
