<?php

namespace Tests\E2E;

class ChefFlowTest extends BrowserTestCase
{
    public function testChefSeesPlacedOrderAndMarksItServed(): void
    {
        $orderId = $this->placeOrderViaKiosk('1234', 1, ['Chicken Soup']);

        try {
            $orderNumber = $this->liveDb()
                ->query("SELECT order_number FROM orders WHERE id = $orderId")
                ->fetchColumn();

            // switch to the chef (must log out first: /login redirects logged-in users away)
            $this->page->locator('form[action="/logout"] button')->click();
            $this->page->waitForURL($this->baseUrl() . '/login');
            $this->loginWithPin('5678');

            // chef lands on the kitchen display
            $this->expect($this->page)->toHaveURL($this->baseUrl() . '/kitchen');
            $this->expect($this->page->locator('h1'))->toContainText('Kitchen Display');

            // order is waiting
            $waitingCard = $this->page->locator('.receipt-card')->filter(['hasText' => $orderNumber]);
            $this->expect($waitingCard)->toBeVisible();

            // serve it
            $waitingCard->locator('button[type="submit"]')->click();
            $this->expect($this->page)->toHaveURL($this->baseUrl() . '/kitchen');

            // moved to served history
            $this->expect($this->page->locator('.served-history'))->toContainText($orderNumber);
            $this->expect($this->page->locator('.receipt-card')->filter(['hasText' => $orderNumber]))->not()->toBeVisible();
        } finally {
            $this->cleanupOrder($orderId);
        }
    }
}