<?php include './layout/header.php'; ?>
    <div class="auth-nav">
        <a href="Register.php" class="btn btn-primary">Register</a>
        <a href="Login.php" class="btn btn-ghost">Login</a>
    </div>


    <div class="auth-container">
        <div class="auth-card">
        <form method="POST" action="register.php">
            <h1 class="auth-title">URED Register</h1>
            <table>
        <tr>
            <td><input type="text" name="username" placeholder="Username"></td>
        </tr>
        <tr>
            <td><input type="password" name="password" placeholder="Password"></td>
        </tr>
        <tr>
            <td>
                <button type="submit">Register</button>
                <button type="button">Cancel</button>
            </td>
        </tr>
    </table>
    </div>
</div>
</form>
    
<?php include './layout/foot.php'; ?>