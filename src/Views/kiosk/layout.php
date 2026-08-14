<?php

use App\Core\Session;

$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
$navItems = [
        ['href' => '/kiosk', 'label' => 'Dashboard'],
        ['href' => '/kiosk/order', 'label' => 'Place Order'],
];
$userName = Session::get('user_name') ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RMS - Kiosk</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/kiosk.css">
</head>
<body class="kiosk-body">

<aside class="sidebar">
    <div class="sidebar-brand">
        <a href="/kiosk">RMS</a>
    </div>
    <nav class="sidebar-nav">
        <?php foreach ($navItems as $item) : ?>
            <a href="<?= $item['href'] ?>"
               class="sidebar-link <?= $currentPath === $item['href'] || str_starts_with($currentPath, $item['href'] . '/') ? 'active' : '' ?>">
                <?= htmlspecialchars($item['label']) ?>
            </a>
        <?php endforeach ?>
    </nav>
    <div class="sidebar-footer">
        <div class="sidebar-user"><?= htmlspecialchars($userName) ?></div>
        <a href="/logout" class="sidebar-link">Logout</a>
    </div>
</aside>

<div class="kiosk-main">
    <main class="kiosk-content"><?= $slot ?></main>
</div>

</body>
</html>
