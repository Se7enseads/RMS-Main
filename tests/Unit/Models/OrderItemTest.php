<?php

namespace Tests\Unit\Models;

use App\Models\OrderItem;
use PHPUnit\Framework\TestCase;

class OrderItemTest extends TestCase
{
    public function testFromRowMapsAllFields(): void
    {
        $item = OrderItem::fromRow([
            'id' => 9,
            'order_id' => 5,
            'menu_item_id' => 3,
            'price_at_time' => '250.00',
            'quantity' => 2,
            'menu_item_name' => 'Chicken Soup',
        ]);

        $this->assertSame(9, $item->id);
        $this->assertSame(5, $item->orderId);
        $this->assertSame(3, $item->menuItemId);
        $this->assertSame(250.00, $item->priceAtTime);
        $this->assertSame(2, $item->quantity);
        $this->assertSame('Chicken Soup', $item->menuItemName);
    }

    public function testFromRowWithoutJoinName(): void
    {
        $item = OrderItem::fromRow([
            'id' => 1,
            'order_id' => 1,
            'menu_item_id' => 1,
            'price_at_time' => '100.00',
            'quantity' => 1,
        ]);

        $this->assertNull($item->menuItemName);
    }
}