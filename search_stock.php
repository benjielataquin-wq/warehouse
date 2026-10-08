<?php

require_once 'auth_check.php';

require_once 'conn.php';

header('Content-Type: application/json');

try {

    $search = isset($_GET['search']) ? trim($_GET['search']) : '';

    // Do not return anything when search is empty
    if ($search === '') {
        echo json_encode([]);
        exit;
    }

    $stmt = $conn->prepare("
        SELECT
            stock_id,
            item_name,
            category,
            current_stock,
            stock_status,
            last_updated
        FROM tb_stock
        WHERE
            CAST(stock_id AS CHAR) LIKE :search
            OR item_name LIKE :search
            OR category LIKE :search
        ORDER BY last_updated DESC
    ");

    $searchValue = "%" . $search . "%";

    $stmt->bindParam(':search', $searchValue, PDO::PARAM_STR);

    $stmt->execute();

    $stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($stocks);

} catch (PDOException $e) {

    echo json_encode([
        'error' => $e->getMessage()
    ]);

}
?>