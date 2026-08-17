<?php

namespace Tests\E2E;

class WaiterFlowTest extends BrowserTestCase
{
    public function testWaiterCanPlaceDineInOrder(): void
    {
        $orderId = $this->placeOrderViaKiosk('1234', 1);

        try {
            // order card appears on the kiosk dashboard with the right details
            $openCard = $this->page->locator('.order-card.open')->first();
            $this->expect($openCard)->toContainText('Table 1');
            $this->expect($openCard)->toContainText('PLACED');

            $db = $this->liveDb();
            $order = $db->query("SELECT * FROM orders WHERE id = $orderId")->fetch();

            $this->assertSame('PLACED', $order['status']);
            $this->assertSame('DINE_IN', $order['type']);
            $this->assertSame(350.0, (float)$order['total_amount']);
        } finally {
            $this->cleanupOrder($orderId);
        }
    }
}