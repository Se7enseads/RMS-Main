<?php

namespace App\Controllers;

use App\Core\Redirect;
use App\Core\Session;
use App\Core\View;
use App\Services\IngredientService;
use App\Services\StockTakeService;

class StoreController
{
    private IngredientService $ingredientService;
    private StockTakeService $stockTakeService;

    public function __construct()
    {
        $this->ingredientService = new IngredientService();
        $this->stockTakeService = new StockTakeService();
    }

    public function index(): void
    {
        $ingredients = $this->ingredientService->getAllIngredients();
        $active = array_filter($ingredients, fn($i) => $i->active);
        $low = array_filter($active, fn($i) => $i->isLowStock());
        $value = array_sum(array_map(fn($i) => $i->stock * $i->costPerUnit, $active));

        View::render('store/index', [
            'totalIngredients' => count($active),
            'lowStockCount' => count($low),
            'inventoryValue' => $value,
        ]);
    }

    public function inventory(): void
    {
        $ingredients = $this->ingredientService->getAllActiveIngredients();
        View::render('store/inventory', [
            'ingredients' => $ingredients,
            'lastVariance' => $this->stockTakeService->getLastVarianceMap(),
        ]);
    }

    public function create(): void
    {
        View::render('store/ingredients/create');
    }

    public function save(): void
    {
        $data = $_POST;
        $result = $this->ingredientService->createIngredient($data, (int)Session::get('user_id'));

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

        $userId = (int)Session::get('user_id');
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

    public function stockTake(): void
    {
        $this->renderStockTake('ALL');
    }

    public function stockTakeBar(): void
    {
        $this->renderStockTake('BAR');
    }

    private function renderStockTake(string $scope): void
    {
        $ingredients = $this->stockTakeService->getStockTakeList($scope);
        $errors = Session::get('errors') ?? [];
        $old = Session::get('old') ?? [];
        Session::remove('errors');
        Session::remove('old');

        View::render('store/stocktake', [
            'scope' => $scope,
            'ingredients' => $ingredients,
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    public function stockTakeSave(): void
    {
        $scope = $_POST['scope'] ?? 'ALL';
        $takeDate = $_POST['take_date'] ?? date('Y-m-d');
        $counts = $_POST['count'] ?? [];
        $userId = (int)Session::get('user_id');

        $result = $this->stockTakeService->performTake($scope, $takeDate, $counts, $userId);

        if ($result['success']) {
            Redirect::to($scope === 'BAR' ? '/store/variance/bar' : '/store/variance');
            return;
        }

        Session::set('errors', $result['errors']);
        Session::set('old', ['take_date' => $takeDate, 'scope' => $scope, 'count' => $counts]);
        Redirect::to($scope === 'BAR' ? '/store/stocktake/bar' : '/store/stocktake');
    }

    public function variance(): void
    {
        $this->renderVariance('ALL');
    }

    public function varianceBar(): void
    {
        $this->renderVariance('BAR');
    }

    private function renderVariance(string $scope): void
    {
        $takes = $this->stockTakeService->getTakes($scope);
        $takeItems = [];
        foreach ($takes as $take) {
            $takeItems[$take['id']] = $this->stockTakeService->getTakeItems((int)$take['id']);
        }

        View::render('store/variance', [
            'scope' => $scope,
            'takes' => $takes,
            'takeItems' => $takeItems,
        ]);
    }
}