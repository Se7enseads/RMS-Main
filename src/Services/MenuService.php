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
