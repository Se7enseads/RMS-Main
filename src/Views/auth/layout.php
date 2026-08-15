<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'RMS' ?></title>
    <link rel="stylesheet" href="/css/auth.css">
</head>
<body>
<main><?= $slot ?? '<h1> Not ready </h1>' ?></main>

<script src="/js/auth_layout.js"></script>
</body>
</html>
