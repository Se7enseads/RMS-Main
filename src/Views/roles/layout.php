<?php

use App\Core\Session;

$userName = Session::get('user_name') ?? '';
$navGroups = [
    'Management' => [
        ['href' => '/admin', 'label' => 'Dashboard'],
        ['href' => '/admin/items', 'label' => 'Menu Items'],
        ['href' => '/admin/categories', 'label' => 'Categories'],
        ['href' => '/admin/users', 'label' => 'Users'],
        ['href' => '/admin/roles', 'label' => 'Roles'],
    ],
    'Store' => [
        ['href' => '/store', 'label' => 'Dashboard'],
        ['href' => '/store/inventory', 'label' => 'Inventory'],
        ['href' => '/store/stocktake', 'label' => 'Stock Take'],
        ['href' => '/store/variance', 'label' => 'Variance'],
    ],
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RMS - Roles</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/admin/main.css">
    <link rel="stylesheet" href="/vendor/tabulator/tabulator.min.css">
    <script src="/vendor/tabulator/tabulator.min.js"></script>
    <script src="/js/main.js" defer></script>
</head>

<body class="kiosk-body">
<aside class="sidebar">
    <div class="sidebar-brand">
        <a href="/admin">RMS</a>
    </div>
    <nav class="sidebar-nav">
        <?php foreach ($navGroups as $group => $items) : ?>
            <div class="sidebar-group"><?= $group ?></div>
            <?php foreach ($items as $item) : ?>
                <a href="<?= $item['href'] ?>"
                   class="sidebar-link">
                    <?= $item['label'] ?>
                </a>
            <?php endforeach ?>
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
    <main class="kiosk-content"><?= $slot ?></main>
</div>
<footer>&copy; <?= date('Y') ?> Restaurant Management System</footer>
</body>

</html>