<?php

require __DIR__ . '/../shared/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RMS - Store</title>
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/admin/main.css">
    <link rel="stylesheet" href="/vendor/tabulator/tabulator.min.css">
    <script src="/vendor/tabulator/tabulator.min.js"></script>
    <script src="/js/main.js" defer></script>
</head>

<body class="kiosk-body">
<div class="kiosk-main">
    <main class="kiosk-content"><?= $slot ?></main>
</div>
<footer>&copy; <?= date('Y') ?> Restaurant Management System</footer>
</body>

</html>