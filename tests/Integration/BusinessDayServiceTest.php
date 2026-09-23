<?php

namespace Tests\Integration;

use App\Core\Database;
use App\Repositories\BusinessDayRepository;
use App\Repositories\OrderRepository;
use App\Services\BusinessDayService;

class BusinessDayServiceTest extends DatabaseTestCase
{
    private function service(): BusinessDayService
    {
        return new BusinessDayService(new BusinessDayRepository(), new OrderRepository());
    }

    public function testCurrentOpenDayCreatesTodayWhenMissing(): void
    {
        Database::getConnection()->exec('DELETE FROM business_days');

        $day = $this->service()->currentOpenDay();

        $this->assertSame(date('Y-m-d'), $day['date']);
        $this->assertSame('0', (string) $day['is_closed']);
    }

    public function testSummaryForDateReflectsOrdersAndPayments(): void
    {
        $order = (new OrderRepository())->insertWithItems([
            'order_number' => 'BD-TEST-1',
            'status' => 'PLACED',
            'type' => 'DINE_IN',
            'user_id' => 1,
            'table_id' => 1,
            'total_amount' => 250.00,
        ], [
            ['menu_item_id' => 1, 'price_at_time' => 250.00, 'quantity' => 1],
        ]);

        $summary = $this->service()->summaryForDate(date('Y-m-d'));

        $this->assertSame(1, $summary['order_count']);
        $this->assertSame(1, $summary['item_count']);
        $this->assertSame(250.0, $summary['gross_total']);
        $this->assertSame(0.0, $summary['paid_total']);
        $this->assertSame(250.0, $summary['unpaid_total']);

        (new OrderRepository())->recordPayment($order->id, 'MOBILE', 250.00, 5, 'TXNTEST');

        $summary = $this->service()->summaryForDate(date('Y-m-d'));

        $this->assertSame(250.0, $summary['paid_total']);
        $this->assertSame(0.0, $summary['unpaid_total']);
        $this->assertSame(250.0, $summary['mobile_total']);
        $this->assertSame(0.0, $summary['cash_total']);
    }

    public function testCloseClosesOpenDayAndOpensNext(): void
    {
        $service = $this->service();
        $day = $service->currentOpenDay();
        $this->assertSame(date('Y-m-d'), $day['date']);

        $result = $service->close(1);

        $this->assertSame('1', (string) $result['closed']['is_closed']);
        $this->assertSame(date('Y-m-d', strtotime('+1 day')), $result['opened']['date']);
        $this->assertSame('0', (string) $result['opened']['is_closed']);

        $db = Database::getConnection();
        $closed = $db->query("SELECT closed_by FROM business_days WHERE date = '" . $day['date'] . "'")->fetchColumn();
        $this->assertSame('1', (string) $closed);
    }
}