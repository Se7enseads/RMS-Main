<?php

use App\Core\Session;

$userName = Session::get('user_name') ?? '';
$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
$navItems = [
        ['href' => '/', 'label' => 'Dashboard'],
        ['href' => '/items', 'label' => 'Items'],
        ['href' => '/users', 'label' => 'Users'],
        ['href' => '/roles', 'label' => 'Roles'],
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RMS - Menu Items</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/admin.css">
</head>

<body>
<header>
    <a href="/" class="logo">RMS</a>
    <nav>
        <?php foreach ($navItems as $item) : ?>
            <a href="<?= $item['href'] ?>"
               class="<?= $currentPath === $item['href'] || str_starts_with($currentPath, $item['href'] . '/') ? 'active' : '' ?>">
                <?= $item['label'] ?>
            </a>
        <?php endforeach ?>
        <?php if ($userName !== '') : ?>
            <span class="header-user"><?= htmlspecialchars($userName) ?></span>
        <?php endif ?>
        <a href="/logout">Logout</a>
    </nav>
</header>
<main><?= $slot ?></main>
<footer>&copy; <?= date('Y') ?> Restaurant Management System</footer>
</body>

</html>
