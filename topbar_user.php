<?php

if (!isset($conn)) {
    include_once __DIR__ . "/conn.php";
}

$initial = strtoupper(substr($_SESSION['firstname'] ?? '', 0, 1));
$initial = $initial !== '' ? $initial : 'U';

?>
<div class="topbar-right">

    <?php include __DIR__ . "/notification_badge.php"; ?>

    <div class="user-profile">

        <div class="profile-image">
            <?php echo htmlspecialchars($initial); ?>
        </div>

        <div class="user-info">
            <strong><?php echo htmlspecialchars(($_SESSION['firstname'] ?? 'Admin User')); ?></strong>
            <span><?php echo htmlspecialchars($_SESSION['position'] ?? 'Warehouse Manager'); ?></span>
        </div>

    </div>

</div>