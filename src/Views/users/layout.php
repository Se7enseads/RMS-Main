<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RMS</title>
  <link rel="stylesheet" href="/assets/css/main.css">
  <link rel="stylesheet" href="/assets/css/users.css">
</head>

<body>
  <header>
    <a href="/dashboard" class="logo">RMS</a>
    <nav>
      <a href="/users">Users</a>
      <a href="/roles">Roles</a>
      <a href="/permissions">Permissions</a>
      <a href="/logout">Logout</a>
    </nav>
  </header>
  <main><?= htmlspecialchars($slot ?? '', ENT_QUOTES, 'UTF-8') ?></main>
  <footer>&copy; <?= date('Y') ?> Restaurant Management System</footer>
</body>

</html
