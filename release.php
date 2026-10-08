<?php

session_start();

include_once "auth_check.php";

include_once "conn.php";

$stocks = [];

try {

    // ================= LOAD STOCK DATA =================

    $stmt = $conn->prepare("
        SELECT
            stock_id,
            item_name,
            category,
            current_stock,
            minimum_stock,
            stock_status,
            last_updated
        FROM tb_stock
        ORDER BY stock_id DESC
    ");

    $stmt->execute();

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

    <title>MK Warehouse - Release Inventory</title>
    <link rel="stylesheet" href="bootstrap%20(1).css">
    <link rel="stylesheet" href="release.css?v=7">
    <link rel="stylesheet" href="layout.css?v=1">
</head>

<body>

    <div class="layout">

        <!-- SIDEBAR -->
        <?php include "sidebar.php"; ?>


        <!-- MAIN CONTENT -->
        <main class="main">

            <!-- TOP BAR -->
            <header class="topbar">

                <div class="page-title">
                    <button type="button" class="menu-button">☰</button>
                    <h1>Release Inventory</h1>
                </div>

                <?php include "topbar_user.php"; ?>

            </header>


            <!-- PAGE CONTENT -->
            <section class="content">
<div id="flashMessages" hidden
     data-success="<?= htmlspecialchars($_SESSION['success'] ?? '', ENT_QUOTES) ?>"
     data-error="<?= htmlspecialchars($_SESSION['error'] ?? '', ENT_QUOTES) ?>"></div>
<?php unset($_SESSION['success'], $_SESSION['error']); ?>


                <!-- SEARCH -->
                <div class="search-box">
                    <span>🔍</span>

                    <input type="text" id="searchInput" placeholder="Search item..." autocomplete="off">
                </div>


                <!-- STOCK TABLE BOX -->
                <div class="stock-box">

                    <div class="table-container">

                        <table id="stockTable">

                            <thead>
                                <tr>
                                    <th>Item ID</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>Current Stock</th>
                                    <th>Stock Status</th>
                                    <th>Last Updated</th>
                                </tr>
                            </thead>

                            <tbody id="stockTableBody">

                                <tr id="searchMessage">
                                    <td colspan="6" style="text-align:center; padding:30px;">
                                        Search for an item to display stock.
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>


                <!-- ================= RELEASE ITEM BOX ================= -->
                <div class="release-form-box">

                    <h2>Release Item</h2>

                    <p class="subtitle">
                        Enter the information for the inventory item to be released.
                    </p>


                    <form action="save_release.php" method="POST">

                        <!-- Hidden stock ID -->
                        <input type="hidden" name="stock_id" id="releaseItemId">

                        <!-- Submit mode: release | request -->
                        <input type="hidden" name="submit_mode" id="submitMode" value="release">

                        <div class="form-grid">

                            <!-- ITEM NAME -->
                            <div class="form-group">

                                <label>Item Name</label>

                                <input type="text" name="item_name" id="releaseItemName"
                                    placeholder="Select an item from the table" required readonly>

                            </div>


                            <!-- DATE OF RELEASE -->
                            <div class="form-group">

                                <label>Date of Release</label>

                                <input type="date" name="release_date" id="releaseDate" required>

                            </div>


                            <!-- QUANTITY -->
                            <div class="form-group">

                                <label>Quantity</label>

                                <input type="number" name="quantity" id="releaseQuantity"
                                    min="1" placeholder="Enter quantity" required>

                            </div>


                            <!-- CUSTOMER -->
                            <div class="form-group">

                                <label>Name of Customer</label>

                                <input type="text" name="client_name" id="clientName"
                                    placeholder="Enter customer name" required>

                            </div>


                            <!-- TRANSPORT -->
                            <div class="form-group">

                                <label>Transport</label>

                                <select name="transport" id="transport" required>
                                    <option value="">Select transport</option>
                                    <option value="Delivery">Delivery</option>
                                    <option value="Pick Up">Pick Up</option>
                                </select>

                            </div>

                        </div>


                        <div class="button-area">

                            <button type="reset" class="btn cancel-btn">
                                Cancel
                            </button>

                            <button type="submit" class="btn release-btn">
                                Release Item
                            </button>

                        </div>

                    </form>

                </div>


                <!-- ================= CONFIRM MODAL ================= -->
                <div class="modal" id="confirmModal">

                    <div class="modal-content">

                        <div class="modal-header">

                            <div>
                                <h2>Confirm Release</h2>
                                <p>Please review the release details before submitting.</p>
                            </div>

                            <button type="button" class="close-btn" id="confirmModalClose">&times;</button>

                        </div>

                        <div style="padding: 25px;">

                            <div class="form-group">
                                <label>Item Name</label>
                                <span id="confirmItemName" style="font-weight: 600; color: #17233c;">-</span>
                            </div>

                            <div class="form-group">
                                <label>Date of Release</label>
                                <span id="confirmReleaseDate" style="font-weight: 600; color: #17233c;">-</span>
                            </div>

                            <div class="form-group">
                                <label>Quantity</label>
                                <span id="confirmQuantity" style="font-weight: 600; color: #17233c;">-</span>
                            </div>

                            <div class="form-group">
                                <label>Name of Customer</label>
                                <span id="confirmClientName" style="font-weight: 600; color: #17233c;">-</span>
                            </div>

                            <div class="form-group">
                                <label>Transport</label>
                                <span id="confirmTransport" style="font-weight: 600; color: #17233c;">-</span>
                            </div>

                            <div class="modal-actions">

                                <button type="button" class="btn cancel-btn" id="confirmModalRequest">
                                    Save as Request
                                </button>

                                <button type="button" class="btn release-btn" id="confirmReleaseBtn">
                                    Confirm Release
                                </button>

                            </div>

                        </div>

                    </div>

                </div>


            </section>

        </main>

    </div>

<script src="flash.js?v=1"></script>
<script src="release.js?v=1"></script>

</body>

</html>
