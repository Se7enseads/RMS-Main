<?php

namespace Tests\E2E;

use PDO;
use Playwright\Testing\PlaywrightTestCase;

abstract class BrowserTestCase extends PlaywrightTestCase
{
    protected function baseUrl(): string
    {
        return getenv('RMS_E2E_BASE_URL') ?: 'http://localhost:8080';
    }

    /**
     * E2E runs against the LIVE app database (bootstrap.php points
     * Database at rms_test, but the dev server writes to rms).
     */
    protected function liveDb(): PDO
    {
        return new PDO(
            'mysql:host=' . (getenv('RMS_E2E_DB_HOST') ?: '127.0.0.1')
            . ';port=' . (getenv('RMS_E2E_DB_PORT') ?: '3306')
            . ';dbname=' . (getenv('RMS_E2E_DB_NAME') ?: 'rms') . ';charset=utf8mb4',
            getenv('RMS_E2E_DB_USER') ?: 'user',
            getenv('RMS_E2E_DB_PASS') ?: 'password',
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
        );
    }

    protected function loginWithPin(string $pin): void
    {
        $this->page->goto($this->baseUrl() . '/login');

        foreach (str_split($pin) as $digit) {
            $this->page->getByRole('button', ['name' => $digit, 'exact' => true])->click();
        }

        $this->page->waitForFunction('location.pathname !== "/login"');
    }

    protected function loginWithPassword(string $employeeNum, string $password): void
    {
        $this->page->goto($this->baseUrl() . '/login');
        $this->page->getByRole('button', ['name' => 'Back Office', 'exact' => true])->click();
        $this->page->locator('input[name="employee_num"]')->fill($employeeNum);
        $this->page->locator('input[name="password"]')->fill($password);
        $this->page->locator('#panel-password button[type="submit"]')->click();
    }

    protected function placeOrderViaKiosk(string $pin, int $tableId, array $itemNames = ['Chicken Soup', 'Soda']): int
    {
        $this->loginWithPin($pin);

        $this->page->goto($this->baseUrl() . '/kiosk/order');
        foreach ($itemNames as $itemName) {
            $this->page->locator('.menu-item')->filter(['hasText' => $itemName])->click();
        }
        $this->page->locator('#table-select')->selectOption((string)$tableId);
        $this->page->locator('.place-order-btn')->click();

        $this->expect($this->page)->toHaveURL($this->baseUrl() . '/kiosk');

        return (int)$this->liveDb()
            ->query('SELECT id FROM orders ORDER BY id DESC LIMIT 1')
            ->fetchColumn();
    }

    protected function cleanupOrder(int $orderId): void
    {
        $db = $this->liveDb();
        $stmt = $db->prepare('DELETE FROM order_items WHERE order_id = ?');
        $stmt->execute([$orderId]);
        $stmt = $db->prepare('DELETE FROM orders WHERE id = ?');
        $stmt->execute([$orderId]);
    }

    protected function cleanupMenuItemByName(string $name): void
    {
        $stmt = $this->liveDb()->prepare('DELETE FROM menu_items WHERE name = ?');
        $stmt->execute([$name]);
    }
}