<?php

session_start();

include_once "conn.php";

/* CHECK REQUEST */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: register.php");
    exit();

}


/* GET DATA */

$firstname = trim($_POST['firstname'] ?? '');

$lastname = trim($_POST['lastname'] ?? '');

$email = trim($_POST['email'] ?? '');

$password = $_POST['password'] ?? '';

$confirm_password = $_POST['confirm_password'] ?? '';

$birth = trim($_POST['birth'] ?? '');

$contact = trim($_POST['contact'] ?? '');

$position = trim($_POST['position'] ?? '');


/* CHECK EMPTY */

if (
    $firstname === '' ||
    $lastname === '' ||
    $email === '' ||
    $password === '' ||
    $confirm_password === '' ||
    $birth === '' ||
    $contact === '' ||
    $position === ''
) {

    header(
        "Location: register.php?error=Please complete all fields."
    );

    exit();
}


/* CHECK EMAIL */

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    header(
        "Location: register.php?error=Please enter a valid email address."
    );

    exit();
}


/* CHECK PASSWORD */

if ($password !== $confirm_password) {

    header(
        "Location: register.php?error=Passwords do not match."
    );

    exit();
}


/* CHECK NAME LENGTH */

if (strlen($firstname) < 2) {

    header(
        "Location: register.php?error=First name must be at least 2 characters."
    );

    exit();
}

if (strlen($lastname) < 2) {

    header(
        "Location: register.php?error=Last name must be at least 2 characters."
    );

    exit();
}


/* CHECK PASSWORD LENGTH */

if (strlen($password) < 6) {

    header(
        "Location: register.php?error=Password must be at least 6 characters."
    );

    exit();
}

/* CHECK PASSWORD COMPLEXITY */

if (!preg_match('/[0-9]/', $password)) {

    header(
        "Location: register.php?error=Password must include at least one number."
    );

    exit();
}

if (!preg_match('/[^A-Za-z0-9]/', $password)) {

    header(
        "Location: register.php?error=Password must include at least one special character."
    );

    exit();
}


/* CHECK DATE OF BIRTH */

if (!strtotime($birth)) {

    header(
        "Location: register.php?error=Please enter a valid date of birth."
    );

    exit();
}


/* CHECK CONTACT NUMBER */

if (!ctype_digit($contact) || strlen($contact) > 12) {

    header(
        "Location: register.php?error=Please enter a valid contact number."
    );

    exit();
}


try {

    /* CHECK EXISTING EMAIL */

    $sql = "
        SELECT id
        FROM tb_users
        WHERE email = :email
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ':email' => $email
    ]);

    if ($stmt->fetch()) {

        header(
            "Location: register.php?error=Email already exists."
        );

        exit();
    }


    /* HASH PASSWORD */

    $hashed_password = password_hash(
        $password,
        PASSWORD_DEFAULT
    );


    /* SAVE USER */

    $sql = "
        INSERT INTO tb_users
        (
            firstname,
            lastname,
            email,
            password,
            contact,
            birth,
            position
        )
        VALUES
        (
            :firstname,
            :lastname,
            :email,
            :password,
            :contact,
            :birth,
            :position
        )
    ";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ':firstname' => $firstname,
        ':lastname' => $lastname,
        ':email' => $email,
        ':password' => $hashed_password,
        ':contact' => (int) $contact,
        ':birth' => $birth,
        ':position' => $position
    ]);


    /* SUCCESS */

    header(
        "Location: login.php?success=Account created successfully. Please login."
    );

    exit();


} catch (PDOException $e) {

    header(
        "Location: register.php?error=Unable to create account."
    );

    exit();
}

?>