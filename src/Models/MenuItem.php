<?php

namespace App\Models;

class MenuItem
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly float $price,
        public readonly ?int $categoryId,
        public readonly bool $isCombo = false,
        public readonly bool $active = true,
        public readonly ?string $categoryName = null,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            name: $row['name'],
            description: $row['description'] ?? null,
            price: (float) $row['price'],
            categoryId: isset($row['category_id']) ? (int) $row['category_id'] : null,
            isCombo: (bool) ($row['is_combo'] ?? false),
            active: (bool) ($row['active'] ?? true),
            categoryName: $row['category_name'] ?? null,
        );
    }

    public const VAT_RATE = 0.16;

    public function netAmount(): float
    {
        return round($this->price / (1 + self::VAT_RATE), 2);
    }

    public function vatAmount(): float
    {
        return round($this->price - $this->netAmount(), 2);
    }
}
