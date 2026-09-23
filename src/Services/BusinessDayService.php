<?php

namespace App\Services;

use App\Repositories\BusinessDayRepository;
use App\Repositories\OrderRepository;

class BusinessDayService
{
    public function __construct(
        private readonly BusinessDayRepository $businessDayRepository,
        private readonly OrderRepository $orderRepository,
    ) {
    }

    /**
     * The current open business day. If none exists (e.g. after first setup),
     * today is created and returned.
     *
     * @return array<string, mixed>
     */
    public function currentOpenDay(): array
    {
        $day = $this->businessDayRepository->currentOpenDay();
        if ($day !== null) {
            return $day;
        }

        $this->businessDayRepository->createForDate(date('Y-m-d'));

        return $this->businessDayRepository->currentOpenDay();
    }

    /**
     * Z-report style summary for a single business day (date).
     *
     * @return array{
     *     date: string, order_count: int, item_count: int, gross_total: float,
     *     paid_total: float, unpaid_total: float, cash_total: float,
     *     card_total: float, mobile_total: float, methods: array<int, array{method: string, count: int, total: float}>
     * }
     */
    public function summaryForDate(string $date): array
    {
        $sales = $this->orderRepository->salesByDay($date, $date);
        $row = $sales[0] ?? ['orders' => 0, 'items' => 0, 'revenue' => 0.0, 'paid' => 0.0, 'unpaid' => 0.0];

        $methods = $this->orderRepository->paymentMethodSummary($date, $date);
        $totals = [
            'CASH' => 0.0,
            'CARD' => 0.0,
            'MOBILE' => 0.0,
        ];
        foreach ($methods as $method) {
            if (isset($totals[$method['method']])) {
                $totals[$method['method']] = (float) $method['total'];
            }
        }

        return [
            'date' => $date,
            'order_count' => (int) $row['orders'],
            'item_count' => (int) $row['items'],
            'gross_total' => (float) $row['revenue'],
            'paid_total' => (float) $row['paid'],
            'unpaid_total' => (float) $row['unpaid'],
            'cash_total' => $totals['CASH'],
            'card_total' => $totals['CARD'],
            'mobile_total' => $totals['MOBILE'],
            'methods' => $methods,
        ];
    }

    /**
     * Close the current open day (snapshoting its summary) and open the next day.
     *
     * @return array{closed: array<string, mixed>, opened: array<string, mixed>, summary: array<string, mixed>}
     */
    public function close(int $userId): array
    {
        $day = $this->currentOpenDay();
        $summary = $this->summaryForDate($day['date']);

        $this->businessDayRepository->closeDay((int) $day['id'], $userId, $summary);

        $next = (new \DateTimeImmutable($day['date']))->modify('+1 day')->format('Y-m-d');
        $this->businessDayRepository->createForDate($next);

        $day['is_closed'] = 1;

        return [
            'closed' => $day,
            'opened' => $this->currentOpenDay(),
            'summary' => $summary,
        ];
    }
}