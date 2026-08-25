<?php

namespace Tests\Integration;

use App\Core\Database;
use PDO;
use RuntimeException;

/**
 * Provisions the rms_test database: recreates it from the live schema
 * (mysqldump --no-data) and seeds a known baseline.
 */
class DatabaseSetup
{
    public static function provision(): void
    {
        $host = getenv('RMS_DB_HOST') ?: '127.0.0.1';
        $port = getenv('RMS_DB_PORT') ?: '3306';
        $user = getenv('RMS_DB_USER') ?: 'user';
        $pass = getenv('RMS_DB_PASS') ?: 'password';

        $cli = "mysql -h$host -P$port -u$user -p$pass";

        exec("$cli -e 'DROP DATABASE IF EXISTS rms_test' 2>/dev/null", $_, $code);
        if ($code !== 0) {
            throw new RuntimeException('Failed to drop rms_test database.');
        }

        exec("$cli -e 'CREATE DATABASE rms_test' 2>/dev/null", $_, $code);
        if ($code !== 0) {
            throw new RuntimeException('Failed to create rms_test database.');
        }

        exec("mysqldump --no-data -h$host -P$port -u$user -p$pass rms 2>/dev/null | $cli rms_test 2>/dev/null", $_, $code);
        if ($code !== 0) {
            throw new RuntimeException('Failed to copy schema into rms_test.');
        }

        $pdo = Database::getConnection();
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $pdo->exec('TRUNCATE TABLE orders');
        $pdo->exec('TRUNCATE TABLE order_items');
        $pdo->exec('TRUNCATE TABLE payments');
        $pdo->exec('TRUNCATE TABLE staff');
        $pdo->exec('TRUNCATE TABLE role_permissions');
        $pdo->exec('TRUNCATE TABLE permissions');
        $pdo->exec('TRUNCATE TABLE roles');
        $pdo->exec('TRUNCATE TABLE menu_items');
        $pdo->exec('TRUNCATE TABLE menu_categories');
        $pdo->exec('TRUNCATE TABLE menu_item_ingredients');
        $pdo->exec('TRUNCATE TABLE inventory_movements');
        $pdo->exec('TRUNCATE TABLE inventory');
        $pdo->exec('TRUNCATE TABLE stock_take_items');
        $pdo->exec('TRUNCATE TABLE stock_takes');
        $pdo->exec('TRUNCATE TABLE audit_logs');
        $pdo->exec('TRUNCATE TABLE tables');
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

        self::seed($pdo);
    }

    public static function reset(): void
    {
        $pdo = Database::getConnection();

        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        $pdo->exec('TRUNCATE TABLE orders');
        $pdo->exec('TRUNCATE TABLE order_items');
        $pdo->exec('TRUNCATE TABLE payments');
        $pdo->exec('TRUNCATE TABLE staff');
        $pdo->exec('TRUNCATE TABLE role_permissions');
        $pdo->exec('TRUNCATE TABLE permissions');
        $pdo->exec('TRUNCATE TABLE roles');
        $pdo->exec('TRUNCATE TABLE menu_items');
        $pdo->exec('TRUNCATE TABLE menu_categories');
        $pdo->exec('TRUNCATE TABLE menu_item_ingredients');
        $pdo->exec('TRUNCATE TABLE inventory_movements');
        $pdo->exec('TRUNCATE TABLE inventory');
        $pdo->exec('TRUNCATE TABLE stock_take_items');
        $pdo->exec('TRUNCATE TABLE stock_takes');
        $pdo->exec('TRUNCATE TABLE audit_logs');
        $pdo->exec('TRUNCATE TABLE tables');
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

        self::seed($pdo);
    }

    /**
     * Baseline: MANAGER/WAITER/HEAD CHEF roles, permissions (mirrors
     * sql/seed_rbac.sql), one user per role, two menu items, two tables.
     */
    private static function seed(PDO $pdo): void
    {
        $stmt = $pdo->prepare('INSERT INTO roles (name) VALUES (?)');
        $stmt->execute(['MANAGER']);
        $managerRoleId = (int) $pdo->lastInsertId();
        $stmt->execute(['WAITER']);
        $waiterRoleId = (int) $pdo->lastInsertId();
        $stmt->execute(['HEAD CHEF']);
        $chefRoleId = (int) $pdo->lastInsertId();
        $stmt->execute(['BARTENDER']);
        $bartenderRoleId = (int) $pdo->lastInsertId();

        $permissions = [
            'dashboard.view', 'kitchen.view', 'bar.view', 'users.view', 'users.create',
            'users.update', 'users.deactivate', 'roles.view', 'roles.create',
            'roles.update', 'roles.deactivate', 'menu.view', 'menu.create',
            'menu.update', 'menu.deactivate', 'categories.view', 'categories.create',
            'categories.update', 'categories.deactivate', 'inventory.view', 'inventory.create',
            'inventory.update', 'inventory.deactivate',
            'inventory.stocktake', 'inventory.variance', 'log.view', 'reports.view', 'store.view',
        ];
        $permStmt = $pdo->prepare('INSERT INTO permissions (name) VALUES (?)');
        $permissionIds = [];
        foreach ($permissions as $name) {
            $permStmt->execute([$name]);
            $permissionIds[$name] = (int) $pdo->lastInsertId();
        }

        $grantStmt = $pdo->prepare('INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)');
        foreach ($permissionIds as $permissionId) {
            $grantStmt->execute([$managerRoleId, $permissionId]);
        }
        $grantStmt->execute([$chefRoleId, $permissionIds['kitchen.view']]);
        $grantStmt->execute([$bartenderRoleId, $permissionIds['bar.view']]);

        $userStmt = $pdo->prepare(
            'INSERT INTO staff (employee_num, first_name, last_name, national_id, pin, pin_hash, password_hash, role_id, active)
             VALUES (?, ?, ?, ?, ?, NULL, ?, ?, 1)'
        );
        $userStmt->execute(['MGR001', 'Manager', 'Main', '30000001', '1111', password_hash('manager123', PASSWORD_BCRYPT), $managerRoleId]);
        $userStmt->execute(['WTR001', 'Brian', 'Otieno', '222222', '1234', null, $waiterRoleId]);
        $userStmt->execute(['CHF001', 'Chef', 'Mkuu', 'CHEF01', '5678', null, $chefRoleId]);
        $userStmt->execute(['BTR001', 'Bar', 'Tender', 'BTR001', '9012', null, $bartenderRoleId]);

        $catStmt = $pdo->prepare('INSERT INTO menu_categories (name, station) VALUES (?, ?)');
        $catStmt->execute(['Mains', 'KITCHEN']);
        $mainsId = (int) $pdo->lastInsertId();
        $catStmt->execute(['Drinks', 'BAR']);
        $drinksId = (int) $pdo->lastInsertId();
        $catStmt->execute(['Hot Beverages', 'KITCHEN']);
        $hotId = (int) $pdo->lastInsertId();

        $itemStmt = $pdo->prepare('INSERT INTO menu_items (name, price, category_id) VALUES (?, ?, ?)');
        $itemStmt->execute(['Chicken Soup', 250.00, $mainsId]);
        $itemStmt->execute(['Soda', 100.00, $drinksId]);
        $itemStmt->execute(['Drip Coffee', 200.00, $hotId]);

        $tableStmt = $pdo->prepare('INSERT INTO tables (number, capacity) VALUES (?, ?)');
        $tableStmt->execute([1, 4]);
        $tableStmt->execute([2, 6]);
    }
}