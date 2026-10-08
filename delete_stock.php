<?php

session_start();

include_once "auth_check.php";

include_once "conn.php";


// ==========================================
// CHECK REQUEST METHOD
// ==========================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    $_SESSION['error'] = "Invalid delete request.";

    header("Location: stock.php");
    exit;
}


// ==========================================
// GET STOCK ID
// ==========================================

$stock_id = filter_input(
    INPUT_POST,
    'stock_id',
    FILTER_VALIDATE_INT
);


// ==========================================
// VALIDATE STOCK ID
// ==========================================

if (!$stock_id || $stock_id <= 0) {

    $_SESSION['error'] = "Invalid stock ID.";

    header("Location: stock.php");
    exit;
}


try {

    // ==========================================
    // FIND STOCK ITEM
    // ==========================================

    $stmt = $conn->prepare("
        SELECT stock_id, item_name
        FROM tb_stock
        WHERE stock_id = :stock_id
        LIMIT 1
    ");

    $stmt->execute([
        ':stock_id' => $stock_id
    ]);

    $stock = $stmt->fetch(PDO::FETCH_ASSOC);


    // ==========================================
    // CHECK IF STOCK EXISTS
    // ==========================================

    if (!$stock) {

        $_SESSION['error'] = "Stock item not found.";

        header("Location: stock.php");
        exit;
    }


    // ==========================================
    // START TRANSACTION
    // ==========================================

    $conn->beginTransaction();


    // ==========================================
    // FIND TABLES THAT REFERENCE tb_stock
    // ==========================================

    $foreignKeySql = "
        SELECT DISTINCT
            TABLE_NAME,
            COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE
            REFERENCED_TABLE_SCHEMA = DATABASE()
            AND REFERENCED_TABLE_NAME = 'tb_stock'
            AND REFERENCED_COLUMN_NAME = 'stock_id'
    ";

    $foreignKeyStmt = $conn->query($foreignKeySql);

    $foreignKeys = $foreignKeyStmt->fetchAll(PDO::FETCH_ASSOC);


    // ==========================================
    // DELETE RELATED RECORDS FIRST
    // ==========================================

    foreach ($foreignKeys as $foreignKey) {

        $tableName = $foreignKey['TABLE_NAME'];
        $columnName = $foreignKey['COLUMN_NAME'];

        // Only allow normal database identifiers
        if (
            !preg_match('/^[a-zA-Z0-9_]+$/', $tableName) ||
            !preg_match('/^[a-zA-Z0-9_]+$/', $columnName)
        ) {
            continue;
        }


        // Don't delete from tb_stock itself
        if ($tableName === 'tb_stock') {
            continue;
        }


        $deleteRelatedSql = "
            DELETE FROM `$tableName`
            WHERE `$columnName` = :stock_id
        ";

        $deleteRelatedStmt = $conn->prepare(
            $deleteRelatedSql
        );

        $deleteRelatedStmt->execute([
            ':stock_id' => $stock_id
        ]);
    }


    // ==========================================
    // DELETE STOCK RECORD
    // ==========================================

    $deleteStockSql = "
        DELETE FROM tb_stock
        WHERE stock_id = :stock_id
    ";

    $deleteStockStmt = $conn->prepare(
        $deleteStockSql
    );

    $deleteStockStmt->execute([
        ':stock_id' => $stock_id
    ]);


    // ==========================================
    // CHECK IF STOCK WAS DELETED
    // ==========================================

    if ($deleteStockStmt->rowCount() !== 1) {

        throw new Exception(
            "Stock record could not be deleted."
        );
    }


    // ==========================================
    // COMMIT
    // ==========================================

    $conn->commit();


    // ==========================================
    // SUCCESS MESSAGE
    // ==========================================

    $_SESSION['success'] =
        "Stock item '" .
        $stock['item_name'] .
        "' was deleted successfully.";


} catch (Exception $e) {


    // ==========================================
    // ROLLBACK
    // ==========================================

    if ($conn->inTransaction()) {
        $conn->rollBack();
    }


    // ==========================================
    // ERROR MESSAGE
    // ==========================================

    $_SESSION['error'] =
        "Delete failed: " . $e->getMessage();
}


// ==========================================
// RETURN TO STOCK PAGE
// ==========================================

header("Location: stock.php");
exit;

?>