<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/images/URED.png?v=1">
    <title>URED | Edit Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="layout/base.css">
    <link rel="stylesheet" href="layout/app.css">
</head>
<body>
    <?php include __DIR__ . '/layout/navigation.php'; ?>

    <main class="page">
        <h1 class="page-title">Edit Profile</h1>

        <form class="post-form" method="POST" action="profile_edit.php" enctype="multipart/form-data" data-validate="profile" novalidate>
            <?= csrf_field() ?>
            <label class="form-label" for="full_name">Full Name</label>
            <input class="form-input" type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" maxlength="100" required>

            <label class="form-label" for="bio">Bio</label>
            <textarea id="bio" name="bio" class="bio-input" placeholder="Tell us about yourself..." maxlength="255"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>

            <label class="form-label" for="profile_image">Profile Picture</label>
            <?php if (!empty($user['profile_image'])): ?>
                <div class="current-photo">
                    <img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="Current photo">
                </div>
            <?php endif; ?>
            <input type="file" id="profile_image" name="profile_image" accept="image/png,image/jpeg,image/gif,image/webp" data-max-mb="5">

            <div class="form-actions form-actions-spaced">
                <a href="Profile.php?id=<?= (int) $user['id'] ?>" class="btn btn-ghost">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Save Changes</button>
            </div>
        </form>
    </main>

    <?php include __DIR__ . '/layout/foot.php'; ?>
</body>
</html>