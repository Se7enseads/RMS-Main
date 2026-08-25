<?php

namespace App\Controllers;

use App\Core\Logger;
use App\Core\Redirect;
use App\Core\Session;
use App\Core\View;
use App\Models\Action;
use App\Repositories\TableRepository;
use App\Services\BarService;
use App\Services\MenuService;
use App\Services\OrderService;

class BarController
{
    private const string STATION = 'BAR';

    private BarService $barService;
    private MenuService $menuService;
    private OrderService $orderService;
    private TableRepository $tableRepository;

    public function __construct()
    {
        $this->barService = new BarService();
        $this->menuService = new MenuService();
        $this->orderService = new OrderService();
        $this->tableRepository = new TableRepository();
    }

    public function index(): void
    {
        View::render('bar/index', $this->barService->getKitchenData());
    }

    public function order(): void
    {
        $menu = $this->menuService->getMenuGroupedByCategory(self::STATION);
        $tables = $this->tableRepository->findAvailable();

        View::render('bar/order', [
            'menu' => $menu,
            'tables' => $tables,
        ]);
    }

    public function placeOrder(): void
    {
        $userId = (int)Session::get('user_id');
        $type = $_POST['order_type'] ?? 'DINE_IN';
        $tableId = (int)($_POST['table_id'] ?? 0);
        $itemsJson = $_POST['items'] ?? '[]';

        $items = json_decode($itemsJson, true);

        if (!is_array($items) || !in_array($type, ['DINE_IN', 'TAKEAWAY', 'DELIVERY'], true)) {
            $this->renderOrderError('Invalid order submission.');
            return;
        }

        $barItemIds = array_map(
            fn($item) => (int)$item->id,
            $this->menuService->getAllActiveItems(self::STATION)
        );

        $validItems = [];
        foreach ($items as $entry) {
            $menuItemId = (int)($entry['menu_item_id'] ?? 0);
            $quantity = (int)($entry['quantity'] ?? 0);
            if (!in_array($menuItemId, $barItemIds, true) || $quantity <= 0) {
                $this->renderOrderError('Order contains items not served at the bar.');
                return;
            }
            $validItems[] = ['menu_item_id' => $menuItemId, 'quantity' => $quantity];
        }

        if (empty($validItems)) {
            $this->renderOrderError('Invalid order submission.');
            return;
        }

        $result = $this->orderService->placeOrder($userId, $type, $tableId, $validItems);

        if ($result['success']) {
            Logger::add($userId, Action::fromRequest('Order placed: ' . $result['order']->orderNumber));
            Redirect::to('/bar');
            return;
        }

        $this->renderOrderError($result['error']);
    }

    public function serve(int $id): void
    {
        $this->barService->markServed($id);
        Logger::add((int)Session::get('user_id'), Action::fromRequest('Bar order item served: ' . $id));
        Redirect::to('/bar');
    }

    private function renderOrderError(string $error): void
    {
        View::render('bar/order', [
            'menu' => $this->menuService->getMenuGroupedByCategory(self::STATION),
            'tables' => $this->tableRepository->findAvailable(),
            'error' => $error,
        ]);
    }
}