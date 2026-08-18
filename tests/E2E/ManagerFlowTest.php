<?php

namespace Tests\E2E;

class ManagerFlowTest extends BrowserTestCase
{
    public function testManagerCanCreateMenuItemAndLogout(): void
    {
        $itemName = 'E2E Test Burger ' . substr(md5((string) mt_rand()), 0, 6);

        $this->loginWithPassword('MGR001', 'manager123');

        $this->expect($this->page)->toHaveURL($this->baseUrl() . '/admin');
        $this->expect($this->page->locator('h1'))->toContainText('Dashboard');

        // go to menu items
        $this->page->goto($this->baseUrl() . '/admin/items');
        $this->expect($this->page)->toHaveURL($this->baseUrl() . '/admin/items');
        $this->expect($this->page->locator('h1'))->toContainText('Menu Items');
        $this->expect($this->page->locator('h1'))->toContainText('Menu Items');

        // create a new item
        $this->page->locator('a[href="/admin/items/create"]')->click();
        $this->page->locator('input[name="name"]')->fill($itemName);
        $this->page->locator('input[name="price"]')->fill('150.00');
        $this->page->locator('form[action="/admin/items/create"] button[type="submit"]')->click();

        // back on the list, item is visible
        $this->expect($this->page)->toHaveURL($this->baseUrl() . '/admin/items');
        $this->expect($this->page->locator('.tabulator'))->toContainText($itemName);

        $this->cleanupMenuItemByName($itemName);

        // logout
        $this->page->locator('form[action="/logout"] button')->click();
        $this->expect($this->page)->toHaveURL($this->baseUrl() . '/login');
    }

    public function testManagerCanEditAndDeactivateMenuItem(): void
    {
        $itemName = 'E2E Test Burger ' . substr(md5((string) mt_rand()), 0, 6);

        $this->loginWithPassword('MGR001', 'manager123');

        // create the item
        $this->page->locator('.sidebar-link[href="/admin/items"]')->click();
        $this->page->locator('a[href="/admin/items/create"]')->click();
        $this->page->locator('input[name="name"]')->fill($itemName);
        $this->page->locator('input[name="price"]')->fill('150.00');
        $this->page->locator('form[action="/admin/items/create"] button[type="submit"]')->click();
        $this->expect($this->page)->toHaveURL($this->baseUrl() . '/admin/items');

        try {
            // edit the item
            $row = $this->page->locator('.tabulator-row')->filter(['hasText' => $itemName]);
            $row->locator('a[href^="/admin/items/edit/"]')->click();
            $this->page->waitForFunction('location.pathname.startsWith("/admin/items/edit/")');
            $this->page->locator('input[name="name"]')->fill($itemName . ' (Edited)');
            $this->page->locator('form[action^="/admin/items/edit/"] button[type="submit"]')->click();
            $this->expect($this->page)->toHaveURL($this->baseUrl() . '/admin/items');
            $this->expect($this->page->locator('.tabulator'))->toContainText($itemName . ' (Edited)');

            // deactivate it (accept the confirm dialog)
            $this->page->events()->on('dialog', fn($dialog) => $dialog->accept());
            $row = $this->page->locator('.tabulator-row')->filter(['hasText' => $itemName . ' (Edited)']);
            $row->locator('form[action^="/admin/items/deactivate/"] button')->click();
            $this->expect($this->page->locator('.tabulator'))->not()->toContainText($itemName . ' (Edited)');
        } finally {
            $this->cleanupMenuItemByName($itemName);
            $this->cleanupMenuItemByName($itemName . ' (Edited)');
        }
    }
}