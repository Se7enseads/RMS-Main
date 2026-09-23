<?php

require __DIR__ . '/../shared/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RMS - Users</title>
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
<script>
    const passwordInput = document.getElementById('password');

    // Common passwords blocklist
    const commonPasswords = ['password', '1234567890', 'qwertyuiop', 'letmein123'];

    function validatePassword() {
        const password = passwordInput.value;

        // Reset previous error state
        passwordInput.setCustomValidity('');

        if (!password) return;

        // 1. Check against common password list
        if (commonPasswords.some(common => password.toLowerCase().includes(common))) {
            passwordInput.setCustomValidity('Password contains a common weak phrase.');
            return;
        }

        // 3. Check for repeated characters (e.g., "aaaaa")
        if (/(.)\1{4,}/.test(password)) {
            passwordInput.setCustomValidity('Password contains too many repeating characters.');

        }
    }

    // Run validation on input change
    passwordInput.addEventListener('input', validatePassword);
    emailInput.addEventListener('input', validatePassword);
</script>
</body>

</html>