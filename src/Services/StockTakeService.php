<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Repositories\IngredientRepository;
use App\Repositories\StockTakeRepository;
use Throwable;

class StockTakeService
{
    public const SCOPES = ['ALL', 'BAR'];

    private StockTakeRepository $stockTakeRepository;
    private IngredientRepository $ingredientRepository;

    public function __construct()
    {
        $this->stockTakeRepository = new StockTakeRepository();
        $this->ingredientRepository = new IngredientRepository();
    }

    /**
     * @return array<int, Ingredient>
     */
    public function getStockTakeList(?string $scope = 'ALL'): array
    {
        return $this->ingredientRepository->getStockTakeList($scope);
    }

    /**
     * @return array<int, float> inventory_id => last variance (base units)
     */
    public function getLastVarianceMap(): array
    {
        return $this->ingredientRepository->getLastVarianceMap();
    }

    /**
     * @return array<int, array<string,mixed>>
     */
    public function getTakes(?string $scope = null): array
    {
        return $this->stockTakeRepository->findTakes($scope);
    }

    /**
     * @return array<int, array<string,mixed>>
     */
    public function getTakeItems(int $takeId): array
    {
        return $this->stockTakeRepository->findTakeItems($takeId);
    }

    /**
     * @param array<string,mixed> $counts inventory_id (receive-unit qty) => value
     * @return array<string,mixed>
     */
    public function performTake(string $scope, string $takeDate, array $counts, int $performedBy): array
    {
        if (!in_array($scope, self::SCOPES, true)) {
            return ['success' => false, 'errors' => ['scope' => 'Invalid stock take scope.']];
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $takeDate) || strtotime($takeDate) === false) {
            return ['success' => false, 'errors' => ['take_date' => 'Invalid stock take date.']];
        }

        $allowed = $this->ingredientRepository->getStockTakeList($scope);
        $allowedById = [];
        foreach ($allowed as $ingredient) {
            $allowedById[$ingredient->id] = $ingredient;
        }

        $counts = array_filter($counts, fn ($value) => $value !== '' && $value !== null);

        if (!$counts) {
            return ['success' => false, 'errors' => ['counts' => 'Enter at least one physical count.']];
        }

        $errors = [];
        $items = [];

        foreach ($counts as $rawId => $rawQuantity) {
            $ingredientId = (int) $rawId;

            if (!isset($allowedById[$ingredientId])) {
                $errors['counts'] = 'Invalid ingredient in stock take.';
                break;
            }

            if (!is_numeric($rawQuantity) || (float) $rawQuantity < 0) {
                $errors['counts'] = 'Counts must be zero or a positive number.';
                break;
            }

            $ingredient = $allowedById[$ingredientId];
            $toBase = $ingredient->isContainerUnit() ? ($ingredient->unitsPerContainer ?? 1.0) : 1.0;
            $countedBase = round((float) $rawQuantity * $toBase, 3);
            $system = $ingredient->stock;
            $variance = round($countedBase - $system, 3);

            $items[] = [
                'inventory_id' => $ingredientId,
                'system_qty' => $system,
                'counted_qty' => $countedBase,
                'variance_qty' => $variance,
                'unit_cost' => $ingredient->costPerUnit,
                'variance_value' => round($variance * $ingredient->costPerUnit, 2),
                'base_unit' => $ingredient->baseUnit,
            ];
        }

        if ($errors) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $takeId = $this->stockTakeRepository->createTake($scope, $takeDate, $performedBy, $items);

            return ['success' => true, 'take_id' => $takeId];
        } catch (Throwable $e) {
            error_log('STOCKTAKE: ' . $e->getMessage());
            return ['success' => false, 'errors' => ['counts' => 'Failed to save stock take.']];
        }
    }
}
