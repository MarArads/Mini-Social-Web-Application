<?php include __DIR__ . '/layout/header.php'; ?>

<div class="auth-nav">
    <a href="Register.php" class="btn btn-primary">Register</a>
    <a href="Login.php" class="btn btn-ghost">Login</a>
</div>

<div class="auth-container">
    <div class="auth-card">
        <form method="POST" action="Register.php" enctype="multipart/form-data" data-validate="register" novalidate>
            <?= csrf_field() ?>
            <h1 class="auth-title"><i class="bi bi-person-plus"></i> URED Register</h1>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <table>
                <tr>
                    <td>
                        <input type="text" name="full_name" placeholder="Full Name" maxlength="100" required autocomplete="name" aria-label="Full name" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="text" name="username" placeholder="Username (letters, numbers, _)" minlength="3" maxlength="50" pattern="[A-Za-z0-9_]{3,50}" required autocomplete="username" aria-label="Username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="password" name="password" placeholder="Password (min 6 characters)" minlength="6" maxlength="72" required autocomplete="new-password" aria-label="Password">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="password" name="confirm_password" placeholder="Confirm password" minlength="6" maxlength="72" required autocomplete="new-password" aria-label="Confirm password">
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="text" name="bio" placeholder="Bio (optional)" maxlength="255" aria-label="Bio" value="<?= htmlspecialchars($_POST['bio'] ?? '') ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label class="form-hint" for="profile_image">Profile Picture (optional):</label>
                        <input type="file" id="profile_image" name="profile_image" accept="image/png,image/jpeg,image/gif,image/webp" data-max-mb="5">
                    </td>
                </tr>
                <tr>
                    <td class="auth-buttons">
                        <button type="submit">Register</button>
                        <a href="Login.php" class="btn btn-ghost btn-auth">Cancel</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<?php include __DIR__ . '/layout/foot.php'; ?>