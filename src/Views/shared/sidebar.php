<?php

use App\Core\Session;

$__userName = Session::get('user_name') ?? '';
$__roleName = Session::get('role_name') ?? '';
$__permissions = $__roleName
    ? (new \App\Repositories\PermissionRepository())->findNamesByRoleName($__roleName)
    : [];
$navGroups = [
    'Management' => [
        ['href' => '/admin', 'label' => 'Dashboard'],
        ['href' => '/admin/items', 'label' => 'Menu Items'],
        ['href' => '/admin/categories', 'label' => 'Categories'],
        ['href' => '/admin/users', 'label' => 'Users'],
        ['href' => '/admin/roles', 'label' => 'Roles'],
        ['href' => '/admin/logs', 'label' => 'Logs'],
        ['href' => '/admin/reports', 'label' => 'Reports', 'permission' => 'reports.view'],
    ],
    'Store' => [
        ['href' => '/store', 'label' => 'Dashboard'],
        ['href' => '/store/inventory', 'label' => 'Inventory'],
        ['href' => '/store/stocktake', 'label' => 'Stock Take'],
        ['href' => '/store/variance', 'label' => 'Variance'],
    ],
];
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <a href="/admin">RMS</a>
    </div>
    <nav class="sidebar-nav">
        <?php foreach ($navGroups as $group => $items) : ?>
            <div class="sidebar-group"><?= $group ?></div>
            <?php foreach ($items as $item) : ?>
                <?php if (isset($item['permission']) && !in_array($item['permission'], $__permissions, true)) : ?>
                    <?php continue; ?>
                <?php endif ?>
                <a href="<?= $item['href'] ?>"
                   class="sidebar-link">
                    <?= $item['label'] ?>
                </a>
            <?php endforeach ?>
        <?php endforeach ?>
    </nav>
    <div class="sidebar-footer">
        <div class="sidebar-user"><?= htmlspecialchars($__userName) ?></div>
        <form method="POST" action="/logout">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
            <button type="submit" class="sidebar-link">Logout</button>
        </form>
    </div>
</aside>