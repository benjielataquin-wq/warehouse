<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MK Warehouse - Create Account</title>

    <link rel="stylesheet" href="bootstrap%20(1).css">
    <link rel="stylesheet" href="login.css?v=3">

</head>

<body>

    <div class="page">

        <div class="login-container">

            <!-- LOGO -->
            <div class="logo">
                <i class="fa-solid fa-warehouse"></i>
            </div>

            <!-- TITLE -->
            <h1>MK WAREHOUSE</h1>

            <p class="subtitle">
                Inventory Management System
            </p>

            <!-- CREATE ACCOUNT -->
            <h2>Create Account</h2>

            <p class="description">
                Create your account to access the warehouse system
            </p>

            <!-- ERROR MESSAGE -->
            <?php if (isset($_GET['error'])): ?>

                <div class="error-message">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>

            <?php endif; ?>

            <!-- REGISTER FORM -->
            <form action="register_process.php" method="POST">

                <!-- FIRST NAME / LAST NAME -->
                <div class="form-row">

                    <div class="form-field">

                        <label for="firstname">
                            First Name
                        </label>

                        <input type="text" id="firstname" name="firstname" placeholder="Enter First Name" required>

                    </div>

                    <div class="form-field">

                        <label for="lastname">
                            Last Name
                        </label>

                        <input type="text" id="lastname" name="lastname" placeholder="Enter Last Name" required>

                    </div>

                </div>

                <!-- EMAIL -->
                <label for="email">
                    Email
                </label>

                <input type="email" id="email" name="email" placeholder="Enter Email" required>

                <!-- PASSWORD -->
                <label for="password">
                    Password
                </label>

                <input type="password" id="password" name="password" placeholder="Enter Password" required pattern="(?=.*\d)(?=.*[^A-Za-z0-9]).{6,}" title="Password must be at least 6 characters and include at least one number and one special character.">

                <!-- CONFIRM PASSWORD -->
                <label for="confirm_password">
                    Confirm Password
                </label>

                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password"
                    required>

                <!-- DATE OF BIRTH + CONTACT -->
                <div class="form-row">

                    <div class="form-field">

                        <label for="birth">
                            Date of Birth
                        </label>

                        <input type="date" id="birth" name="birth" required>

                    </div>

                    <div class="form-field">

                        <label for="contact">
                            Contact Number
                        </label>

                        <input type="tel" id="contact" name="contact" placeholder="Enter Contact Number"
                            inputmode="numeric" maxlength="11" pattern="[0-9]{1,11}"
                            title="Contact number must be up to 11 digits."
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);" required>

                    </div>

                </div>

                <!-- POSITION -->
                <label for="position">
                    Position
                </label>

                <select id="position" name="position" required>
                    <option value="">Select Position</option>
                    <option value="Warehouse Admin">Warehouse Admin</option>
                    <option value="Staff">Staff</option>
                </select>

                <!-- BUTTON -->
                <button type="submit">
                    CREATE ACCOUNT
                </button>

            </form>

            <!-- LOGIN LINK -->
            <p class="register">

                Already have an account?

                <a href="login.php">
                    Login here!
                </a>

            </p>

        </div>

    </div>

</body>

</html>