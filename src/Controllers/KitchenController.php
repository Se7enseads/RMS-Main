<?php

namespace App\Controllers;

use App\Core\Logger;
use App\Core\Redirect;
use App\Core\Session;
use App\Core\View;
use App\Models\Action;
use App\Services\KitchenService;

class KitchenController
{
    private KitchenService $kitchenService;

    public function __construct()
    {
        $this->kitchenService = new KitchenService();
    }

    public function index(): void
    {
        View::render('kitchen/index', $this->kitchenService->getKitchenData());
    }

    public function serve(int $id): void
    {
        $result = $this->kitchenService->markServed($id);
        Logger::add((int)Session::get('user_id'), Action::fromRequest('Kitchen order item served: ' . $id));
        Redirect::to('/kitchen');
    }
}