<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Payment;
use PDO;

class PaymentRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByOrderId(int $orderId): ?Payment
    {
        $stmt = $this->db->prepare("SELECT * FROM payments WHERE order_id = :order_id LIMIT 1");
        $stmt->execute(['order_id' => $orderId]);
        $row = $stmt->fetch();

        return $row ? Payment::fromRow($row) : null;
    }

    public function insert(array $data): Payment
    {
        $sql = "INSERT INTO payments (transaction_code, method, amount, order_id, cashier_id)
                VALUES (:transaction_code, :method, :amount, :order_id, :cashier_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'transaction_code' => $data['transaction_code'] ?? null,
            'method' => $data['method'],
            'amount' => $data['amount'],
            'order_id' => $data['order_id'],
            'cashier_id' => $data['cashier_id'],
        ]);

        return new Payment(
            id: (int) $this->db->lastInsertId(),
            method: $data['method'],
            amount: (float) $data['amount'],
            orderId: (int) $data['order_id'],
            cashierId: (int) $data['cashier_id'],
            transactionCode: $data['transaction_code'] ?? null,
        );
    }
}
