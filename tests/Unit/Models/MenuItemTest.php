<?php

namespace Tests\Unit\Models;

use App\Models\MenuItem;
use PHPUnit\Framework\TestCase;

class MenuItemTest extends TestCase
{
    public function testFromRowMapsAllFields(): void
    {
        $item = MenuItem::fromRow([
            'id' => 7,
            'name' => 'Beef Stew',
            'description' => 'Tender beef',
            'price' => '450.00',
            'category_id' => 1,
            'is_combo' => 0,
            'active' => 1,
            'category_name' => 'Mains',
        ]);

        $this->assertSame(7, $item->id);
        $this->assertSame('Beef Stew', $item->name);
        $this->assertSame(450.00, $item->price);
        $this->assertSame(1, $item->categoryId);
        $this->assertSame('Mains', $item->categoryName);
    }

    public function testFromRowDefaults(): void
    {
        $item = MenuItem::fromRow([
            'id' => 1,
            'name' => 'Soda',
            'description' => null,
            'price' => '100.00',
            'category_id' => null,
        ]);

        $this->assertNull($item->categoryId);
        $this->assertTrue($item->active);
        $this->assertFalse($item->isCombo);
        $this->assertNull($item->categoryName);
    }
}