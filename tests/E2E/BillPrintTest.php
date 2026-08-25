<?php

namespace Tests\E2E;

use PDO;

class BillPrintTest extends BrowserTestCase
{
    private int $orderId = 0;
    private string $orderNumber = '';

    protected function tearDown(): void
    {
        // remove the seeded order so other flow tests are not affected
        if ($this->orderId > 0) {
            $db = $this->liveDb();
            $db->exec("DELETE FROM order_items WHERE order_id = {$this->orderId}");
            $db->exec("DELETE FROM orders WHERE id = {$this->orderId}");
            if ($this->orderNumber !== '') {
                $db->exec("DELETE FROM audit_logs WHERE action = 'Bill printed: {$this->orderNumber}'");
            }
        }

        parent::tearDown();
    }

    public function testBillPageRendersReceiptSvg(): void
    {
        $this->ensureDemoData();
        $this->loginWithPin('1234'); // WTR001

        // seed an order directly
        $db = $this->liveDb();
        $userId = (int) $db->query("SELECT id FROM staff WHERE employee_num = 'WTR001'")->fetchColumn();
        $stmt = $db->prepare(
            "INSERT INTO orders (order_number, status, type, user_id, table_id, total_amount)
             VALUES (?, 'PLACED', 'DINE_IN', ?, 1, ?)"
        );
        $stmt->execute(['ORD-E2E-' . mt_rand(1000, 9999), $userId, 500.00]);
        $this->orderId = (int) $db->lastInsertId();
        $this->orderNumber = (string) $db->query("SELECT order_number FROM orders WHERE id = {$this->orderId}")->fetchColumn();
        $menuItemId = (int) $db->query("SELECT id FROM menu_items WHERE name = 'Chicken Soup'")->fetchColumn();
        $stmt = $db->prepare(
            'INSERT INTO order_items (order_id, menu_item_id, price_at_time, quantity) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$this->orderId, $menuItemId, 250.00, 2]);

        $this->page->goto($this->baseUrl() . "/kiosk/order/{$this->orderId}/bill");
        $this->page->waitForSelector('#bill-receipt svg', ['timeout' => 15000]);

        $text = $this->page->evaluate('() => [...document.querySelectorAll("#bill-receipt svg tspan")].map(t => t.textContent).join("").replace(/\\u00a0/g, " ")');
        $this->assertStringContainsString('RECEIPT', $text);
        $this->assertStringContainsString($this->orderNumber, $text);
        $this->assertStringContainsString('Chicken Soup', $text);
        $this->assertStringContainsString('500.00', $text);

        // audit log entry for the print
        $count = (int) $db->query("SELECT COUNT(*) FROM audit_logs WHERE action = 'Bill printed: {$this->orderNumber}'")->fetchColumn();
        $this->assertSame(1, $count);

        $this->page->screenshot('/tmp/opencode/bill-e2e.png');
    }
}