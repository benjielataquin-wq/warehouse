<?php

session_start();

include_once "auth_check.php";

include_once "conn.php";


// ================= GET FORM DATA =================

$stock_id = (int) ($_POST['stock_id'] ?? 0);
$item_name = trim($_POST['item_name'] ?? '');
$quantity = (int) ($_POST['quantity'] ?? 0);
$release_date = trim($_POST['release_date'] ?? '');
$client_name = trim($_POST['client_name'] ?? '');
$transport = trim($_POST['transport'] ?? '');


// "request" saves a PENDING request (Release/Cancel buttons
// stay visible in receive.php). Anything else saves a
// confirmed release (buttons hidden in receive.php).

$submit_mode =
    ($_POST['submit_mode'] ?? '') === 'request'
        ? 'request'
        : 'release';

$status =
    $submit_mode === 'request'
        ? 'Pending'
        : 'Released';


// ================= VALIDATION =================

if (
    $stock_id <= 0 ||
    empty($item_name) ||
    $quantity <= 0 ||
    empty($release_date) ||
    empty($client_name) ||
    empty($transport)
) {

    $_SESSION['error'] =
        "Please select an item and fill in all fields correctly.";

    header("Location: release.php");
    exit;
}


try {

    // =========================================
    // START TRANSACTION
    // =========================================

    $conn->beginTransaction();


    // =========================================
    // GET CURRENT STOCK
    // =========================================

    $stockStmt = $conn->prepare("
        SELECT
            stock_id,
            item_name,
            category,
            current_stock,
            minimum_stock
        FROM tb_stock
        WHERE stock_id = ?
        FOR UPDATE
    ");

    $stockStmt->execute([$stock_id]);

    $stock = $stockStmt->fetch(PDO::FETCH_ASSOC);


    // =========================================
    // CHECK IF ITEM EXISTS
    // =========================================

    if (!$stock) {

        $conn->rollBack();

        $_SESSION['error'] =
            "Selected item was not found in stock.";

        header("Location: release.php");
        exit;
    }


    // =========================================
    // CHECK AVAILABLE STOCK
    // =========================================

    $current_stock = (int) $stock['current_stock'];
    $minimum_stock = (int) $stock['minimum_stock'];


    if ($quantity > $current_stock) {

        $conn->rollBack();

        $_SESSION['error'] =
            "Out of stock. Only "
            . $current_stock
            . " item(s) left in stock.";

        header("Location: release.php");
        exit;
    }


    // =========================================
    // CALCULATE NEW STOCK
    // =========================================

    $new_quantity = $current_stock - $quantity;


    // =========================================
    // UPDATE STOCK STATUS
    // =========================================

    if ($new_quantity <= 0) {

        $stock_status = "Out of Stock";

    } elseif ($new_quantity <= $minimum_stock) {

        $stock_status = "Low Stock";

    } else {

        $stock_status = "In Stock";

    }


    // =========================================
    // UPDATE TB_STOCK
    // =========================================

    $updateStock = $conn->prepare("
        UPDATE tb_stock
        SET
            current_stock = ?,
            stock_status = ?,
            last_updated = NOW()
        WHERE stock_id = ?
    ");

    $updateStock->execute([
        $new_quantity,
        $stock_status,
        $stock_id
    ]);


    // =========================================
    // SAVE RELEASE REQUEST
    // =========================================

    $releaseStmt = $conn->prepare("
        INSERT INTO tb_release_requests
        (
            stock_id,
            item_name,
            category,
            quantity,
            requested_by,
            release_date,
            status,
            transport
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ");

    $releaseStmt->execute([
        $stock_id,
        $stock['item_name'],
        $stock['category'],
        $quantity,
        $client_name,
        $release_date,
        $status,
        $transport
    ]);


    // =========================================
    // COMMIT
    // =========================================

    $conn->commit();


    // =========================================
    // SUCCESS
    // =========================================

    if ($submit_mode === 'request') {

        $_SESSION['success'] =
            "Release request submitted! "
            . $quantity
            . " item(s) of "
            . $stock['item_name']
            . " is pending completion.";

    } else {

        $_SESSION['success'] =
            "Released successfully! "
            . $quantity
            . " item(s) of "
            . $stock['item_name']
            . " released via "
            . $transport
            . ".";

    }

    header("Location: release.php");
    exit;


} catch (PDOException $e) {

    // =========================================
    // ROLLBACK IF ERROR
    // =========================================

    if ($conn->inTransaction()) {

        $conn->rollBack();

    }


    $_SESSION['error'] =
        "Failed to release item.";

    header("Location: release.php");
    exit;
}

?>