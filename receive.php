<?php

session_start();

include_once "auth_check.php";

include_once "conn.php";

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MK Warehouse - Release Requests</title>

    <link rel="stylesheet" href="bootstrap%20(1).css">
    <link rel="stylesheet" href="receive.css?v=9">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="layout.css?v=1">

</head>

<body>

    <!-- =========================
         SIDEBAR
    ========================= -->

    <?php include "sidebar.php"; ?>


    <!-- =========================
         MAIN
    ========================= -->

    <div class="main">

        <!-- ================= TOPBAR ================= -->

        <div class="topbar">

            <div class="page-title">

                <button type="button" class="menu-button">
                    &#9776;
                </button>

                <h1>Release Information</h1>

            </div>

            <?php include "topbar_user.php"; ?>

        </div>


        <!-- ================= CONTENT ================= -->

        <div class="content">

            <!-- PAGE HEADER -->

            <div class="request-header">

                <div>

                    <h2>Release Requests</h2>

                    <p>
                        Manage released inventory information.
                    </p>

                </div>

            </div>


            <!-- ================= SEARCH ================= -->

            <div class="request-controls">

                <div class="search-box">

                    <i class="fa-solid fa-magnifying-glass"></i>

                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Search request..."
                        autocomplete="off">

                </div>

            </div>


            <!-- ================= REQUEST TABLE ================= -->

            <div class="table-container">

                <table id="releaseTable">

                    <thead>

                        <tr>

                            <th>Release ID</th>

                            <th>Item Name</th>

                            <th>Category</th>

                            <th>Quantity</th>

                            <th>Requested By</th>

                            <th>Transport</th>

                            <th>Date Release</th>

                            <th>Status</th>

                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody id="releaseTableBody">

                        <tr>

                            <td colspan="9"
                                style="text-align:center; padding:30px;">

                                Loading release information...

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- =========================
         JAVASCRIPT
    ========================= -->

<script src="receive.js?v=1"></script>

</body>

</html>
