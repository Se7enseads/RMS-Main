<?php

declare(strict_types=1);

use App\Core\Database;
use App\Services\EmployeeNumberGenerator;

require __DIR__ . '/../src/Core/Database.php';
require __DIR__ . '/../src/Services/EmployeeNumberGenerator.php';

/**
 * Demo staff & tables for the live `rms` database.
 *
 *   * tables with different capacities and statuses
 *   * additional waiters, a cashier and a store-only STORE MANAGER
 *   * paid + unpaid orders on the current open business day (cashier board)
 *
 * Usage: php scripts/seed_staff_tables.php
 */

$pdo = Database::getConnection();

// ── Tables: different capacities and statuses ───────────────────────────────
$floorPlan = [
    // number => [capacity, status]
    2 => [4, 'AVAILABLE'],
    3 => [2, 'AVAILABLE'],
    4 => [2, 'AVAILABLE'],
    5 => [6, 'AVAILABLE'],
    6 => [6, 'AVAILABLE'],
    7 => [8, 'AVAILABLE'],
    8 => [8, 'AVAILABLE'],
    9 => [4, 'RESERVED'],
    10 => [6, 'OCCUPIED'],
    11 => [2, 'OUT_OF_SERVICE'],
    12 => [6, 'OCCUPIED'],
];

$existingTables = array_map('intval', $pdo->query('SELECT number FROM tables')->fetchAll(PDO::FETCH_COLUMN));
$insertTable = $pdo->prepare('INSERT INTO tables (number, capacity, status) VALUES (?, ?, ?)');
$tableAdded = 0;
foreach ($floorPlan as $number => [$capacity, $status]) {
    if (!in_array($number, $existingTables, true)) {
        $insertTable->execute([$number, $capacity, $status]);
        $tableAdded++;
    }
}
echo "Tables added: $tableAdded\n";

// ── STORE MANAGER role: store permissions only ──────────────────────────────
$storePermissions = [
    'store.view',
    'inventory.view',
    'inventory.create',
    'inventory.update',
    'inventory.deactivate',
    'inventory.stocktake',
    'inventory.variance',
];

$storeRoleId = $pdo->query("SELECT id FROM roles WHERE name = 'STORE MANAGER'")->fetchColumn();
if (!$storeRoleId) {
    $ins = $pdo->prepare('INSERT INTO roles (name) VALUES (?)');
    $ins->execute(['STORE MANAGER']);
    $storeRoleId = (int) $pdo->lastInsertId();
    echo "Role created: STORE MANAGER\n";
}
$storeRoleId = (int) $storeRoleId;

