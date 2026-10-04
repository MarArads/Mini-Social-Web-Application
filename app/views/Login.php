<?php include './layout/header.php'; ?>
    <div class="auth-nav">
    <a href="Register.php" class="btn btn-ghost">Register</a>
    <a href="Login.php" class="btn btn-primary">Login</a>
</div>

    <div class="auth-container">
        <form method="GET" action="Login.php">
            <h1 class="auth-title">URED Log in</h1>
            <table>
        <tr>
            <td><input type="text" name="username" placeholder="Username"></td>
        </tr>
        <tr>
            <td><input type="password" name="password" placeholder="Password"></td>
        </tr>
        <tr>
            <td>
                <button type="submit">Log in</button>
                <button type="button">Cancel</button>
            </td>
        </tr>
    </table>
    </div>
</div>
</form>
    
<?php include './layout/foot.php'; ?>