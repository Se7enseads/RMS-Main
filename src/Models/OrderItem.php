<?php

namespace App\Models;

class OrderItem
{
    public function __construct(
        public readonly int $id,
        public readonly int $orderId,
        public readonly int $menuItemId,
        public readonly float $priceAtTime,
        public readonly int $quantity,
        public readonly ?string $menuItemName = null,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            orderId: (int) $row['order_id'],
            menuItemId: (int) $row['menu_item_id'],
            priceAtTime: (float) $row['price_at_time'],
            quantity: (int) $row['quantity'],
            menuItemName: $row['menu_item_name'] ?? null,
        );
    }
}
