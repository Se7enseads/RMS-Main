<?php

namespace Tests\E2E;

class ManagerFlowTest extends BrowserTestCase
{
    public function testManagerCanCreateMenuItemAndLogout(): void
    {
        $itemName = 'E2E Test Burger ' . substr(md5((string) mt_rand()), 0, 6);

        $this->loginWithPassword('MGR001', 'manager123');

        $this->expect($this->page)->toHaveURL($this->baseUrl() . '/');
        $this->expect($this->page->locator('h1'))->toContainText('Dashboard');

        // go to menu items
        $this->page->locator('nav a[href="/items"]')->click();
        $this->expect($this->page)->toHaveURL($this->baseUrl() . '/items');
        $this->expect($this->page->locator('h1'))->toContainText('Menu Items');

        // create a new item
        $this->page->locator('a[href="/items/create"]')->click();
        $this->page->locator('input[name="name"]')->fill($itemName);
        $this->page->locator('input[name="price"]')->fill('150.00');
        $this->page->locator('form[action="/items/create"] button[type="submit"]')->click();

        // back on the list, item is visible
        $this->expect($this->page)->toHaveURL($this->baseUrl() . '/items');
        $this->expect($this->page->locator('.items-table'))->toContainText($itemName);

        $this->cleanupMenuItemByName($itemName);

        // logout
        $this->page->locator('.nav-logout-btn')->click();
        $this->expect($this->page)->toHaveURL($this->baseUrl() . '/login');
    }
}