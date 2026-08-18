<?php

namespace Tests\E2E;

class StoreFlowTest extends BrowserTestCase
{
    public function testManagerCanCreateCategoryIngredientAndAddStock(): void
    {
        $categoryName = 'E2E Grills ' . substr(md5((string) mt_rand()), 0, 6);
        $ingredientName = 'E2E Water ' . substr(md5((string) mt_rand()), 0, 6);

        try {
            $this->loginWithPassword('MGR001', 'manager123');

            // create a category
            $this->page->goto($this->baseUrl() . '/admin/categories');
            $this->page->locator('a[href="/admin/categories/create"]')->click();
            $this->page->locator('input[name="name"]')->fill($categoryName);
            $this->page->locator('form[action="/admin/categories/create"] button[type="submit"]')->click();
            $this->expect($this->page)->toHaveURL($this->baseUrl() . '/admin/categories');
            $this->expect($this->page->locator('.tabulator'))->toContainText($categoryName);

            // create an ingredient received in cases, with opening stock:
            // 2 cases @ 600 = 24 pieces @ 50 each
            $this->page->goto($this->baseUrl() . '/store/inventory');
            $this->page->locator('a[href="/store/inventory/create"]')->click();
            $this->page->locator('input[name="name"]')->fill($ingredientName);
            $this->page->locator('select[name="base_unit"]')->selectOption('pcs');
            $this->page->locator('select[name="receive_unit"]')->selectOption('case');
            $this->page->locator('input[name="units_per_container"]')->fill('12');
            $this->page->locator('input[name="quantity"]')->fill('2');
            $this->page->locator('input[name="unit_cost"]')->fill('600');
            $this->page->locator('form[action="/store/inventory/create"] button[type="submit"]')->click();
            $this->expect($this->page)->toHaveURL($this->baseUrl() . '/store/inventory');
            $this->expect($this->page->locator('.tabulator'))->toContainText($ingredientName);
            $row = $this->page->locator('.tabulator-row')->filter(['hasText' => $ingredientName]);
            $this->expect($row)->toContainText('24 pcs');
            $this->expect($row)->toContainText('KES 50.0000');
        } finally {
            // cleanup
            $db = $this->liveDb();
            $stmt = $db->prepare('DELETE FROM inventory_movements WHERE inventory_id = (SELECT id FROM inventory WHERE name = ?)');
            $stmt->execute([$ingredientName]);
            $stmt = $db->prepare('DELETE FROM inventory WHERE name = ?');
            $stmt->execute([$ingredientName]);
            $stmt = $db->prepare('DELETE FROM menu_categories WHERE name = ?');
            $stmt->execute([$categoryName]);
        }
    }
}