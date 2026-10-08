<?php

session_start();

include_once "auth_check.php";

include_once "conn.php";

header("Content-Type: application/json");

try {

    $user_id = $_SESSION['user_id'] ?? null;

    if ($user_id) {

        $sql = "
            SELECT id, firstname, lastname, email, contact, birth, country, city, postal, position, created_at
            FROM tb_users
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $conn->prepare($sql);

        $stmt->execute([
            ':id' => $user_id
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

    } else {

        $stmt = $conn->query("
            SELECT id, firstname, lastname, email, contact, birth, country, city, postal, position, created_at
            FROM tb_users
            ORDER BY id ASC
            LIMIT 1
        ");

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

    }

    if (!$user) {

        echo json_encode([
            'error' => 'No user found.'
        ]);

        exit;
    }

    echo json_encode($user);

} catch (PDOException $e) {

    echo json_encode([
        'error' => 'Failed to load user data.'
    ]);

}

?>