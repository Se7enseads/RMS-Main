<?php

namespace Tests\E2E;

use Playwright\Testing\PlaywrightTestCase;

class BarFlowTest extends BrowserTestCase
{
    public function testBartenderSeesDrinksOrderAndMarksItServed(): void
    {
        $orderId = $this->placeOrderViaKiosk('1234', 1, ['Soda']);

        try {
            $orderNumber = $this->liveDb()
                ->query("SELECT order_number FROM orders WHERE id = $orderId")
                ->fetchColumn();

            // switch to the bartender (log out first: /login redirects logged-in users away)
            $this->page->locator('form[action="/logout"] button')->click();
            $this->page->waitForURL($this->baseUrl() . '/login');
            $this->loginWithPin('9012');

            // bartender lands on the bar display
            $this->expect($this->page)->toHaveURL($this->baseUrl() . '/bar');
            $this->expect($this->page->locator('h1'))->toContainText('Bar Display');

            // drink order is waiting
            $waitingCard = $this->page->locator('.receipt-card')->filter(['hasText' => $orderNumber]);
            $this->expect($waitingCard)->toBeVisible();

            // serve it
            $waitingCard->locator('button[type="submit"]')->click();
            $this->expect($this->page)->toHaveURL($this->baseUrl() . '/bar');

            // moved to served history
            $this->expect($this->page->locator('.served-history'))->toContainText($orderNumber);
        } finally {
            $this->cleanupOrder($orderId);
        }
    }

    public function testBartenderPlacesDrinksOrderFromBar(): void
    {
        $this->loginWithPin('9012');

        try {
            $this->page->goto($this->baseUrl() . '/bar/order');

            // menu shows only bar drinks
            $this->expect($this->page->locator('.menu-item')->filter(['hasText' => 'Soda']))->toBeVisible();
            $this->expect($this->page->locator('.menu-item')->filter(['hasText' => 'Chicken Soup']))->toHaveCount(0);

            // place a drinks order
            $this->page->locator('.menu-item')->filter(['hasText' => 'Soda'])->click();
            $this->page->locator('#table-select')->selectOption('1');
            $this->page->locator('.place-order-btn')->click();

            // back on the bar display
            $this->expect($this->page)->toHaveURL($this->baseUrl() . '/bar');

            $orderId = (int)$this->liveDb()
                ->query('SELECT id FROM orders ORDER BY id DESC LIMIT 1')
                ->fetchColumn();
            $orderNumber = $this->liveDb()
                ->query("SELECT order_number FROM orders WHERE id = $orderId")
                ->fetchColumn();

            // the order is waiting on the bar display
            $waitingCard = $this->page->locator('.receipt-card')->filter(['hasText' => $orderNumber]);
            $this->expect($waitingCard)->toBeVisible();

            // serve it
            $waitingCard->locator('button[type="submit"]')->click();
            $this->expect($this->page)->toHaveURL($this->baseUrl() . '/bar');
            $this->expect($this->page->locator('.served-history'))->toContainText($orderNumber);
        } finally {
            $this->cleanupOrder($orderId ?? 0);
        }
    }
}