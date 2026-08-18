<?php

namespace App\Services;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Repositories\MenuRepository;

class MenuService
{
    private MenuRepository $menuRepository;

    public function __construct()
    {
        $this->menuRepository = new MenuRepository();
    }

    /**
     * @return array<int, array{category: MenuCategory, items: MenuItem[]}>
     */
    public function getMenuGroupedByCategory(?string $station = null): array
    {
        $categories = $this->menuRepository->findAllActiveCategories($station);
        $items = $this->menuRepository->findAllActiveItems($station);

        $itemsByCategory = [];
        foreach ($items as $item) {
            $itemsByCategory[$item->categoryId][] = $item;
        }

        $grouped = [];
        foreach ($categories as $category) {
            $grouped[] = [
                'category' => $category,
                'items' => $itemsByCategory[$category->id] ?? [],
            ];
        }

        return $grouped;
    }

    public function getAllActiveItems(?string $station = null): array
    {
        return $this->menuRepository->findAllActiveItems($station);
    }

    public function getAllCategories(): array
    {
        return $this->menuRepository->findAllCategories();
    }

    public function getItemById(int $id): ?MenuItem
    {
        return $this->menuRepository->findItemById($id);
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function createItem(array $data): array
    {
        $errors = $this->validateItemData($data);

        if ($errors) {
            return ['success' => false, 'errors' => $errors];
        }

        $item = $this->menuRepository->insertItem($data);
        return ['success' => true, 'item' => $item];
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function updateItem(int $id, array $data): array
    {
        $errors = $this->validateItemData($data);

        if ($errors) {
            return ['success' => false, 'errors' => $errors];
        }

        $item = $this->menuRepository->updateItem($id, $data);
        return ['success' => true, 'item' => $item];
    }

    public function setItemActive(int $id, bool $active): void
    {
        $this->menuRepository->setItemActive($id, $active);
    }

    public function getCategoryById(int $id): ?MenuCategory
    {
        return $this->menuRepository->findCategoryById($id);
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function createCategory(array $data): array
    {
        $errors = $this->validateCategoryData($data);

        if ($errors) {
            return ['success' => false, 'errors' => $errors];
        }

        $category = $this->menuRepository->insertCategory($data);
        return ['success' => true, 'category' => $category];
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function updateCategory(int $id, array $data): array
    {
        $errors = $this->validateCategoryData($data);

        if ($errors) {
            return ['success' => false, 'errors' => $errors];
        }

        $this->menuRepository->updateCategory($id, $data);
        return ['success' => true, 'category' => $this->menuRepository->findCategoryById($id)];
    }

    public function setCategoryActive(int $id, bool $active): void
    {
        $this->menuRepository->setCategoryActive($id, $active);
    }

    /**
     * @return array<int, array{ingredient_id: int, name: string, base_unit: string, quantity: float, unit: string, cost_per_unit: float}>
     */
    public function getRecipe(int $menuItemId): array
    {
        return $this->menuRepository->findRecipeByItemId($menuItemId);
    }

    /**
     * @param array<int, array{ingredient_id: int, quantity: float}> $rows
     */
    public function setRecipe(int $menuItemId, array $rows): void
    {
        $this->menuRepository->setRecipe($menuItemId, $rows);
    }

    /**
     * @return array<int, float>
     */
    public function getRecipeCosts(): array
    {
        return $this->menuRepository->findRecipeCosts();
    }

    public function getRecipeCost(int $menuItemId): float
    {
        $costs = $this->menuRepository->findRecipeCosts();
        return $costs[$menuItemId] ?? 0.0;
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,string>
     */
    private function validateCategoryData(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'Category name is required.';
        }

        if (!in_array($data['station'] ?? null, ['KITCHEN', 'BAR'], true)) {
            $errors['station'] = 'Select a valid station.';
        }

        return $errors;
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,string>
     */
    private function validateItemData(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'Item name is required.';
        }

        if (!isset($data['price']) || !is_numeric($data['price']) || (float) $data['price'] <= 0) {
            $errors['price'] = 'Price must be a positive number.';
        }

        if (!empty($data['category_id'])) {
            $categoryExists = false;
            foreach ($this->menuRepository->findAllCategories() as $category) {
                if ($category->id === (int) $data['category_id']) {
                    $categoryExists = true;
                    break;
                }
            }
            if (!$categoryExists) {
                $errors['category_id'] = 'Selected category does not exist.';
            }
        }

        return $errors;
    }
}
