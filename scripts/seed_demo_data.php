<?php

declare(strict_types=1);

use App\Core\Database;

require __DIR__ . '/../src/Core/Database.php';

/**
 * Demo data seeder for the live `rms` database.
 *
 * Usage:
 *   php scripts/seed_demo_data.php            # add demo data (idempotent for inventory/menu)
 *   php scripts/seed_demo_data.php --reset    # wipe orders/payments first, then rebuild
 */

$pdo = Database::getConnection();
$reset = in_array('--reset', $argv, true);

$existingOrders = (int) $pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
if (!$reset && $existingOrders > 40) {
    fwrite(STDERR, "Orders already seeded ({$existingOrders}). Pass --reset to rebuild order/payment data.\n");
    exit(1);
}

if ($reset) {
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    $pdo->exec('DELETE FROM order_items');
    $pdo->exec('DELETE FROM payments');
    $pdo->exec('DELETE FROM orders');
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
    echo "Reset orders/payments.\n";
}

mt_srand(20260912);

// ── Categories ───────────────────────────────────────────────────────────────
$categories = [
    'Starters' => 'KITCHEN',
    'Desserts' => 'KITCHEN',
];
$catId = [];
foreach ($pdo->query('SELECT id, name FROM menu_categories') as $row) {
    $catId[$row['name']] = (int) $row['id'];
}
$insertCat = $pdo->prepare('INSERT INTO menu_categories (name, station) VALUES (?, ?)');
foreach ($categories as $name => $station) {
    if (!isset($catId[$name])) {
        $insertCat->execute([$name, $station]);
        $catId[$name] = (int) $pdo->lastInsertId();
        echo "Category: $name\n";
    }
}

// ── Ingredients (inventory) ─────────────────────────────────────────────────
// name => [base_unit, stock, cost_per_unit, reorder_level, receive_unit, units_per_container]
$ingredients = [
    'Beef'                 => ['g',   8000, 1.00,   2000, 'packet', 10],
    'Chicken Breast'       => ['g',   8000, 0.35,  2000, 'box',   20],
    'Beef Mince'           => ['g',   4000, 0.35,  1000, 'box',   10],
    'Fish Fillets'         => ['g',   5000, 0.45,  1500, 'box',   10],
    'Potatoes'             => ['g',  12000, 0.08,  3000, 'packet', 50],
    'Cooking Oil'          => ['ml', 15000, 0.12,  4000, 'case',  12],
    'Rice'                 => ['g',  20000, 0.10,  5000, 'packet', 25],
    'Wheat Flour'          => ['g',  10000, 0.09,  3000, 'packet', 10],
    'Maize Flour'          => ['g',   8000, 0.08,  2000, 'packet', 10],
    'Collard Greens'       => ['g',   6000, 0.05,  1500, 'packet', 5],
    'Tomatoes'             => ['g',   5000, 0.10,  2000, 'case',   20],
    'Onions'               => ['g',   4000, 0.08,  1500, 'packet', 10],
    'Garlic'               => ['g',   1500, 0.30,   500, 'packet', 5],
    'Ginger'               => ['g',   1200, 0.30,   400, 'packet', 5],
    'Bell Pepper'          => ['g',    800, 0.25,   300, 'packet', 10],
    'Salt'                 => ['g',  20000, 0.01,  5000, 'packet', 20],
    'Black Pepper'         => ['g',   1000, 0.50,   300, 'packet', 5],
    'Sugar'                => ['g',  15000, 0.06,  4000, 'packet', 20],
    'Milk'                 => ['ml', 10000, 0.06,  3000, 'carton', 10],
    'Butter'               => ['g',   3000, 0.35,   800, 'packet', 10],
    'Mozzarella Cheese'    => ['g',   1500, 0.80,   400, 'packet', 5],
    'Chocolate'            => ['g',   2000, 0.70,   500, 'packet', 10],
    'Coffee Beans'         => ['g',   4000, 0.30,  1000, 'packet', 5],
    'Tea Leaves'           => ['g',   3000, 0.25,   800, 'packet', 5],
    'Samosa Pastry'        => ['pcs', 1000, 20.00,   300, 'packet', 50],
    'Chicken Marinade'     => ['ml',  5000, 0.20,  1000, 'packet', 5],
    'Ice Cream'            => ['ml', 10000, 0.15,  2000, 'carton', 10],
    'Avocado'              => ['pcs',  800, 80.00,   200, 'case',   50],
    'Oranges'              => ['pcs', 1500, 30.00,   400, 'case',   100],
    'Mangoes'              => ['pcs', 1000, 50.00,   300, 'case',   50],
    'Bananas'              => ['pcs', 1500, 10.00,   400, 'case',   100],
    'Limes'                => ['pcs',  600,  5.00,   200, 'case',   50],
    'Lettuce'              => ['pcs',  500, 25.00,   150, 'case',   50],
    'Wheat Buns'           => ['pcs',  800, 20.00,   200, 'case',   50],
    'Coca-Cola'            => ['pcs',  480, 60.00,   120, 'case',   24],
    'Fanta Orange'         => ['pcs',  480, 60.00,   120, 'case',   24],
    'Sprite'               => ['pcs',  480, 60.00,   120, 'case',   24],
    'Tusker Beer'          => ['pcs',  200, 180.00,   50, 'case',   12],
    'White Cap Beer'       => ['pcs',  160, 180.00,   40, 'case',   12],
    'Soda Water'           => ['pcs',  200, 45.00,    50, 'case',   24],
];

