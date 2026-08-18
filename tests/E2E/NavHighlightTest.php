<?php

namespace Tests\E2E;

class NavHighlightTest extends BrowserTestCase
{
    public function testSidebarHighlightsCurrentPageAcrossAdminAndKiosk(): void
    {
        $this->loginWithPassword('MGR001', 'manager123');

        $nav = 'nav.sidebar-nav a.sidebar-link';

        // dashboard
        $this->page->goto($this->baseUrl() . '/admin');
        $this->expect($this->page->locator($nav)->filter(['hasText' => 'Dashboard']))->toHaveClass('sidebar-link active');
        $this->expect($this->page->locator($nav)->filter(['hasText' => 'Menu Items']))->toHaveClass('sidebar-link');

        // child page must highlight its own section, not the dashboard
        $this->page->goto($this->baseUrl() . '/admin/items/create');
        $this->expect($this->page->locator($nav)->filter(['hasText' => 'Menu Items']))->toHaveClass('sidebar-link active');
        $this->expect($this->page->locator($nav)->filter(['hasText' => 'Dashboard']))->toHaveClass('sidebar-link');

        $this->page->goto($this->baseUrl() . '/store/ingredients/create');
        $this->expect($this->page->locator($nav)->filter(['hasText' => 'Store']))->toHaveClass('sidebar-link active');

        $this->page->goto($this->baseUrl() . '/admin/roles/edit/1');
        $this->expect($this->page->locator($nav)->filter(['hasText' => 'Roles']))->toHaveClass('sidebar-link active');

        // kiosk highlight
        $this->page->goto($this->baseUrl() . '/kiosk/order');
        $this->expect($this->page->locator($nav)->filter(['hasText' => 'Place Order']))->toHaveClass('sidebar-link active');
        $this->expect($this->page->locator($nav)->filter(['hasText' => 'Dashboard']))->toHaveClass('sidebar-link');
    }
}