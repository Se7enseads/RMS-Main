<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    public function testFromRowMapsAllFields(): void
    {
        $order = Order::fromRow([
            'id' => 5,
            'order_number' => 'ORD-20260815-ABCD',
            'status' => 'PLACED',
            'type' => 'DINE_IN',
            'user_id' => 2,
            'table_id' => 3,
            'total_amount' => '1250.50',
            'closed_at' => null,
            'created_at' => '2026-08-15 07:00:00',
            'table_number' => 4,
            'user_name' => 'Brian',
            'is_paid' => 0,
            'payment_method' => null,
            'item_count' => 2,
        ]);

        $this->assertSame(5, $order->id);
        $this->assertSame('ORD-20260815-ABCD', $order->orderNumber);
        $this->assertSame('PLACED', $order->status);
        $this->assertSame('DINE_IN', $order->type);
        $this->assertSame(2, $order->userId);
        $this->assertSame(3, $order->tableId);
        $this->assertSame(1250.50, $order->totalAmount);
        $this->assertSame(4, $order->tableNumber);
        $this->assertSame('Brian', $order->userName);
        $this->assertFalse($order->isPaid);
        $this->assertSame(2, $order->itemCount);
    }

    public function testFromRowWithNullTableId(): void
    {
        $order = Order::fromRow([
            'id' => 1,
            'order_number' => 'ORD-20260815-ABCD',
            'status' => 'TAKEAWAY',
            'type' => 'TAKEAWAY',
            'user_id' => 2,
            'table_id' => null,
            'total_amount' => '100.00',
        ]);

        $this->assertNull($order->tableId);
        $this->assertNull($order->tableNumber);
    }

    public function testIsCancelled(): void
    {
        $cancelled = Order::fromRow([
            'id' => 1, 'order_number' => 'X', 'status' => 'CANCELLED',
            'type' => 'TAKEAWAY', 'user_id' => 1, 'table_id' => null, 'total_amount' => '0',
        ]);
        $placed = Order::fromRow([
            'id' => 2, 'order_number' => 'Y', 'status' => 'PLACED',
            'type' => 'TAKEAWAY', 'user_id' => 1, 'table_id' => null, 'total_amount' => '0',
        ]);

        $this->assertTrue($cancelled->isCancelled());
        $this->assertFalse($placed->isCancelled());
    }
}