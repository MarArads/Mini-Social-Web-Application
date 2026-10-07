<?php include __DIR__ . '/layout/header.php'; ?>

<div class="auth-nav">
    <a href="Register.php" class="btn btn-primary">Register</a>
    <a href="Login.php" class="btn btn-ghost">Login</a>
</div>

<div class="auth-container">
    <div class="auth-card">
        <form method="POST" action="Register.php" enctype="multipart/form-data">
            <h1 class="auth-title">URED Register</h1>

            <?php if (!empty($error)): ?>
                <div style="background: #ffe5e5; color: #d63031; padding: 0.7rem; border-radius: var(--radius); margin-bottom: 1rem; text-align: center; font-size: 0.9rem;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <table>
                <tr>
                    <td><input type="text" name="full_name" placeholder="Full Name" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"></td>
                </tr>
                <tr>
                    <td><input type="text" name="username" placeholder="Username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"></td>
                </tr>
                <tr>
                    <td><input type="password" name="password" placeholder="Password (min 6 characters)" required></td>
                </tr>
                <tr>
                    <td><input type="text" name="bio" placeholder="Bio (optional)" value="<?= htmlspecialchars($_POST['bio'] ?? '') ?>"></td>
                </tr>
                <tr>
                    <td>
                        <label style="font-size: 0.85rem; color: var(--text-muted); display: block; margin-bottom: 0.3rem;">Profile Picture (optional):</label>
                        <input type="file" name="profile_image" accept="image/png,image/jpeg,image/gif,image/webp">
                    </td>
                </tr>
                <tr>
                    <td>
                        <button type="submit">Register</button>
                        <a href="Login.php" class="btn btn-ghost" style="text-decoration: none; padding: 0.7rem 1.4rem; font-size: 0.95rem;">Cancel</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<?php include __DIR__ . '/layout/foot.php'; ?>