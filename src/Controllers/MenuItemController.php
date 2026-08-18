<?php

namespace App\Controllers;

use App\Core\Redirect;
use App\Core\View;
use App\Services\IngredientService;
use App\Services\MenuService;

class MenuItemController
{
    private MenuService $menuService;
    private IngredientService $ingredientService;

    public function __construct()
    {
        $this->menuService = new MenuService();
        $this->ingredientService = new IngredientService();
    }

    public function index(): void
    {
        $items = $this->menuService->getAllActiveItems();
        View::render('items/index', [
            'items' => $items,
            'recipeCosts' => $this->menuService->getRecipeCosts(),
        ]);
    }

    public function create(): void
    {
        $categories = $this->menuService->getAllCategories();
        $ingredients = $this->ingredientService->getAllActiveIngredients();
        View::render('items/create', [
            'categories' => $categories,
            'ingredients' => $ingredients,
        ]);
    }

    public function save(): void
    {
        $data = $_POST;
        $result = $this->menuService->createItem($data);

        if ($result['success']) {
            $this->menuService->setRecipe($result['item']->id, $this->parseRecipeRows($data));
            Redirect::to('/admin/items');
            return;
        }

        $categories = $this->menuService->getAllCategories();
        $ingredients = $this->ingredientService->getAllActiveIngredients();
        View::render('items/create', [
            'categories' => $categories,
            'ingredients' => $ingredients,
            'errors' => $result['errors'],
            'old' => $data,
        ]);
    }

    public function edit(int $id): void
    {
        $item = $this->menuService->getItemById($id);
        if (!$item) {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        $categories = $this->menuService->getAllCategories();
        $ingredients = $this->ingredientService->getAllActiveIngredients();
        View::render('items/edit', [
            'item' => $item,
            'categories' => $categories,
            'ingredients' => $ingredients,
            'recipe' => $this->menuService->getRecipe($id),
            'recipeCost' => $this->menuService->getRecipeCost($id),
        ]);
    }

    public function update(int $id): void
    {
        $data = $_POST;
        $result = $this->menuService->updateItem($id, $data);

        if ($result['success']) {
            $this->menuService->setRecipe($id, $this->parseRecipeRows($data));
            Redirect::to('/admin/items');
            return;
        }

        $item = $this->menuService->getItemById($id);
        $categories = $this->menuService->getAllCategories();
        $ingredients = $this->ingredientService->getAllActiveIngredients();
        View::render('items/edit', [
            'item' => $item,
            'categories' => $categories,
            'ingredients' => $ingredients,
            'recipe' => $this->menuService->getRecipe($id),
            'recipeCost' => $this->menuService->getRecipeCost($id),
            'errors' => $result['errors'],
            'old' => $data,
        ]);
    }

    public function deactivate(int $id): void
    {
        $item = $this->menuService->getItemById($id);
        if ($item) {
            $this->menuService->setItemActive($id, !$item->active);
        }
        Redirect::to('/admin/items');
    }

    /**
     * Extract recipe rows from POST: ingredient_id[] checkboxes and a
     * quantity[<ingredient_id>] map. Only rows with a positive quantity count.
     *
     * @param array<string,mixed> $data
     * @return array<int, array{ingredient_id: int, quantity: float}>
     */
    private function parseRecipeRows(array $data): array
    {
        $rows = [];
        $quantities = $data['quantity'] ?? [];

        foreach ((array) ($data['ingredient_id'] ?? []) as $ingredientId) {
            $quantity = (float) ($quantities[$ingredientId] ?? 0);
            if ($quantity > 0) {
                $rows[] = [
                    'ingredient_id' => (int) $ingredientId,
                    'quantity' => $quantity,
                ];
            }
        }

        return $rows;
    }
}