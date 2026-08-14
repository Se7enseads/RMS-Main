<?php

namespace App\Models;

class RestaurantTable
{
    public function __construct(
        public readonly int $id,
        public readonly int $number,
        public readonly int $capacity,
        public readonly string $status,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            number: (int) $row['number'],
            capacity: (int) $row['capacity'],
            status: $row['status'],
        );
    }
}
