<?php

namespace App\Services;

use App\Models\Ingredient;
use App\Repositories\IngredientRepository;
use Throwable;

class IngredientService
{
    private IngredientRepository $ingredientRepository;

    public function __construct()
    {
        $this->ingredientRepository = new IngredientRepository();
    }

    /**
     * @return array<int, Ingredient>
     */
    public function getAllActiveIngredients(): array
    {
        return $this->ingredientRepository->findAllActive();
    }

    /**
     * @return array<int, Ingredient>
     */
    public function getAllIngredients(): array
    {
        return $this->ingredientRepository->findAll();
    }

    public function getIngredientById(int $id): ?Ingredient
    {
        return $this->ingredientRepository->findById($id);
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function createIngredient(array $data): array
    {
        $errors = $this->validateIngredientData($data);

        if ($errors) {
            return ['success' => false, 'errors' => $errors];
        }

        if ($this->ingredientRepository->findByName($data['name'])) {
            return ['success' => false, 'errors' => ['name' => 'An ingredient with this name already exists.']];
        }

        $ingredient = $this->ingredientRepository->insert($data);
        return ['success' => true, 'ingredient' => $ingredient];
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function updateIngredient(int $id, array $data): array
    {
        $errors = $this->validateIngredientData($data);

        if ($errors) {
            return ['success' => false, 'errors' => $errors];
        }

        $existing = $this->ingredientRepository->findByName($data['name']);
        if ($existing && $existing->id !== $id) {
            return ['success' => false, 'errors' => ['name' => 'An ingredient with this name already exists.']];
        }

        $this->ingredientRepository->update($id, $data);
        return ['success' => true, 'ingredient' => $this->ingredientRepository->findById($id)];
    }

    public function setIngredientActive(int $id, bool $active): void
    {
        $this->ingredientRepository->setActive($id, $active);
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function addStock(int $ingredientId, array $data, int $performedBy): array
    {
        $ingredient = $this->ingredientRepository->findById($ingredientId);
        if (!$ingredient) {
            return ['success' => false, 'errors' => ['ingredient' => 'Ingredient not found.']];
        }

        $errors = [];

        $quantity = $data['quantity'] ?? null;
        if (!is_numeric($quantity) || (float) $quantity <= 0) {
            $errors['quantity'] = 'Quantity must be a positive number.';
        }

        $unit = $data['unit'] ?? $ingredient->receiveUnit;
        if (!in_array($unit, Ingredient::RECEIVE_UNITS, true)) {
            $errors['unit'] = 'Invalid unit.';
        } elseif ($unit !== $ingredient->receiveUnit) {
            $errors['unit'] = 'Stock must be received in ' . $ingredient->receiveUnit . '.';
        }

        $unitCost = $data['unit_cost'] ?? null;
        if (!is_numeric($unitCost) || (float) $unitCost < 0) {
            $errors['unit_cost'] = 'Unit cost must be zero or a positive number.';
        }

        if ($errors) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            $this->ingredientRepository->addStock(
                $ingredientId,
                (float) $quantity,
                $unit,
                (float) $unitCost,
                $performedBy
            );
        } catch (Throwable $e) {
            return ['success' => false, 'errors' => ['ingredient' => 'Failed to record stock entry.']];
        }

        return ['success' => true, 'ingredient' => $this->ingredientRepository->findById($ingredientId)];
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,string>
     */
    private function validateIngredientData(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'Ingredient name is required.';
        }

        if (!in_array($data['base_unit'] ?? null, Ingredient::BASE_UNITS, true)) {
            $errors['base_unit'] = 'Select a valid base unit.';
        }

        if (!in_array($data['receive_unit'] ?? null, Ingredient::RECEIVE_UNITS, true)) {
            $errors['receive_unit'] = 'Select a valid receive unit.';
        }

        $isContainer = ($data['receive_unit'] ?? null) !== ($data['base_unit'] ?? null);
        if ($isContainer && (empty($data['units_per_container']) || (float) $data['units_per_container'] <= 0)) {
            $errors['units_per_container'] = 'Pieces per container is required for container units.';
        }

        if (isset($data['reorder_level']) && $data['reorder_level'] !== '' && (!is_numeric($data['reorder_level']) || (float) $data['reorder_level'] < 0)) {
            $errors['reorder_level'] = 'Reorder level must be zero or a positive number.';
        }

        return $errors;
    }
}