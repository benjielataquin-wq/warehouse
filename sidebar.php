<?php

$currentPage = basename($_SERVER['PHP_SELF'] ?? '');

$menuItems = [
    'home.php'    => ['icon' => '⌂', 'label' => 'Dashboard'],
    'release.php' => ['icon' => '➤', 'label' => 'Release Inventory'],
    'receive.php' => ['icon' => '▦', 'label' => 'Release Requests'],
    'stock.php'   => ['icon' => '⇅', 'label' => 'Stock'],
    'report.php'  => ['icon' => '▤', 'label' => 'Reports'],
    'user.php'    => ['icon' => '♙', 'label' => 'Users'],
];

?>
<aside class="sidebar">

    <div class="logo">
        <div class="logo-icon">▣</div>

        <div class="logo-text">
            MK<br>
            WAREHOUSE
        </div>
    </div>

    <nav class="menu">

        <?php foreach ($menuItems as $page => $item): ?>

            <a href="<?php echo $page; ?>"
               class="<?php echo $currentPage === $page ? 'active' : ''; ?>">

                <span class="icon"><?php echo $item['icon']; ?></span>
                <span><?php echo $item['label']; ?></span>

            </a>

        <?php endforeach; ?>

    </nav>

    <div class="logout">
        <a href="logout.php">
            <span class="icon">↪</span>
            <span>Logout</span>
        </a>
    </div>

</aside>