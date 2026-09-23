<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class BusinessDayRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * The earliest open (unclosed) business day.
     *
     * @return array<string, mixed>|null
     */
    public function currentOpenDay(): ?array
    {
        $stmt = $this->db->query('SELECT * FROM business_days WHERE is_closed = 0 ORDER BY date ASC LIMIT 1');
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function createForDate(string $date): int
    {
        $stmt = $this->db->prepare('INSERT INTO business_days (date, is_closed) VALUES (?, 0)');
        $stmt->execute([$date]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * @param array<string, int|float> $summary
     */
    public function closeDay(int $id, int $closedBy, array $summary): void
    {
        $stmt = $this->db->prepare('
            UPDATE business_days
            SET is_closed = 1, closed_at = NOW(), closed_by = :closed_by,
                order_count = :order_count, item_count = :item_count,
                gross_total = :gross_total, paid_total = :paid_total,
                unpaid_total = :unpaid_total, cash_total = :cash_total,
                card_total = :card_total, mobile_total = :mobile_total
            WHERE id = :id
        ');
        $stmt->execute([
            'closed_by' => $closedBy,
            'order_count' => $summary['order_count'],
            'item_count' => $summary['item_count'],
            'gross_total' => $summary['gross_total'],
            'paid_total' => $summary['paid_total'],
            'unpaid_total' => $summary['unpaid_total'],
            'cash_total' => $summary['cash_total'],
            'card_total' => $summary['card_total'],
            'mobile_total' => $summary['mobile_total'],
            'id' => $id,
        ]);
    }
}