<?php

if (!isset($conn)) {
    include_once __DIR__ . "/conn.php";
}

$pendingReleaseCount = 0;

try {
    $stmt = $conn->query("
        SELECT COUNT(*) AS pending_releases
        FROM tb_release_requests
        WHERE status NOT IN ('Released', 'Cancelled')
    ");
    $pendingReleaseCount = (int) $stmt->fetch(PDO::FETCH_ASSOC)['pending_releases'];
} catch (PDOException $e) {
    $pendingReleaseCount = 0;
}

?>

<a href="receive.php" class="notification">
    ♧
    <span class="notification-count"><?php echo number_format($pendingReleaseCount); ?></span>
</a>