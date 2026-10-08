<?php

session_start();

include_once "auth_check.php";

include_once "conn.php";

header("Content-Type: application/json");

$response = [
    "success" => false,
    "message" => ""
];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    $response["message"] = "Invalid request method.";

    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {

    $response["message"] = "Not authenticated.";

    echo json_encode($response);
    exit;
}

$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');
$email = trim($_POST['email'] ?? '');
$contact = trim($_POST['contact'] ?? '');
$birth = trim($_POST['birth'] ?? '');
$country = trim($_POST['country'] ?? '');
$city = trim($_POST['city'] ?? '');
$postal = trim($_POST['postal'] ?? '');

if ($firstname === '' || $lastname === '') {

    $response["message"] = "First name and last name are required.";

    echo json_encode($response);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $response["message"] = "Please enter a valid email address.";

    echo json_encode($response);
    exit;
}

if ($firstname !== '' && strlen($firstname) < 2) {

    $response["message"] = "First name must be at least 2 characters.";

    echo json_encode($response);
    exit;
}

if ($lastname !== '' && strlen($lastname) < 2) {

    $response["message"] = "Last name must be at least 2 characters.";

    echo json_encode($response);
    exit;
}

if ($birth !== '' && !strtotime($birth)) {

    $response["message"] = "Please enter a valid date of birth.";

    echo json_encode($response);
    exit;
}

if ($contact !== '' && (!ctype_digit($contact) || strlen($contact) > 12)) {

    $response["message"] = "Please enter a valid contact number.";

    echo json_encode($response);
    exit;
}

try {

    $emailStmt = $conn->prepare("
        SELECT id
        FROM tb_users
        WHERE email = ?
          AND id <> ?
        LIMIT 1
    ");

    $emailStmt->execute([$email, $user_id]);

    if ($emailStmt->fetch()) {

        $response["message"] = "Email is already in use.";

        echo json_encode($response);
        exit;
    }

    $stmt = $conn->prepare("
        UPDATE tb_users
        SET
            firstname = ?,
            lastname = ?,
            email = ?,
            contact = ?,
            birth = ?,
            country = ?,
            city = ?,
            postal = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $firstname,
        $lastname,
        $email,
        $contact !== '' ? (int) $contact : null,
        $birth !== '' ? $birth : null,
        $country !== '' ? $country : null,
        $city !== '' ? $city : null,
        $postal !== '' ? $postal : null,
        $user_id
    ]);

    $_SESSION['firstname'] = $firstname;
    $_SESSION['lastname'] = $lastname;
    $_SESSION['email'] = $email;

    $response["success"] = true;
    $response["message"] = "Profile updated successfully.";

    echo json_encode($response);

} catch (PDOException $e) {

    $response["message"] = "Failed to update profile.";

    echo json_encode($response);
}

?>