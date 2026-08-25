<?php

use App\Models\Order;

/**
 * @var array<int, Order> $openOrders
 * @var array<int, Order> $todayPaid
 * @var array<int, Order> $todayUnpaid
 * @var array<int, array<int, \App\Models\OrderItem>> $itemsByOrderId
 */

$renderCard = function (Order $order, string $badgeClass, string $badgeText) use ($itemsByOrderId): void {
    ?>
    <article class="receipt-card">
        <header class="receipt-header">
            <span class="receipt-store">RMS</span>
            <span class="receipt-order"><?= htmlspecialchars($order->orderNumber) ?></span>
            <span class="receipt-status <?= htmlspecialchars($badgeClass) ?>"><?= htmlspecialchars($badgeText) ?></span>
        </header>
        <div class="receipt-meta">
            <span><?= date('h:i A', strtotime($order->createdAt ?? 'now')) ?></span>
            <span><?= $order->type === 'DINE_IN' ? 'Table ' . htmlspecialchars($order->tableNumber ?? '—') : htmlspecialchars($order->type) ?></span>
            <span>Waiter: <?= htmlspecialchars($order->userName ?? '—') ?></span>
            <span><?= count($itemsByOrderId[$order->id] ?? []) ?> item<?= count($itemsByOrderId[$order->id] ?? []) === 1 ? '' : 's' ?></span>
        </div>
        <div class="receipt-rule"></div>
        <ul class="receipt-items">
            <?php foreach ($itemsByOrderId[$order->id] ?? [] as $item) : ?>
                <li>
                    <span class="receipt-item-name">
                        <strong><?= (int)$item->quantity ?>x</strong> <?= htmlspecialchars($item->menuItemName) ?>
                    </span>
                    <span class="receipt-item-price"><?= number_format($item->priceAtTime * $item->quantity, 2) ?></span>
                </li>
            <?php endforeach ?>
        </ul>
        <div class="receipt-rule"></div>
        <footer class="receipt-footer">
            <span>TOTAL</span>
            <span class="receipt-total"><?= number_format($order->totalAmount, 2) ?> KES</span>
        </footer>
        <div class="receipt-actions">
            <a href="/kiosk/order/<?= $order->id ?>/bill" target="_blank" class="button button-primary no-print">Print Bill</a>
        </div>
    </article>
    <?php
};
?>

<section class="kiosk-dashboard">

    <div class="dashboard-header">
        <h1>Dashboard</h1>
        <form method="GET" action="/kiosk" class="date-filter">
            <input type="date" name="date" value="<?= htmlspecialchars($dateFilter ?? '') ?>">
            <button type="submit" class="button button-primary">Filter</button>
            <?php if (!empty($dateFilter)) : ?>
                <a href="/kiosk" class="button button-outline">Today</a>
            <?php endif ?>
        </form>
    </div>

    <h2>Open Orders</h2>
    <?php if (empty($openOrders)) : ?>
        <p class="empty-note">No open orders.</p>
    <?php else : ?>
        <div class="order-grid">
            <?php foreach ($openOrders as $order) : ?>
                <?php $renderCard($order, 'open', $order->status); ?>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <h2>Orders for <?= htmlspecialchars($date) ?></h2>

    <details class="kiosk-section unpaid" open>
        <summary><span class="section-title unpaid">Unpaid (<?= count($todayUnpaid) ?>)</span></summary>
        <?php if (empty($todayUnpaid)) : ?>
            <p class="muted">None</p>
        <?php else : ?>
            <div class="order-grid">
                <?php foreach ($todayUnpaid as $order) : ?>
                    <?php $renderCard($order, 'unpaid', $order->status); ?>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </details>

    <details class="kiosk-section paid">
        <summary><span class="section-title paid">Paid (<?= count($todayPaid) ?>)</span></summary>
        <?php if (empty($todayPaid)) : ?>
            <p class="muted">None</p>
        <?php else : ?>
            <div class="order-grid">
                <?php foreach ($todayPaid as $order) : ?>
                    <?php $renderCard($order, 'paid', $order->paymentMethod ?? 'PAID'); ?>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </details>

</section>