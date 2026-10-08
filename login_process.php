<?php

session_start();

include_once "conn.php";

/* Make sure request is POST */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

/* Get form data */
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

/* Check empty fields */
if ($email === '' || $password === '') {
    header("Location: login.php?error=Please enter your email and password.");
    exit();
}

try {

    /*
     * Find user by email
     */
    $sql = "SELECT * FROM tb_users WHERE email = :email LIMIT 1";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ':email' => $email
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    /* User does not exist */
    if (!$user) {
        header("Location: login.php?error=Invalid email or password.");
        exit();
    }

    /*
     * Validate password
     *
     * This assumes the password was saved using password_hash().
     */
    if (!password_verify($password, $user['password'])) {

        header("Location: login.php?error=Invalid email or password.");
        exit();
    }

    /*
     * LOGIN SUCCESS
     */

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['firstname'] = $user['firstname'];
    $_SESSION['lastname'] = $user['lastname'] ?? '';
    $_SESSION['position'] = $user['position'] ?? '';

    /*
     * Go to home page
     */
    header("Location: home.php");
    exit();

} catch (PDOException $e) {

    header("Location: login.php?error=Unable to process login.");
    exit();
}
?>