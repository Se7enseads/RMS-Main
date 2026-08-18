<?php

namespace App\Controllers;

use App\Core\Redirect;
use App\Core\Session;
use App\Core\View;
use App\Services\IngredientService;

class StoreController
{
    private IngredientService $ingredientService;

    public function __construct()
    {
        $this->ingredientService = new IngredientService();
    }

    public function index(): void
    {
        $ingredients = $this->ingredientService->getAllIngredients();
        View::render('store/index', ['ingredients' => $ingredients]);
    }

    public function create(): void
    {
        View::render('store/ingredients/create');
    }

    public function save(): void
    {
        $data = $_POST;
        $result = $this->ingredientService->createIngredient($data);

        if ($result['success']) {
            Redirect::to('/store/inventory');
            return;
        }

        View::render('store/ingredients/create', [
            'errors' => $result['errors'],
            'old' => $data,
        ]);
    }

    public function edit(int $id): void
    {
        $ingredient = $this->ingredientService->getIngredientById($id);
        if (!$ingredient) {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        View::render('store/ingredients/edit', ['ingredient' => $ingredient]);
    }

    public function update(int $id): void
    {
        $data = $_POST;
        $result = $this->ingredientService->updateIngredient($id, $data);

        if ($result['success']) {
            Redirect::to('/store/inventory');
            return;
        }

        $ingredient = $this->ingredientService->getIngredientById($id);
        View::render('store/ingredients/edit', [
            'ingredient' => $ingredient,
            'errors' => $result['errors'],
            'old' => $data,
        ]);
    }

    public function deactivate(int $id): void
    {
        $ingredient = $this->ingredientService->getIngredientById($id);
        if ($ingredient) {
            $this->ingredientService->setIngredientActive($id, !$ingredient->active);
        }
        Redirect::to('/store/inventory');
    }

    public function stockForm(int $id): void
    {
        $ingredient = $this->ingredientService->getIngredientById($id);
        if (!$ingredient) {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        View::render('store/ingredients/stock', ['ingredient' => $ingredient]);
    }

    public function stockSave(int $id): void
    {
        $ingredient = $this->ingredientService->getIngredientById($id);
        if (!$ingredient) {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        $userId = (int) Session::get('user_id');
        $result = $this->ingredientService->addStock($id, $_POST, $userId);

        if ($result['success']) {
            Redirect::to('/store/inventory');
            return;
        }

        View::render('store/ingredients/stock', [
            'ingredient' => $ingredient,
            'errors' => $result['errors'],
            'old' => $_POST,
        ]);
    }
}