<?php

session_start();

include_once "auth_check.php";

// ================= DATABASE CONNECTION =================

// conn.php is in the same project folder
include_once "conn.php";


// ================= DATA =================

$item_name = trim($_POST['item_name'] ?? '');
$category = trim($_POST['category'] ?? '');
$supplier = trim($_POST['supplier'] ?? '');
$quantity = (int) ($_POST['quantity'] ?? 0);


// ================= VALIDATION =================

if (empty($item_name) || empty($category) || $quantity <= 0) {

    $_SESSION['error'] = "Please fill in all fields correctly.";

    header("Location: stock.php");
    exit;

}


// ================= CHECK IF ITEM ALREADY EXISTS =================

$stmt = $conn->prepare("
    SELECT * FROM tb_stock
    WHERE item_name = ?
");

$stmt->execute([$item_name]);

$item = $stmt->fetch(PDO::FETCH_ASSOC);


// ================= UPDATE EXISTING STOCK =================

if ($item) {

    // Restock only. The supplier is set when the item is first
    // created and is never changed afterwards.

    $new_quantity = $item['current_stock'] + $quantity;

    $update = $conn->prepare("
        UPDATE tb_stock
        SET current_stock = ?,
            category = ?,
            last_updated = NOW()
        WHERE item_name = ?
    ");

    $result = $update->execute([
        $new_quantity,
        $category,
        $item_name
    ]);

    $_SESSION['success'] = "Stock added successfully!";

} else {

    // ================= INSERT NEW STOCK =================

    if (empty($supplier)) {

        $_SESSION['error'] = "Please fill in all fields correctly.";

        header("Location: stock.php");
        exit;

    }

    $insert = $conn->prepare("
        INSERT INTO tb_stock
        (
            item_name,
            category,
            supplier,
            current_stock,
            minimum_stock,
            stock_status,
            last_updated
        )
        VALUES
        (?, ?, ?, ?, ?, ?, NOW())
    ");


    // Default minimum stock
    $minimum_stock = 100;


    // Determine stock status

    if ($quantity == 0) {

        $stock_status = "Out of Stock";

    } elseif ($quantity <= $minimum_stock) {

        $stock_status = "Low Stock";

    } else {

        $stock_status = "In Stock";

    }


    $result = $insert->execute([
        $item_name,
        $category,
        $supplier,
        $quantity,
        $minimum_stock,
        $stock_status
    ]);

    $_SESSION['success'] = "Stock added successfully!";

}


// ================= RESULT =================

if (!$result) {

    $_SESSION['error'] = "Failed to save stock.";

}


header("Location: stock.php");
exit;

?>