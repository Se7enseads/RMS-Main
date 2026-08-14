<?php

namespace App\Models;

class Order
{
    public function __construct(
        public readonly int $id,
        public readonly string $orderNumber,
        public readonly string $status,
        public readonly string $type,
        public readonly int $userId,
        public readonly int $tableId,
        public readonly float $totalAmount,
        public readonly ?string $closedAt = null,
        public readonly ?string $createdAt = null,
        public readonly ?int $tableNumber = null,
        public readonly ?string $userName = null,
        public readonly bool $isPaid = false,
        public readonly ?string $paymentMethod = null,
        public readonly ?int $itemCount = null,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            orderNumber: $row['order_number'],
            status: $row['status'],
            type: $row['type'],
            userId: (int) $row['user_id'],
            tableId: (int) $row['table_id'],
            totalAmount: (float) $row['total_amount'],
            closedAt: $row['closed_at'] ?? null,
            createdAt: $row['created_at'] ?? null,
            tableNumber: isset($row['table_number']) ? (int) $row['table_number'] : null,
            userName: $row['user_name'] ?? null,
            isPaid: (bool) ($row['is_paid'] ?? false),
            paymentMethod: $row['payment_method'] ?? null,
            itemCount: isset($row['item_count']) ? (int) $row['item_count'] : null,
        );
    }

    public function isCancelled(): bool
    {
        return $this->status === 'CANCELLED';
    }
}
