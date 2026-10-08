<?php

session_start();

include_once "auth_check.php";

include_once "conn.php";

// UPDATE STOCK STATUS FUNCTION
function updateStockStatus($conn, $stock_id)
{
    $stmt = $conn->prepare("
        SELECT current_stock, minimum_stock
        FROM tb_stock
        WHERE stock_id = ?
        LIMIT 1
    ");

    $stmt->execute([$stock_id]);

    $stock = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$stock) {
        return;
    }

    $current_stock = (int) $stock['current_stock'];
    $minimum_stock = (int) $stock['minimum_stock'];

    if ($current_stock <= 0) {
        $status = "Out of Stock";
    } elseif ($current_stock <= $minimum_stock) {
        $status = "Low Stock";
    } else {
        $status = "In Stock";
    }

    $update = $conn->prepare("
        UPDATE tb_stock
        SET stock_status = ?
        WHERE stock_id = ?
    ");

    $update->execute([
        $status,
        $stock_id
    ]);
}

// UPDATE ALL STOCK STATUSES
try {

    $statusStmt = $conn->query("
        SELECT stock_id
        FROM tb_stock
    ");

    $allStocks = $statusStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($allStocks as $item) {
        updateStockStatus($conn, $item['stock_id']);
    }

} catch (PDOException $e) {

    $_SESSION['error'] = "Failed to update stock status.";
}

// GET STOCK DATA
try {

    $sql = "
        SELECT
            stock_id,
            item_name,
            category,
            supplier,
            current_stock,
            minimum_stock,
            stock_status,
            last_updated
        FROM tb_stock
        ORDER BY stock_id DESC
    ";

    $stmt = $conn->query($sql);

    $stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $stocks = [];

    $_SESSION['error'] = "Failed to load stock data.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MK Warehouse - Stock</title>

    <link rel="stylesheet" href="bootstrap%20(1).css">
    <link rel="stylesheet" href="stock.css?v=8">

    <link rel="stylesheet" href="layout.css?v=1">

</head>

<body>

    <div class="layout">

        <!-- SIDEBAR -->
        <?php include "sidebar.php"; ?>

        <!-- MAIN CONTENT -->
        <main class="main-content">

            <!-- HEADER -->
            <header class="topbar">

                <div class="page-title">

                    <button type="button" class="menu-button">
                        ☰
                    </button>

                    <h1>Stock</h1>

                </div>

                <?php include "topbar_user.php"; ?>

            </header>

            <!-- STOCK CONTENT -->
            <section class="content">

                <div class="stock-header">

                    <div>

                        <h2>
                            Stock Inventory
                        </h2>

                        <p>
                            Monitor and manage the current inventory stock levels.
                        </p>

                    </div>

                    <button
                        type="button"
                        class="add-stock-btn"
                        onclick="openAddStockModal()"
                    >
                        + Add Item
                    </button>

                </div>

                <!-- SEARCH AND FILTER -->
                <div class="stock-controls">

                    <div class="search-box">

                        <input
                            type="text"
                            id="searchInput"
                            placeholder="Search item..."
                            onkeyup="filterStock()"
                        >

                    </div>

                    <select
                        id="categoryFilter"
                        onchange="filterStock()"
                    >

                        <option value="">
                            All Categories
                        </option>

                        <option value="Electronics">
                            Electronics
                        </option>

                        <option value="Office Supplies">
                            Office Supplies
                        </option>

                        <option value="Furniture">
                            Furniture
                        </option>

                    </select>

                    <select
                        id="statusFilter"
                        onchange="filterStock()"
                    >

                        <option value="">
                            All Stock
                        </option>

                        <option value="In Stock">
                            In Stock
                        </option>

                        <option value="Low Stock">
                            Low Stock
                        </option>

                        <option value="Out of Stock">
                            Out of Stock
                        </option>

                    </select>

                </div>

                <!-- STOCK TABLE -->
                <div class="table-container">

                    <table id="stockTable">

                        <thead>

                            <tr>

                                <th>
                                    Item ID
                                </th>

                                <th>
                                    Item Name
                                </th>

                                <th>
                                    Category
                                </th>

                                <th>
                                    Supplier
                                </th>

                                <th>
                                    Current Stock
                                </th>

                                <th>
                                    Minimum Stock
                                </th>

                                <th>
                                    Stock Status
                                </th>

                                <th>
                                    Last Updated
                                </th>

                                <th>
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php if (!empty($stocks)): ?>

                                <?php foreach ($stocks as $stock): ?>

                                    <tr>

                                        <td>
                                            <?= htmlspecialchars($stock['stock_id']); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($stock['item_name']); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($stock['category']); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($stock['supplier']); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($stock['current_stock']); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($stock['minimum_stock']); ?>
                                        </td>

                                        <td>

                                            <?php
                                            $status = $stock['stock_status'];

                                            $statusClass = strtolower(
                                                str_replace(
                                                    ' ',
                                                    '-',
                                                    $status
                                                )
                                            );
                                            ?>

                                            <span class="status-badge <?= htmlspecialchars($statusClass); ?>">

                                                <?= htmlspecialchars($status); ?>

                                            </span>

                                        </td>

                                        <td>

                                            <?php

                                            if (!empty($stock['last_updated'])) {

                                                echo date(
                                                    "M d, Y h:i A",
                                                    strtotime($stock['last_updated'])
                                                );

                                            } else {

                                                echo "N/A";

                                            }

                                            ?>

                                        </td>

                                        <td>

                                            <button
                                                    type="button"
                                                    class="action-btn"
                                                    onclick="openEditStockModal(
                                                        '<?= htmlspecialchars($stock['item_name'], ENT_QUOTES); ?>',
                                                        '<?= htmlspecialchars($stock['category'], ENT_QUOTES); ?>',
                                                        '<?= htmlspecialchars($stock['supplier'], ENT_QUOTES); ?>'
                                                    )"
                                                >
                                                    <span class="btn-icon">+</span>
                                                    Add
                                                </button>

                                            <form
                                                action="delete_stock.php"
                                                method="POST"
                                                style="display: inline;"
                                                onsubmit="return confirm('Are you sure you want to delete this stock item?');"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="stock_id"
                                                    value="<?= (int) $stock['stock_id']; ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="action-btn delete-btn"
                                                >
                                                    <span class="btn-icon">✕</span>
                                                    Delete
                                                </button>

                                            </form>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>

                                    <td
                                        colspan="9"
                                        style="text-align:center; padding:30px;"
                                    >
                                        No stock records found.
                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </section>

        </main>

    </div>

    <!-- Update STOCK MODAL -->
    <div
        class="modal"
        id="addStockModal"
    >

        <div class="modal-content">

            <div class="modal-header">

                <div>

                    <h2 id="modalTitle">
                        Update Stock
                    </h2>

                    <p id="modalDescription">
                        Enter the item information to add stock.
                    </p>

                </div>

                <button
                    type="button"
                    class="close-btn"
                    onclick="closeAddStockModal()"
                >
                    &times;
                </button>

            </div>

            <form
                action="add_stock.php"
                method="POST"
                id="stockForm"
            >

                <div class="form-group">

                    <label for="item_name">
                        Item Name
                    </label>

                    <input
                        type="text"
                        id="item_name"
                        name="item_name"
                        placeholder="Enter item name"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="category">
                        Category
                    </label>

                    <select
                        id="category"
                        name="category"
                        required
                    >

                        <option value="">
                            Select Category
                        </option>

                        <option value="Electronics">
                            Electronics
                        </option>

                        <option value="Office Supplies">
                            Office Supplies
                        </option>

                        <option value="Furniture">
                            Furniture
                        </option>

                    </select>

                </div>

                <div class="form-group">

                    <label for="supplier">
                        Supplier
                    </label>

                    <input
                        type="text"
                        id="supplier"
                        name="supplier"
                        placeholder="Enter supplier name"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="quantity">
                        Quantity
                    </label>

                    <input
                        type="number"
                        id="quantity"
                        name="quantity"
                        placeholder="Enter quantity"
                        min="1"
                        required
                    >

                </div>

                <div class="modal-actions">

                    <button
                        type="button"
                        class="cancel-modal-btn"
                        onclick="closeAddStockModal()"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="save-stock-btn"
                        id="modalSubmitButton"
                    >
                        + Add Stock
                    </button>

                </div>

            </form>

        </div>

    </div>

    <!-- JAVASCRIPT -->
<div id="flashMessages" hidden
     data-success="<?= htmlspecialchars($_SESSION['success'] ?? '', ENT_QUOTES) ?>"
     data-error="<?= htmlspecialchars($_SESSION['error'] ?? '', ENT_QUOTES) ?>"></div>
<?php unset($_SESSION['success'], $_SESSION['error']); ?>

<script src="flash.js?v=1"></script>
<script src="stock.js?v=1"></script>

</body>

</html>
