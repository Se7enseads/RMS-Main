<?php

use App\Models\Order;

/**
 * @var array<int, Order> $orders
 * @var float $paidTotal
 * @var float $unpaidTotal
 */

?>
<section class="kiosk-payments">

    <div class="dashboard-header">
        <h1>Payments</h1>
        <form method="GET" action="/kiosk/payments" class="date-filter">
            <input type="date" name="date" value="<?= htmlspecialchars($dateFilter ?? '') ?>">
            <button type="submit" class="button button-primary">Filter</button>
            <?php if (!empty($dateFilter)) : ?>
                <a href="/kiosk/payments" class="button-outline">Today</a>
            <?php endif ?>
        </form>
    </div>

    <div class="payments-summary">
        <div class="summary-item paid">
            <span>Paid</span>
            <strong><?= number_format($paidTotal, 2) ?> KES</strong>
        </div>
        <div class="summary-item unpaid">
            <span>Unpaid</span>
            <strong><?= number_format($unpaidTotal, 2) ?> KES</strong>
        </div>
    </div>

    <?php if (empty($orders)) : ?>
        <p class="empty-note">No orders for <?= htmlspecialchars($date) ?>.</p>
    <?php else : ?>
        <table class="payments-table">
            <thead>
            <tr>
                <th>Order</th>
                <th>Time</th>
                <th>Type</th>
                <th>Table</th>
                <th>Waiter</th>
                <th>Items</th>
                <th>Total</th>
                <th>Payment</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order) : ?>
                <tr class="<?= $order->isPaid ? 'row-paid' : 'row-unpaid' ?>">
                    <td class="order-number"><?= htmlspecialchars($order->orderNumber) ?></td>
                    <td><?= date('h:i A', strtotime($order->createdAt ?? 'now')) ?></td>
                    <td><?= htmlspecialchars($order->type) ?></td>
                    <td><?= htmlspecialchars($order->tableNumber ?? '—') ?></td>
                    <td><?= htmlspecialchars($order->userName ?? '—') ?></td>
                    <td><?= (int)$order->itemCount ?></td>
                    <td class="total"><?= number_format($order->totalAmount, 2) ?> KES</td>
                    <td>
                        <?php if ($order->isPaid) : ?>
                            <span class="receipt-status paid"><?= htmlspecialchars($order->paymentMethod ?? 'PAID') ?></span>
                        <?php else : ?>
                            <span class="receipt-status unpaid">UNPAID</span>
                        <?php endif ?>
                    </td>
                    <td>
                        <a href="/kiosk/order/<?= $order->id ?>/bill" target="_blank" class="button button-outline no-print">Print Bill</a>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    <?php endif ?>

</section>