<?php

namespace App\Models;

class Payment
{
    public function __construct(
        public readonly int $id,
        public readonly string $method,
        public readonly float $amount,
        public readonly int $orderId,
        public readonly int $cashierId,
        public readonly ?string $transactionCode = null,
        public readonly ?string $createdAt = null,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            method: $row['method'],
            amount: (float) $row['amount'],
            orderId: (int) $row['order_id'],
            cashierId: (int) $row['cashier_id'],
            transactionCode: $row['transaction_code'] ?? null,
            createdAt: $row['created_at'] ?? null,
        );
    }
}
