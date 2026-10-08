<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MK Warehouse - Login</title>

    <link rel="stylesheet" href="bootstrap%20(1).css">
    <link rel="stylesheet" href="login.css?v=3">
</head>

<body>

    <div class="page">

        <div class="login-container">
            <?php if (isset($_GET['success'])): ?>

                <div class="success-message">
                    <?php echo htmlspecialchars($_GET['success']); ?>
                </div>

            <?php endif; ?>
            <div class="logo">
                <i class="fa-solid fa-warehouse"></i>
            </div>

            <h1>MK WAREHOUSE</h1>

            <p class="subtitle">
                Inventory Management System
            </p>

            <h2>Sign in</h2>

            <p class="description">
                Sign in with your email and password
            </p>

            <!-- Display login error -->
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <!-- LOGIN FORM -->
            <form action="login_process.php" method="POST">

                <label for="email">Your email</label>

                <input type="email" id="email" name="email" placeholder="Enter Email" required>

                <label for="password">Your password</label>

                <input type="password" id="password" name="password" placeholder="Enter Password" required>

                <div class="login-options">

                    <label class="remember">
                        <input type="checkbox" name="remember">
                        <span>Remember me</span>
                    </label>

                    <a href="#" class="forgot">
                        Forgot Password?
                    </a>

                </div>

                <button type="submit">
                    LOGIN
                </button>

            </form>

            <p class="register">
                Not a member?
                <a href="register.php">Register here!</a>
            </p>

        </div>

    </div>

<script src="login.js?v=1"></script>

</body>

</html>
