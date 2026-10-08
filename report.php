<?php

session_start();

include_once "auth_check.php";

include_once "conn.php";

// Total inventory items
try {

    $stmt = $conn->query("
        SELECT COUNT(*) AS total
        FROM tb_stock
    ");

    $totalItems = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];

} catch (PDOException $e) {

    $totalItems = 0;

}

// Total quantity
try {

    $stmt = $conn->query("
        SELECT COALESCE(SUM(current_stock), 0) AS total_quantity
        FROM tb_stock
    ");

    $totalQuantity = (int) $stmt->fetch(PDO::FETCH_ASSOC)['total_quantity'];

} catch (PDOException $e) {

    $totalQuantity = 0;

}

// Low stock items
try {

    $stmt = $conn->query("
        SELECT COUNT(*) AS low_stock
        FROM tb_stock
        WHERE current_stock > 0
          AND current_stock <= minimum_stock
    ");

    $lowStock = (int) $stmt->fetch(PDO::FETCH_ASSOC)['low_stock'];

} catch (PDOException $e) {

    $lowStock = 0;

}

// Out of stock
try {

    $stmt = $conn->query("
        SELECT COUNT(*) AS out_stock
        FROM tb_stock
        WHERE current_stock <= 0
    ");

    $outOfStock = (int) $stmt->fetch(PDO::FETCH_ASSOC)['out_stock'];

} catch (PDOException $e) {

    $outOfStock = 0;

}

// Delivery / Pick Up totals per month
try {

    $stmt = $conn->query("
        SELECT
            DATE_FORMAT(release_date, '%Y-%m') AS month_key,
            SUM(transport = 'Delivery') AS delivery_count,
            SUM(transport = 'Pick Up') AS pickup_count
        FROM tb_release_requests
        WHERE transport IN ('Delivery', 'Pick Up')
        GROUP BY month_key
        ORDER BY month_key DESC
    ");

    $transportByMonth = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $transportByMonth = [];

}

// Inventory list
try {

    $stmt = $conn->query("
        SELECT
            stock_id,
            item_name,
            category,
            current_stock,
            minimum_stock,
            stock_status
        FROM tb_stock
        ORDER BY stock_id DESC
    ");

    $inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $inventory = [];

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MK Warehouse - Reports</title>

    <link rel="stylesheet" href="bootstrap%20(1).css">
    <link rel="stylesheet" href="report.css?v=2">

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

                    <h1>Reports</h1>

                </div>

                <?php include "topbar_user.php"; ?>

            </header>

            <!-- REPORT CONTENT -->
            <section class="content">

                <!-- REPORT HEADER -->
                <div class="report-header">

                    <div>

                        <h2>Inventory Report</h2>

                        <p>
                            Stock levels, minimum thresholds and item availability at a glance.
                        </p>

                    </div>

                    <button
                        type="button"
                        class="print-btn"
                        onclick="window.print()"
                    >
                        Print Report
                    </button>

                </div>

                <!-- SUMMARY CARDS -->
                <div class="cards">

                    <div class="card">

                        <h4>Total Items</h4>

                        <div class="number">
                            <?= number_format($totalItems) ?>
                        </div>

                        <p>Distinct items in inventory</p>

                        <div class="card-icon blue">▤</div>

                    </div>

                    <div class="card">

                        <h4>Total Quantity</h4>

                        <div class="number">
                            <?= number_format($totalQuantity) ?>
                        </div>

                        <p>Combined units on hand</p>

                        <div class="card-icon purple">⇅</div>

                    </div>

                    <div class="card">

                        <h4>Low Stock</h4>

                        <div class="number">
                            <?= number_format($lowStock) ?>
                        </div>

                        <p>At or below minimum stock</p>

                        <div class="card-icon orange">⚠</div>

                    </div>

                    <div class="card">

                        <h4>Out of Stock</h4>

                        <div class="number">
                            <?= number_format($outOfStock) ?>
                        </div>

                        <p>No remaining stock</p>

                        <div class="card-icon red">✕</div>

                    </div>

                </div>

                <!-- INVENTORY TABLE -->
                <div class="table-container">

                        <table>

                            <thead>

                                <tr>

                                    <th>
                                        ID
                                    </th>

                                    <th>
                                        Item Name
                                    </th>

                                    <th>
                                        Category
                                    </th>

                                    <th>
                                        Quantity
                                    </th>

                                    <th>
                                        Minimum Stock
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                            <?php if (count($inventory) > 0): ?>

                                <?php foreach ($inventory as $item): ?>

                                    <?php

                                    $currentStock = (int) $item['current_stock'];
                                    $minimumStock = (int) $item['minimum_stock'];

                                    if ($currentStock <= 0) {
                                        $status = "Out of Stock";
                                        $statusClass = "out-of-stock";
                                    } elseif ($currentStock <= $minimumStock) {
                                        $status = "Low Stock";
                                        $statusClass = "low-stock";
                                    } else {
                                        $status = "In Stock";
                                        $statusClass = "in-stock";
                                    }

                                    ?>

                                    <tr>

                                        <td>
                                            <?= htmlspecialchars($item['stock_id']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($item['item_name']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($item['category']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($item['current_stock']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($item['minimum_stock']) ?>
                                        </td>

                                        <td>

                                            <span class="status-badge <?= $statusClass ?>">

                                                <?= htmlspecialchars($status) ?>

                                            </span>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="6" class="no-records">
                                        No inventory records found.
                                    </td>
                                </tr>

                            <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                <!-- TRANSPORT PER MONTH -->
                <div class="transport-report">

                    <div class="report-header">

                        <div>

                            <h2>Transport per Month</h2>

                            <p>
                                How many release requests used delivery and pick up, grouped by month.
                            </p>

                        </div>

                    </div>

                    <div class="table-container">

                        <table>

                            <thead>

                                <tr>

                                    <th>
                                        Month
                                    </th>

                                    <th class="count-col">
                                        Delivery
                                    </th>

                                    <th class="count-col">
                                        Pick Up
                                    </th>

                                    <th class="count-col">
                                        Total
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                            <?php if (count($transportByMonth) > 0): ?>

                                <?php foreach ($transportByMonth as $row): ?>

                                    <?php

                                    $deliveryCount = (int) $row['delivery_count'];
                                    $pickupCount = (int) $row['pickup_count'];

                                    $monthDate = DateTime::createFromFormat('Y-m', $row['month_key']);
                                    $monthLabel = $monthDate
                                        ? $monthDate->format('F Y')
                                        : $row['month_key'];

                                    ?>

                                    <tr>

                                        <td>
                                            <?= htmlspecialchars($monthLabel) ?>
                                        </td>

                                        <td class="count-col count-delivery">
                                            <?= number_format($deliveryCount) ?>
                                        </td>

                                        <td class="count-col count-pickup">
                                            <?= number_format($pickupCount) ?>
                                        </td>

                                        <td class="count-col">
                                            <?= number_format($deliveryCount + $pickupCount) ?>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <tr>
                                    <td colspan="4" class="no-records">
                                        No delivery or pick up records found.
                                    </td>
                                </tr>

                            <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </section>

        </main>

    </div>

    <!-- SIDEBAR TOGGLE -->
<script src="report.js?v=1"></script>

</body>

</html>
