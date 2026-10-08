<?php

session_start();

include_once "auth_check.php";

include_once "conn.php";

try {

    $stmt = $conn->query("
        SELECT COALESCE(SUM(current_stock), 0) AS total_stock
        FROM tb_stock
    ");

    $totalStock = $stmt->fetch(PDO::FETCH_ASSOC)['total_stock'];

} catch (PDOException $e) {

    $totalStock = 0;

}


try {

    $stmt = $conn->query("
        SELECT COUNT(*) AS total_released
        FROM tb_release_requests
    ");

    $totalReleased = $stmt->fetch(PDO::FETCH_ASSOC)['total_released'];

} catch (PDOException $e) {

    $totalReleased = 0;

}


try {

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS month_released
        FROM tb_release_requests
        WHERE DATE_FORMAT(release_date, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
    ");

    $stmt->execute();

    $thisMonth = $stmt->fetch(PDO::FETCH_ASSOC)['month_released'];

} catch (PDOException $e) {

    $thisMonth = 0;

}


try {

    $stmt = $conn->query("
        SELECT COUNT(*) AS pending_releases
        FROM tb_release_requests
        WHERE status NOT IN ('Released', 'Cancelled')
    ");

    $pendingReleases = $stmt->fetch(PDO::FETCH_ASSOC)['pending_releases'];

} catch (PDOException $e) {

    $pendingReleases = 0;

}


// RELEASE DATES FOR CALENDAR
$releaseDates = [];
$releasesByDate = [];

try {

    $stmt = $conn->query("
        SELECT
            release_id,
            item_name,
            category,
            quantity,
            requested_by,
            release_date,
            status
        FROM tb_release_requests
        ORDER BY release_date, release_id
    ");

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {

        $date = trim((string) $row['release_date']);

        // Only well-formed YYYY-MM-DD dates can match a calendar cell key.
        // Anything else (NULL, empty, malformed) is skipped rather than
        // creating a phantom "" entry in the calendar data.
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            continue;
        }

        if (!isset($releaseDates[$date])) {
            $releaseDates[$date] = 0;
        }

        $releaseDates[$date]++;

        $releasesByDate[$date][] = [
            'release_id' => $row['release_id'],
            'item_name' => $row['item_name'],
            'category' => $row['category'],
            'quantity' => $row['quantity'],
            'requested_by' => $row['requested_by'],
            'release_date' => $row['release_date'],
            'status' => $row['status']
        ];
    }

} catch (PDOException $e) {

    $releaseDates = [];
    $releasesByDate = [];

}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MK WAREHOUSE</title>

    <link rel="stylesheet" href="bootstrap%20(1).css">
    <link rel="stylesheet" href="home.css?v=10">
    <link rel="stylesheet" href="layout.css?v=1">
</head>

<body>

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

            <!-- SUMMARY CARDS -->
            <div class="cards">

                <div class="card">
                    <h4>TOTAL STOCKS</h4>

                    <div class="number">
                        <?php echo number_format($totalStock); ?>
                    </div>

                    <p>All stock recorded</p>

                    <div class="card-icon blue">▣</div>
                </div>

                <div class="card">

                    <h4>ITEMS RELEASED</h4>

                    <div class="number" id="totalReleased">
                        <?php echo number_format($totalReleased); ?>
                    </div>

                    <p>Total number of release requests</p>

                    <div class="card-icon green">➤</div>

                </div>

                <div class="card">
                    <h4>THIS MONTH</h4>
                    <div class="number">
                        <?php echo number_format($thisMonth); ?>
                    </div>
                    <p>Releases this month</p>
                    <div class="card-icon purple">▣</div>
                </div>

                <div class="card">
                    <h4>PENDING RELEASES</h4>
                    <div class="number">
                        <?php echo number_format($pendingReleases); ?>
                    </div>
                    <p>Awaiting completion</p>
                    <div class="card-icon orange">▤</div>
                </div>

            </div>

            <!-- CALENDAR -->
            <div class="calendar-card">

                <div class="calendar-header">

                    <h2>Release Calendar</h2>

                    <div class="calendar-nav">

                        <button type="button" class="today-btn" id="todayBtn">
                            Today
                        </button>

                        <button type="button" class="calendar-arrow" id="prevMonth">‹</button>

                        <span id="calendarMonth"></span>

                        <button type="button" class="calendar-arrow" id="nextMonth">›</button>

                    </div>

                </div>

                <div class="calendar-weekdays">
                    <span>Sun</span>
                    <span>Mon</span>
                    <span>Tue</span>
                    <span>Wed</span>
                    <span>Thu</span>
                    <span>Fri</span>
                    <span>Sat</span>
                </div>

                <div class="calendar-grid" id="calendarGrid"></div>

                <div class="calendar-legend">

                    <span class="legend-item">
                        <span class="legend-dot release-legend"></span>
                        Has release(s)
                    </span>

                    <span class="legend-item">
                        <span class="legend-dot today-legend"></span>
                        Today
                    </span>

                </div>

            </div>

        </section>

    </main>

    <!-- RELEASE DETAILS MODAL -->
    <div class="release-modal" id="releaseModal">

        <div class="release-modal-box">

            <div class="release-modal-header">

                <h3>Release Details</h3>

                <button type="button" class="release-modal-close" id="closeReleaseModal">×</button>

            </div>

            <div class="release-modal-date" id="releaseModalDate"></div>

            <div class="release-info-list" id="releaseInfoList"></div>

        </div>

    </div>

    <script type="application/json" id="calendarData"><?= json_encode([
        'releaseDates' => $releaseDates,
        'releasesByDate' => $releasesByDate
    ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
    <script src="home.js?v=1"></script>
</body>

</html>
