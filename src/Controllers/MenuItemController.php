<?php

namespace App\Controllers;

use App\Core\View;
use App\Services\MenuService;

class MenuItemController
{
    private MenuService $menuService;

    public function __construct()
    {
        $this->menuService = new MenuService();
    }

    public function index(): void
    {
        $items = $this->menuService->getAllActiveItems();
        View::render('items/index', ['items' => $items]);
    }

    public function create(): void
    {
        $categories = $this->menuService->getAllCategories();
        View::render('items/create', ['categories' => $categories]);
    }

    public function save(): void
    {
        $data = $_POST;
        $result = $this->menuService->createItem($data);

        if ($result['success']) {
            header('Location: /items');
            return;
        }

        $categories = $this->menuService->getAllCategories();
        View::render('items/create', [
            'categories' => $categories,
            'errors' => $result['errors'],
            'old' => $data,
        ]);
    }
}
