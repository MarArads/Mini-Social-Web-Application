<?php include __DIR__ . '/layout/header.php'; ?>

<div class="auth-nav">
    <a href="Register.php" class="btn btn-ghost">Register</a>
    <a href="Login.php" class="btn btn-primary">Login</a>
</div>

<div class="auth-container">
    <div class="auth-card">
        <form method="POST" action="Login.php" data-validate="login" novalidate>
            <?= csrf_field() ?>
            <h1 class="auth-title"><i class="bi bi-box-arrow-in-right"></i> URED Log in</h1>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success" role="status">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <table>
                <tr>
                    <td><input type="text" name="username" placeholder="Username" required autofocus autocomplete="username" aria-label="Username"></td>
                </tr>
                <tr>
                    <td><input type="password" name="password" placeholder="Password" required autocomplete="current-password" aria-label="Password"></td>
                </tr>
                <tr>
                    <td class="auth-buttons">
                        <button type="submit">Log in</button>
                        <a href="Register.php" class="btn btn-ghost btn-auth">Register</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<?php include __DIR__ . '/layout/foot.php'; ?>