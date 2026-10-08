<?php session_start(); ?>

<?php include_once "auth_check.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Profile</title>
    <link rel="stylesheet" href="bootstrap%20(1).css">
    <link rel="stylesheet" href="user.css?v=5">

    <link rel="stylesheet" href="layout.css?v=1">

</head>

<body>

    <!-- SIDEBAR -->
    <?php include "sidebar.php"; ?>


    <!-- MAIN CONTENT -->
    <main class="main-content">

        <!-- TOP BAR -->
        <header class="topbar">

            <div class="page-title">

                <button type="button" class="menu-button">
                    ☰
                </button>

                <h1>My Profile</h1>

            </div>

            <?php include "topbar_user.php"; ?>

        </header>


        <!-- PAGE CONTENT -->
        <section class="content">

            <div class="container">

                <!-- Profile Header -->
                <div class="profile-header">

                    <div class="profile-picture">

                        <div class="avatar-initial" id="avatarInitial">
                            <?php
                            $initial = strtoupper(
                                substr($_SESSION['firstname'] ?? '', 0, 1)
                            );
                            echo $initial !== '' ? $initial : 'U';
                            ?>
                        </div>

                        <div class="camera">
                            📷
                        </div>

                    </div>

                    <div class="profile-info">
                        <h2>
                            <?php
                            $sessionName = trim(
                                ($_SESSION['firstname'] ?? '') . ' ' .
                                ($_SESSION['lastname'] ?? '')
                            );
                            echo htmlspecialchars($sessionName !== '' ? $sessionName : 'User');
                            ?>
                        </h2>
                        <p><?php echo htmlspecialchars($_SESSION['position'] ?? 'Admin'); ?></p>
                    </div>

                </div>


                <!-- Personal Information -->
                <div class="card">

                    <div class="card-header">

                        <h2>Personal Information</h2>

                        <button class="edit-btn" onclick="openPersonalModal()">
                            Edit ✎
                        </button>

                    </div>


                    <div class="info-grid">

                        <div class="info-item">
                            <label>First Name</label>
                            <p id="firstName">—</p>
                        </div>

                        <div class="info-item">
                            <label>Last Name</label>
                            <p id="lastName">—</p>
                        </div>

                        <div class="info-item">
                            <label>Date of Birth</label>
                            <p id="birthDate">—</p>
                        </div>

                        <div class="info-item">
                            <label>Email Address</label>
                            <p id="email">—</p>
                        </div>

                        <div class="info-item">
                            <label>Phone Number</label>
                            <p id="phone">—</p>
                        </div>

                        <div class="info-item">
                            <label>User Role</label>
                            <p id="role">Admin</p>
                        </div>

                    </div>

                </div>


                <!-- Address -->
                <div class="card">

                    <div class="card-header">

                        <h2>Address</h2>

                        <button class="edit-btn address-edit" onclick="openAddressModal()">
                            Edit ✎
                        </button>

                    </div>


                    <div class="address-grid">

                        <div class="info-item">
                            <label>Country</label>
                            <p id="country">—</p>
                        </div>

                        <div class="info-item">
                            <label>City</label>
                            <p id="city">—</p>
                        </div>

                        <div class="info-item">
                            <label>Postal Code</label>
                            <p id="postal">—</p>
                        </div>

                    </div>

                </div>

            </div>

        </section>

    </main>


    <!-- Personal Information Modal -->
    <div class="modal" id="personalModal">

        <div class="modal-box">

            <h2>Edit Personal Information</h2>

            <div class="form-group">
                <label>First Name</label>
                <input type="text" id="editFirstName" placeholder="Enter First Name">
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text" id="editLastName" placeholder="Enter Last Name">
            </div>

            <div class="form-group">
                <label>Date of Birth</label>
                <input type="text" id="editBirthDate" placeholder="YYYY-MM-DD">
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" id="editEmail" placeholder="Enter Email">
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" id="editPhone" placeholder="Enter Phone Number">
            </div>

            <button class="save-btn" onclick="savePersonalInfo()">
                Save Changes
            </button>

            <button class="cancel-btn" onclick="closePersonalModal()">
                Cancel
            </button>

        </div>

    </div>


    <!-- Address Modal -->
    <div class="modal" id="addressModal">

        <div class="modal-box">

            <h2>Edit Address</h2>

            <div class="form-group">
                <label>Country</label>
                <input type="text" id="editCountry" placeholder="Enter Country">
            </div>

            <div class="form-group">
                <label>City</label>
                <input type="text" id="editCity" placeholder="Enter City">
            </div>

            <div class="form-group">
                <label>Postal Code</label>
                <input type="text" id="editPostal" placeholder="Enter Postal Code">
            </div>

            <button class="save-btn" onclick="saveAddress()">
                Save Changes
            </button>

            <button class="cancel-btn" onclick="closeAddressModal()">
                Cancel
            </button>

        </div>

    </div>


<script src="user.js?v=1"></script>

</body>

</html>
