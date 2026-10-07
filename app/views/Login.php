<?php include __DIR__ . '/layout/header.php'; ?>

<div class="auth-nav">
    <a href="Register.php" class="btn btn-ghost">Register</a>
    <a href="Login.php" class="btn btn-primary">Login</a>
</div>

<div class="auth-container">
    <div class="auth-card">
        <form method="POST" action="Login.php">
            <h1 class="auth-title">URED Log in</h1>

            <?php if (!empty($error)): ?>
                <div style="background: #ffe5e5; color: #d63031; padding: 0.7rem; border-radius: var(--radius); margin-bottom: 1rem; text-align: center; font-size: 0.9rem;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div style="background: #e3fcef; color: #00875a; padding: 0.7rem; border-radius: var(--radius); margin-bottom: 1rem; text-align: center; font-size: 0.9rem;">
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <table>
                <tr>
                    <td><input type="text" name="username" placeholder="Username" required autofocus></td>
                </tr>
                <tr>
                    <td><input type="password" name="password" placeholder="Password" required></td>
                </tr>
                <tr>
                    <td>
                        <button type="submit">Log in</button>
                        <a href="Register.php" class="btn btn-ghost" style="text-decoration: none; padding: 0.7rem 1.4rem; font-size: 0.95rem;">Register</a>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<?php include __DIR__ . '/layout/foot.php'; ?>