$grantStmt = $pdo->prepare('
    INSERT IGNORE INTO role_permissions (role_id, permission_id)
    SELECT ?, id FROM permissions WHERE name = ?
');
foreach ($storePermissions as $permName) {
    $grantStmt->execute([$storeRoleId, $permName]);
}
echo 'STORE MANAGER permissions ensured: ' . count($storePermissions) . "\n";

// ── Staff: waiters, cashier, store manager ──────────────────────────────────
$generator = new EmployeeNumberGenerator();

$roleId = static fn (string $name): int => (int) $pdo->query(
    "SELECT id FROM roles WHERE name = " . $pdo->quote($name)
)->fetchColumn();

$hasPin = static function (string $pin) use ($pdo): bool {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM staff WHERE pin = ?');
    $stmt->execute([$pin]);
    return (int) $stmt->fetchColumn() > 0;
};

$staffSeed = [
    // business card => [first_name, last_name, national_id, pin, role_name, password]
    ['Brian', 'Kiprop', '70990001', '4444', 'WAITER', 'waiter123'],
    ['Grace', 'Wairimu', '70990002', '5555', 'WAITER', 'waiter123'],
    ['James', 'Mwangi', '70990003', '6666', 'WAITER', 'waiter123'],
    ['Diana', 'Achieng', '70990004', '3456', 'CASHIER', null],
    ['Peter', 'Kamau', '70990005', '7777', 'STORE MANAGER', 'store123'],
];

$insertStaff = $pdo->prepare('
    INSERT INTO staff (employee_num, first_name, last_name, national_id, pin, password_hash, role_id, active)
    VALUES (?, ?, ?, ?, ?, ?, ?, 1)
');
$staffAdded = 0;
foreach ($staffSeed as [$first, $last, $nationalId, $pin, $roleName, $password]) {
    if ($hasPin($pin)) {
        echo "Skip $first (pin $pin already used)\n";
        continue;
    }
    $employeeNum = $generator->nextForRole($roleName);
    $passwordHash = $password !== null ? password_hash($password, PASSWORD_BCRYPT) : null;
    $insertStaff->execute([$employeeNum, $first, $last, $nationalId, $pin, $passwordHash, $roleId($roleName)]);
    echo "Staff: $employeeNum ($first $last, $roleName)\n";
    $staffAdded++;
}
echo "Staff added: $staffAdded\n";

// ── Paid + unpaid orders for the cashier board ──────────────────────────────
$day = $pdo->query('SELECT date FROM business_days WHERE is_closed = 0 ORDER BY date LIMIT 1')->fetchColumn();
if (!$day) {
    $day = date('Y-m-d');
}

$existingPaid = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'PAYED' AND DATE(created_at) = " . $pdo->quote($day))->fetchColumn();

if ($existingPaid > 0) {
    echo "Business day $day already has $existingPaid paid orders; skipping order seed.\n";
} else {
    $waiterId = (int) $pdo->query(
        'SELECT id FROM staff WHERE role_id = ' . $roleId('WAITER') . ' ORDER BY id LIMIT 1'
    )->fetchColumn();
    $cashierStaffId = (int) $pdo->query("SELECT id FROM staff WHERE pin = '3456'")->fetchColumn();
    $tableNumbers = array_map('intval', $pdo->query('SELECT number FROM tables ORDER BY number')->fetchAll(PDO::FETCH_COLUMN));

    $menu = [];
    foreach ($pdo->query('SELECT id, name, price FROM menu_items WHERE active = 1') as $row) {
        $menu[] = ['id' => (int) $row['id'], 'name' => $row['name'], 'price' => (float) $row['price']];
    }

    $insertOrder = $pdo->prepare('
        INSERT INTO orders (order_number, status, type, user_id, table_id, total_amount, closed_at, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $insertItem = $pdo->prepare('INSERT INTO order_items (order_id, menu_item_id, price_at_time, quantity, served) VALUES (?, ?, ?, ?, 0)');
    $insertPayment = $pdo->prepare('INSERT INTO payments (order_id, method, amount, cashier_id, created_at) VALUES (?, ?, ?, ?, ?)');

    mt_srand(20260913);

    foreach ([1, 1, 1, 2, 2] as $idx => $servedSuffix) {
        $orderNumber = 'ORD-' . str_replace('-', '', $day) . '-' . strtoupper(str_pad(dechex(mt_rand(0, 65535)), 4, '0', STR_PAD_LEFT));
        $typeIdx = $idx % 3;
        $type = $typeIdx === 1 ? 'TAKEAWAY' : 'DINE_IN';
        $tableId = $type === 'DINE_IN' ? array_rand(array_flip($tableNumbers)) : null;
        $hour = $idx < 2 ? 9 : ($idx < 4 ? 10 : 11);
        $time = sprintf('%02d:%02d:%02d', $hour, 30 + $idx, 0);
        $createdAt = "$day $time";
        $total = 0.0;

        $insertOrder->execute([$orderNumber, 'PAYED', $type, $waiterId, $tableId, 0, $createdAt, $createdAt]);
        $orderId = (int) $pdo->lastInsertId();

        $itemCount = mt_rand(1, 2);
        for ($i = 0; $i < $itemCount; $i++) {
            $item = $menu[array_rand($menu)];
            $qty = mt_rand(1, 3);
            $insertItem->execute([$orderId, $item['id'], $item['price'], $qty]);
            $total += $item['price'] * $qty;
        }

        $method = ['CASH', 'CARD', 'MOBILE'][mt_rand(0, 2)];
        $insertPayment->execute([$orderId, $method, $total, $cashierStaffId, $createdAt]);
        $pdo->prepare('UPDATE orders SET total_amount = ? WHERE id = ?')->execute([$total, $orderId]);
    }

    $unpaid = (int) $pdo->query("
        SELECT COUNT(*) FROM orders
        WHERE status <> 'CANCELLED'
          AND DATE(created_at) = " . $pdo->quote($day) . "
          AND NOT EXISTS (SELECT 1 FROM payments p WHERE p.order_id = orders.id)
    ")->fetchColumn();
    echo "Business day $day order seed done: 5 paid added, unpaid queued: $unpaid\n";
}

$staffList = $pdo->query('SELECT employee_num, first_name, last_name, r.name AS role FROM staff s JOIN roles r ON r.id = s.role_id ORDER BY s.id')->fetchAll();
echo "--- Staff ---\n";
foreach ($staffList as $s) {
    echo sprintf("  %-8s %-12s %-8s %s\n", $s['employee_num'], $s['first_name'], $s['last_name'], $s['role']);
}
echo "--- Tables ---\n";
foreach ($pdo->query('SELECT number, capacity, status FROM tables ORDER BY number') as $t) {
    echo sprintf("  %-4d cap %-3d %s\n", $t['number'], $t['capacity'], $t['status']);
}
echo "Done.\n";