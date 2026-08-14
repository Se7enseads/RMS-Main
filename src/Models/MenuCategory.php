<?php

namespace App\Models;

class MenuCategory
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?int $parentId = null,
        public readonly bool $active = true,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            name: $row['name'],
            parentId: isset($row['parent_id']) ? (int) $row['parent_id'] : null,
            active: (bool) ($row['active'] ?? true),
        );
    }
}
