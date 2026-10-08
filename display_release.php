<?php

require_once 'auth_check.php';

header('Content-Type: application/json');

include_once "conn.php";

try {

    $stmt = $conn->prepare("
        SELECT
            release_id,
            stock_id,
            item_name,
            category,
            quantity,
            requested_by,
            release_date,
            created_at,
            status,
            transport
        FROM tb_release_requests
        ORDER BY release_id DESC
    ");

    $stmt->execute();

    $releases = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($releases ?: []);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        'error' => 'Failed to load release information.'
    ]);

}

?>