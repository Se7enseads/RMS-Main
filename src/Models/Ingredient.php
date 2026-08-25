<?php

namespace App\Models;

class Ingredient
{
    public const array BASE_UNITS = ['g', 'ml', 'pcs', 'bottles'];
    public const array RECEIVE_UNITS = ['g', 'ml', 'pcs', 'case', 'packet', 'carton', 'box', 'crate'];

    public function __construct(
        public readonly int    $id,
        public readonly string $name,
        public readonly string $baseUnit,
        public readonly string $receiveUnit,
        public readonly ?float $unitsPerContainer = null,
        public readonly float  $stock = 0.0,
        public readonly float  $costPerUnit = 0.0,
        public readonly float  $reorderLevel = 0.0,
        public readonly bool   $active = true,
    )
    {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int)$row['id'],
            name: $row['name'],
            baseUnit: $row['base_unit'],
            receiveUnit: $row['receive_unit'],
            unitsPerContainer: isset($row['units_per_container']) ? (float)$row['units_per_container'] : null,
            stock: (float)$row['stock'],
            costPerUnit: (float)$row['cost_per_unit'],
            reorderLevel: (float)$row['reorder_level'],
            active: (bool)($row['active'] ?? true),
        );
    }

    /**
     * Stock expressed in receive units (e.g. 2.5 cases of water).
     */
    public function stockInReceiveUnits(): float
    {
        if ($this->receiveUnit === $this->baseUnit || $this->unitsPerContainer === null || $this->unitsPerContainer === 0.0) {
            return $this->stock;
        }

        return round($this->stock / $this->unitsPerContainer, 3);
    }

    public function isContainerUnit(): bool
    {
        return $this->receiveUnit !== $this->baseUnit;
    }

    public function isLowStock(): bool
    {
        return $this->reorderLevel > 0 && $this->stock <= $this->reorderLevel;
    }
}