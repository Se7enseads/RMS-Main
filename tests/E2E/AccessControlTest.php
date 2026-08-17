<?php

namespace Tests\E2E;

class AccessControlTest extends BrowserTestCase
{
    public function testAnonymousUserIsRedirectedFromAdminPages(): void
    {
        $this->page->goto($this->baseUrl() . '/items');
        $this->expect($this->page)->toHaveURL($this->baseUrl() . '/login');
    }

    public function testWaiterCannotAccessUsersPage(): void
    {
        $this->loginWithPin('1234');
        $this->page->goto($this->baseUrl() . '/kitchen');

        $this->expect($this->page->locator('h1'))->toContainText('403');
        $this->expect($this->page)->not()->toHaveURL($this->baseUrl() . '/users');
    }

    public function testChefCannotAccessUsersPage(): void
    {
        $this->loginWithPin('5678');
        $this->page->goto($this->baseUrl() . '/users');

        $this->expect($this->page->locator('h1'))->toContainText('403');
    }

    public function testWaiterCannotAccessKitchen(): void
    {
        $this->loginWithPin('1234');
        $this->page->goto($this->baseUrl() . '/kitchen');

        $this->expect($this->page->locator('h1'))->toContainText('403');
    }

    public function testManagerCanAccessUsersPage(): void
    {
        $this->loginWithPassword('MGR001', 'manager123');
        $this->page->goto($this->baseUrl() . '/users');

        $this->expect($this->page->locator('h1'))->toContainText('User List');
    }
}