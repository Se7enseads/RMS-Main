<?php

namespace App\Controllers;

use App\Core\Logger;
use App\Core\Redirect;
use App\Core\Session;
use App\Core\View;
use App\Models\Action;
use App\Repositories\BusinessDayRepository;
use App\Repositories\OrderRepository;
use App\Services\BusinessDayService;

class CashierController
{
    private const METHODS = ['CASH', 'CARD', 'MOBILE'];

    private OrderRepository $orderRepository;
    private BusinessDayService $businessDayService;

    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->businessDayService = new BusinessDayService(new BusinessDayRepository(), $this->orderRepository);
    }

    public function index(): void
    {
        $day = $this->businessDayService->currentOpenDay();
        $orders = $this->orderRepository->findUnpaidByDate($day['date']);
        $paidOrders = $this->orderRepository->findPaidByDate($day['date']);

        $errors = Session::get('errors') ?? [];
        Session::remove('errors');

        View::render('cashier/index', [
            'day' => $day,
            'orders' => $orders,
            'paidOrders' => $paidOrders,
            'methods' => self::METHODS,
            'errors' => $errors,
        ]);
    }

    public function pay(int $id): void
    {
        $order = $this->orderRepository->findById($id);
        $userId = (int) Session::get('user_id');
        $method = (string) ($_POST['method'] ?? '');
        $amount = (float) ($_POST['amount'] ?? 0);
        $transactionCode = trim((string) ($_POST['transaction_code'] ?? ''));

        if ($order === null) {
            Session::set('errors', ['Order not found.']);
            Redirect::to('/cashier');
            return;
        }

        if ($order->isCancelled()) {
            Session::set('errors', ['A cancelled order cannot be settled.']);
            Redirect::to('/cashier');
            return;
        }

        if ($order->isPaid) {
            Session::set('errors', ['This order is already paid.']);
            Redirect::to('/cashier');
            return;
        }

        if (!in_array($method, self::METHODS, true) || $amount <= 0) {
            Session::set('errors', ['Choose a valid payment method and enter an amount greater than zero.']);
            Redirect::to('/cashier');
            return;
        }

        $this->orderRepository->recordPayment(
            $order->id,
            $method,
            round($amount, 2),
            $userId,
            $transactionCode !== '' ? $transactionCode : null,
        );

        Logger::add($userId, Action::fromRequest('Payment received for ' . $order->orderNumber . ' (' . $method . ' KES ' . number_format($amount, 2) . ')'));
        Redirect::to('/cashier');
    }
}