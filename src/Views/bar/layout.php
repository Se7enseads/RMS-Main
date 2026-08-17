<?php

use App\Core\Session;

$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
$navItems = [
        ['href' => '/bar', 'label' => 'Bar Display'],
        ['href' => '/bar/order', 'label' => 'Place Order'],
];
$userName = Session::get('user_name') ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RMS - Bar Display</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/kiosk/main.css">
    <link rel="stylesheet" href="/css/bar.css">
</head>
<body class="kiosk-body">

<aside class="sidebar">
    <div class="sidebar-brand">
        <a href="/bar">RMS</a>
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
        <form method="POST" action="/logout">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
            <button type="submit" class="sidebar-link">Logout</button>
        </form>
    </div>
</aside>

<div class="kiosk-main">
    <main class="kiosk-content"><?= $slot ?? '<h1>No Content</h1>' ?></main>
</div>

</body>
</html>