$ingredientIds = [];
foreach ($pdo->query('SELECT id, name FROM inventory') as $row) {
    $ingredientIds[$row['name']] = (int) $row['id'];
}
$upsertIngredient = $pdo->prepare('
    INSERT INTO inventory (name, base_unit, receive_unit, units_per_container, stock, cost_per_unit, reorder_level, active)
    VALUES (?, ?, ?, ?, ?, ?, ?, 1)
    ON DUPLICATE KEY UPDATE
        stock = VALUES(stock),
        cost_per_unit = VALUES(cost_per_unit),
        reorder_level = VALUES(reorder_level),
        active = 1
');
foreach ($ingredients as $name => [$base, $stock, $cost, $reorder, $receive, $units]) {
    $upsertIngredient->execute([$name, $base, $receive, $units, $stock, $cost, $reorder]);
    $ingredientIds[$name] = $ingredientIds[$name]
        ?? (int) $pdo->query('SELECT id FROM inventory WHERE name = ' . $pdo->quote($name))->fetchColumn();
}
echo 'Ingredients: ' . count($ingredients) . " ensured.\n";

// ── Menu items ──────────────────────────────────────────────────────────────
// name => [category, price, description, [ingredient => quantity]]
$menu = [
    // Starters
    'Beef Samosas (3 pcs)'    => ['Starters', 350, 'Crispy pastry parcels filled with spiced beef mince.', ['Beef Mince' => 120, 'Samosa Pastry' => 3, 'Cooking Oil' => 30, 'Onions' => 20, 'Salt' => 2]],
    'Chicken Wings (6 pcs)'   => ['Starters', 550, 'Grilled wings tossed in house marinade.', ['Chicken Breast' => 300, 'Chicken Marinade' => 60, 'Cooking Oil' => 20, 'Black Pepper' => 2]],
    'Onion Rings'             => ['Starters', 300, 'Golden battered onion rings.', ['Onions' => 150, 'Wheat Flour' => 60, 'Cooking Oil' => 40, 'Salt' => 2]],
    'Garlic Cheese Bread'     => ['Starters', 400, 'Toasted bread topped with garlic butter and mozzarella.', ['Wheat Buns' => 1, 'Mozzarella Cheese' => 40, 'Butter' => 15, 'Garlic' => 5]],
    'Avocado Salad'           => ['Starters', 450, 'Fresh avocado, tomato and onion salad.', ['Avocado' => 1, 'Tomatoes' => 80, 'Onions' => 40, 'Salt' => 2, 'Black Pepper' => 1]],
    // Mains
    'Grilled Chicken (Quarter)' => ['Mains', 650, 'Flame grilled quarter chicken with marinade.', ['Chicken Breast' => 250, 'Chicken Marinade' => 50, 'Cooking Oil' => 20, 'Salt' => 3, 'Black Pepper' => 2]],
    'Nyama Choma'             => ['Mains', 1200, 'Slow roasted goat-style beef ribs, served with kachumbari.', ['Beef' => 400, 'Salt' => 4, 'Black Pepper' => 2, 'Tomatoes' => 40, 'Onions' => 40]],
    'Pilau Rice'              => ['Mains', 500, 'Fragrant spiced rice cooked in beef stock.', ['Rice' => 250, 'Onions' => 40, 'Garlic' => 5, 'Ginger' => 5, 'Salt' => 3]],
    'Beef Stew'               => ['Mains', 950, 'Slow cooked beef in rich tomato gravy.', ['Beef' => 250, 'Tomatoes' => 100, 'Onions' => 60, 'Garlic' => 5, 'Cooking Oil' => 20, 'Salt' => 3, 'Black Pepper' => 2]],
    'Fish & Chips'            => ['Mains', 800, 'Battered fish fillet with chunky chips.', ['Fish Fillets' => 200, 'Potatoes' => 250, 'Wheat Flour' => 50, 'Cooking Oil' => 40, 'Salt' => 3]],
    'Veggie Burger'           => ['Mains', 450, 'Grilled vegetable patty burger with fresh toppings.', ['Wheat Buns' => 1, 'Avocado' => 1, 'Tomatoes' => 40, 'Onions' => 30, 'Lettuce' => 1, 'Mozzarella Cheese' => 20]],
    'Chapati'                 => ['Mains', 150, 'Soft layered flatbread.', ['Wheat Flour' => 120, 'Cooking Oil' => 15, 'Salt' => 2]],
    'Ugali & Sukuma'          => ['Mains', 250, 'Stiff maize meal with sauteed collard greens.', ['Maize Flour' => 200, 'Collard Greens' => 120, 'Cooking Oil' => 15, 'Salt' => 2]],
    // Drinks
    'Coca-Cola (330ml)'       => ['Drinks', 100, 'Chilled cola in a glass bottle.', ['Coca-Cola' => 1]],
    'Fanta Orange (330ml)'    => ['Drinks', 100, 'Chilled orange soda.', ['Fanta Orange' => 1]],
    'Sprite (330ml)'          => ['Drinks', 100, 'Chilled lemon-lime soda.', ['Sprite' => 1]],
    'Tusker Beer (500ml)'     => ['Drinks', 250, 'Kenyan lager, served ice cold.', ['Tusker Beer' => 1]],
    'White Cap Beer (500ml)'  => ['Drinks', 250, 'Smooth light lager.', ['White Cap Beer' => 1]],
    'Fresh Orange Juice'      => ['Drinks', 300, 'Freshly squeezed oranges.', ['Oranges' => 3]],
    'Mango Juice'             => ['Drinks', 300, 'Thick fresh mango juice.', ['Mangoes' => 2]],
    'Fresh Lime Soda'         => ['Drinks', 200, 'Sparkling soda with fresh lime and sugar.', ['Limes' => 2, 'Soda Water' => 1, 'Sugar' => 10]],
    'Masala Tea'              => ['Drinks', 150, 'Spiced milk tea.', ['Tea Leaves' => 5, 'Milk' => 150, 'Sugar' => 15, 'Ginger' => 3]],
    'Cappuccino'              => ['Drinks', 300, 'Espresso with steamed milk and foam.', ['Coffee Beans' => 15, 'Milk' => 100, 'Sugar' => 10]],
    // Desserts
    'Chocolate Cake (Slice)'  => ['Desserts', 350, 'Rich moist chocolate cake.', ['Chocolate' => 40, 'Wheat Flour' => 50, 'Sugar' => 40, 'Butter' => 20, 'Milk' => 30]],
    'Vanilla Ice Cream'       => ['Desserts', 250, 'Two scoops of vanilla ice cream.', ['Ice Cream' => 150]],
    'Fruit Salad'             => ['Desserts', 250, 'Seasonal mixed fruit cup.', ['Mangoes' => 1, 'Bananas' => 1, 'Oranges' => 1]],
    'Banana Pancakes'         => ['Desserts', 280, 'Fluffy pancakes with caramelised banana.', ['Bananas' => 2, 'Wheat Flour' => 80, 'Milk' => 80, 'Sugar' => 20, 'Butter' => 15]],
];

$itemId = [];
foreach ($pdo->query('SELECT id, name FROM menu_items') as $row) {
    $itemId[$row['name']] = (int) $row['id'];
}
$insertItem = $pdo->prepare('INSERT INTO menu_items (name, description, price, category_id, active) VALUES (?, ?, ?, ?, 1)');
$insertIngredient = $pdo->prepare('INSERT INTO menu_item_ingredients (menu_item_id, inventory_id, quantity, unit) VALUES (?, ?, ?, ?)');
foreach ($menu as $name => [$category, $price, $description, $recipe]) {
    if (isset($itemId[$name])) {
        $id = $itemId[$name];
    } else {
        $insertItem->execute([$name, $description, $price, $catId[$category]]);
        $id = (int) $pdo->lastInsertId();
        $itemId[$name] = $id;
    }
    $check = $pdo->prepare('SELECT COUNT(*) FROM menu_item_ingredients WHERE menu_item_id = ?');
    $check->execute([$id]);
    if ((int) $check->fetchColumn() === 0) {
        foreach ($recipe as $ingredient => $qty) {
            $insertIngredient->execute([$id, $ingredientIds[$ingredient], $qty, $ingredients[$ingredient][0]]);
        }
    }
}
echo 'Menu items ensured: ' . count($menu) . "\n";

// ── Orders & payments ───────────────────────────────────────────────────────
$staffIdx = $pdo->query('SELECT id FROM staff ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);
$waiterId = (int) $pdo->query("SELECT id FROM staff WHERE role_id = (SELECT id FROM roles WHERE name = 'WAITER') LIMIT 1")->fetchColumn();
$cashierId = (int) $pdo->query("SELECT id FROM staff WHERE role_id = (SELECT id FROM roles WHERE name = 'MANAGER') LIMIT 1")->fetchColumn();

$gItemId = [];
foreach ($pdo->query('SELECT id, name, price FROM menu_items') as $row) {
    $gItemId[$row['name']] = ['id' => (int) $row['id'], 'price' => (float) $row['price']];
}
$kitchenItems = array_keys(array_filter($menu, fn($m) => $m[0] !== 'Drinks'));
$barItems = array_keys(array_filter($menu, fn($m) => $m[0] === 'Drinks'));

$orderStmt = $pdo->prepare('
    INSERT INTO orders (order_number, status, type, user_id, table_id, total_amount, closed_at, created_at)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
');
$itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, menu_item_id, price_at_time, quantity, served) VALUES (?, ?, ?, ?, ?)');
$payStmt = $pdo->prepare('INSERT INTO payments (order_id, method, amount, cashier_id, created_at) VALUES (?, ?, ?, ?, ?)');

$orderCount = 0;
$paymentCount = 0;
$orderIdsByDay = [];

$makeItems = function (array $pool, int $max) use ($gItemId): array {
    $count = mt_rand(1, $max);
    $picked = array_rand(array_flip($pool), min($count, count($pool)));
    if (!is_array($picked)) {
        $picked = [$picked];
    }
    $items = [];
    foreach ($picked as $name) {
        $items[] = ['name' => $name, 'qty' => mt_rand(1, 3)];
    }
    return $items;
};

$tableIds = $pdo->query('SELECT id FROM tables ORDER BY id')->fetchAll(PDO::FETCH_COLUMN);
$pickTable = fn (): ?int => $tableIds ? $tableIds[array_rand($tableIds)] : null;

$insertOrder = function (string $day, array $items, string $status, ?int $table = null) use (
    $pdo, $orderStmt, $itemStmt, $payStmt, $gItemId, $waiterId, $cashierId, &$orderCount, &$paymentCount, &$orderIdsByDay
): int {
    $orderNumber = 'ORD-' . str_replace('-', '', $day) . '-' . strtoupper(str_pad(dechex(mt_rand(0, 65535)), 4, '0', STR_PAD_LEFT));
    $type = mt_rand(0, 9) < 2 ? 'TAKEAWAY' : 'DINE_IN';
    $usedTable = $type === 'DINE_IN' ? $table : null;
    $total = array_sum(array_map(fn($it) => $gItemId[$it['name']]['price'] * $it['qty'], $items));
    $hour = mt_rand(11, 21);
    $minute = str_pad((string) mt_rand(0, 59), 2, '0', STR_PAD_LEFT);
    $time = sprintf('%02d:%02d:00', $hour, $minute);
    $createdAt = "$day $time";
    $closedAt = $status === 'PAYED' ? $createdAt : null;

    $orderStmt->execute([$orderNumber, $status, $type, $waiterId, $usedTable, $total, $closedAt, $createdAt]);
    $orderId = (int) $pdo->lastInsertId();
    $orderIdsByDay[$day][] = $orderId;

    foreach ($items as $it) {
        $itemStmt->execute([$orderId, $gItemId[$it['name']]['id'], $gItemId[$it['name']]['price'], $it['qty'], 0]);
    }

    if ($status === 'PAYED') {
        $roll = mt_rand(1, 100);
        $method = $roll <= 45 ? 'CASH' : ($roll <= 75 ? 'CARD' : 'MOBILE');
        $payStmt->execute([$orderId, $method, $total, $cashierId, $createdAt]);
        $paymentCount++;
    }
    $orderCount++;

    return $orderId;
};

// Per-day footfall (weekday lull, Friday/Saturday spike).
$dayPlan = [
    '2026-09-06' => 12,
    '2026-09-07' => 10,
    '2026-09-08' => 13,
    '2026-09-09' =>  9,
    '2026-09-10' => 14,
    '2026-09-11' => 19,
    '2026-09-12' => 17,
    '2026-09-13' =>  6,
];

foreach ($dayPlan as $day => $count) {
    for ($i = 0; $i < $count; $i++) {
        if ($day === '2026-09-12') {
            // Today: paid history + served + pending on kitchen/bar.
            if ($i < 8) {
                $insertOrder($day, $makeItems(array_merge($kitchenItems, $barItems), 3), 'PAYED', $pickTable());
            } elseif ($i < 10) {
                $items = $makeItems(array_merge($kitchenItems, $barItems), 2);
                $orderId = $insertOrder($day, $items, 'SERVED', $pickTable());
                $pdo->exec("UPDATE order_items SET served = 1 WHERE order_id = $orderId");
            } else {
                $c = $i - 10; // 0..6 pending kitchen/bar
                if ($c === 0) {
                    $insertOrder($day, $makeItems($kitchenItems, 3), 'PLACED', $pickTable());
                } elseif ($c === 1) {
                    $insertOrder($day, $makeItems($kitchenItems, 2), 'PLACED', $pickTable());
                } elseif ($c === 2) {
                    $insertOrder($day, $makeItems($kitchenItems, 2), 'PLACED', $pickTable());
                } elseif ($c === 3) {
                    $insertOrder($day, $makeItems($barItems, 2), 'PLACED', $pickTable());
                } elseif ($c === 4) {
                    $insertOrder($day, $makeItems($barItems, 3), 'PLACED', $pickTable());
                } elseif ($c === 5) {
                    $items = array_merge($makeItems($kitchenItems, 1), $makeItems($barItems, 1));
                    $insertOrder($day, $items, 'PLACED', $pickTable());
                } else {
                    $items = array_merge($makeItems($kitchenItems, 2), $makeItems($barItems, 2));
                    $insertOrder($day, $items, 'PLACED', $pickTable());
                }
            }
        } elseif ($day === '2026-09-13') {
            // Next business day: open orders queued for the cashier.
            $insertOrder($day, $makeItems(array_merge($kitchenItems, $barItems), 3), 'PLACED', $pickTable());
        } else {
            $roll = mt_rand(1, 100);
            if ($roll <= 68) {
                $insertOrder($day, $makeItems(array_merge($kitchenItems, $barItems), 3), 'PAYED', $pickTable());
            } elseif ($roll <= 95) {
                $insertOrder($day, $makeItems(array_merge($kitchenItems, $barItems), 3), 'PLACED', $pickTable());
            } else {
                $insertOrder($day, $makeItems(array_merge($kitchenItems, $barItems), 2), 'CANCELLED', $pickTable());
            }
        }
    }
}

$summary = $pdo->query('
    SELECT status, COUNT(*) AS c FROM orders GROUP BY status ORDER BY c DESC
')->fetchAll(PDO::FETCH_KEY_PAIR);
echo 'Orders created: ' . $orderCount . ', payments: ' . $paymentCount . ".\n";
foreach ($summary as $s => $c) {
    echo "  $s: $c\n";
}

$todayPending = (int) $pdo->query("
    SELECT COUNT(*)
    FROM orders o
    WHERE o.status = 'PLACED' AND DATE(o.created_at) = CURDATE()
      AND EXISTS (SELECT 1 FROM order_items oi JOIN menu_items mi ON mi.id = oi.menu_item_id
                  JOIN menu_categories mc ON mc.id = mi.category_id
                  WHERE oi.order_id = o.id AND oi.served = 0 AND mc.station = 'KITCHEN')
")->fetchColumn();
echo "Kitchen pending today: $todayPending\n";
$todayPendingBar = (int) $pdo->query("
    SELECT COUNT(*)
    FROM orders o
    WHERE o.status = 'PLACED' AND DATE(o.created_at) = CURDATE()
      AND EXISTS (SELECT 1 FROM order_items oi JOIN menu_items mi ON mi.id = oi.menu_item_id
                  JOIN menu_categories mc ON mc.id = mi.category_id
                  WHERE oi.order_id = o.id AND oi.served = 0 AND mc.station = 'BAR')
")->fetchColumn();
echo "Bar pending today: $todayPendingBar\n";

echo "Done.\n";