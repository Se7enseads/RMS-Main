<?php

namespace App\Controllers;

use App\Core\Redirect;
use App\Core\View;
use App\Services\MenuService;

class CategoryController
{
    private MenuService $menuService;

    public function __construct()
    {
        $this->menuService = new MenuService();
    }

    public function index(): void
    {
        $categories = $this->menuService->getAllCategories();
        View::render('categories/index', ['categories' => $categories]);
    }

    public function create(): void
    {
        View::render('categories/create');
    }

    public function save(): void
    {
        $data = $_POST;
        $result = $this->menuService->createCategory($data);

        if ($result['success']) {
            Redirect::to('/admin/categories');
            return;
        }

        View::render('categories/create', [
            'errors' => $result['errors'],
            'old' => $data,
        ]);
    }

    public function edit(int $id): void
    {
        $category = $this->menuService->getCategoryById($id);
        if (!$category) {
            http_response_code(404);
            View::render('errors/404');
            return;
        }

        View::render('categories/edit', ['category' => $category]);
    }

    public function update(int $id): void
    {
        $data = $_POST;
        $result = $this->menuService->updateCategory($id, $data);

        if ($result['success']) {
            Redirect::to('/admin/categories');
            return;
        }

        $category = $this->menuService->getCategoryById($id);
        View::render('categories/edit', [
            'category' => $category,
            'errors' => $result['errors'],
            'old' => $data,
        ]);
    }

    public function deactivate(int $id): void
    {
        $category = $this->menuService->getCategoryById($id);
        if ($category) {
            $this->menuService->setCategoryActive($id, !$category->active);
        }
        Redirect::to('/admin/categories');
    }
}