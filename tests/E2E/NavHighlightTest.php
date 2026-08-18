<?php

namespace Tests\E2E;

class NavHighlightTest extends BrowserTestCase
{
    public function testSidebarHighlightsCurrentPageAcrossAdminAndKiosk(): void
    {
        $this->loginWithPassword('MGR001', 'manager123');

        $link = 'nav.sidebar-nav a.sidebar-link';

        // admin dashboard
        $this->page->goto($this->baseUrl() . '/admin');
        $this->expect($this->page->locator($link . '[href="/admin"]'))->toHaveClass('sidebar-link active');
        $this->expect($this->page->locator($link . '[href="/store"]'))->toHaveClass('sidebar-link');

        // child page must highlight its own section, not the dashboard
        $this->page->goto($this->baseUrl() . '/admin/items/create');
        $this->expect($this->page->locator($link . '[href="/admin/items"]'))->toHaveClass('sidebar-link active');
        $this->expect($this->page->locator($link . '[href="/admin"]'))->toHaveClass('sidebar-link');

        // store section: inventory highlighted, not the store dashboard
        $this->page->goto($this->baseUrl() . '/store/inventory');
        $this->expect($this->page->locator($link . '[href="/store/inventory"]'))->toHaveClass('sidebar-link active');
        $this->expect($this->page->locator($link . '[href="/store"]'))->toHaveClass('sidebar-link');

        $this->page->goto($this->baseUrl() . '/store/inventory/create');
        $this->expect($this->page->locator($link . '[href="/store/inventory"]'))->toHaveClass('sidebar-link active');

        $this->page->goto($this->baseUrl() . '/admin/roles/edit/1');
        $this->expect($this->page->locator($link . '[href="/admin/roles"]'))->toHaveClass('sidebar-link active');

        // kiosk highlight
        $this->page->goto($this->baseUrl() . '/kiosk/order');
        $this->expect($this->page->locator($link . '[href="/kiosk/order"]'))->toHaveClass('sidebar-link active');
        $this->expect($this->page->locator($link . '[href="/kiosk"]'))->toHaveClass('sidebar-link');
    }
}