<?php

use App\Models\Order;
use App\Repositories\OrderRepository;

/**
 * @var array<int, Order> $orders
 * @var array<int, Order> $paidOrders
 * @var array<string, mixed> $day
 * @var array<int, string> $methods
 * @var array<int, string> $errors
 */

$orderRepository = new OrderRepository();
$itemsByOrderId = $orderRepository->findItemsByOrderIds(array_map(static fn(Order $o) => $o->id, $orders));
?>

    <div class="dashboard-header">
        <h1>Cashier</h1>
        <p class="muted">Settling unpaid orders for business day <strong><?= htmlspecialchars($day['date']) ?></strong>.
        </p>
    </div>

<?php if (!empty($errors)) : ?>
    <div class="errors">
        <ul>
            <?php foreach ($errors as $msg) : ?>
                <li><?= htmlspecialchars($msg) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>

<?php if (empty($orders)) : ?>
    <p class="muted">No unpaid orders to settle for this business day.</p>
<?php else : ?>
    <section class="cashier-grid">
        <?php foreach ($orders as $order) : ?>
            <article class="receipt-card">
                <header class="receipt-header">
                    <span class="receipt-store">RMS</span>
                    <span class="receipt-order"><?= htmlspecialchars($order->orderNumber) ?></span>
                    <span class="receipt-status status-unpaid">UNPAID</span>
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

                <form method="POST" action="/cashier/orders/<?= (int)$order->id ?>/pay" class="settle-form">
                    <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
                    <div class="settle-row">
                        <label>
                            Amount
                            <input type="number" name="amount" step="0.01" min="0.01"
                                   value="<?= number_format($order->totalAmount, 2, '.', '') ?>" required>
                        </label>
                        <label>
                            Method
                            <select name="method" required>
                                <?php foreach ($methods as $method) : ?>
                                    <option value="<?= htmlspecialchars($method) ?>"><?= htmlspecialchars($method) ?></option>
                                <?php endforeach ?>
                            </select>
                        </label>
                    </div>
                    <label>
                        Transaction code (optional)
                        <input type="text" name="transaction_code" placeholder="e.g. T12345" maxlength="50">
                    </label>
                    <button type="submit" class="button button-primary">Receive Payment</button>
                    <a href="/kiosk/order/<?= (int)$order->id ?>/bill" target="_blank" class="button button-outline">Print
                        Receipt</a>
                </form>
            </article>
        <?php endforeach ?>
    </section>
<?php endif ?>

<?php if (!empty($paidOrders)) : ?>
    <section class="settled-section">
        <h2>Settled Today <span class="settled-count">(<?= count($paidOrders) ?>)</span></h2>
        <table class="settled-table">
            <thead>
            <tr>
                <th>Order</th>
                <th>Time</th>
                <th>Service</th>
                <th>Waiter</th>
                <th>Items</th>
                <th>Method</th>
                <th>Total</th>
                <th>Receipt</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($paidOrders as $order) : ?>
                <tr>
                    <td class="mono"><?= htmlspecialchars($order->orderNumber) ?></td>
                    <td><?= date('h:i A', strtotime($order->createdAt ?? 'now')) ?></td>
                    <td><?= $order->type === 'DINE_IN' ? 'Table ' . htmlspecialchars($order->tableNumber ?? '—') : htmlspecialchars($order->type) ?></td>
                    <td><?= htmlspecialchars($order->userName ?? '—') ?></td>
                    <td><?= (int)($order->itemCount ?? 0) ?></td>
                    <td>
                        <span class="method-badge method-<?= strtolower(htmlspecialchars((string)($order->paymentMethod ?? 'CASH'))) ?>"><?= htmlspecialchars((string)($order->paymentMethod ?? 'CASH')) ?></span>
                    </td>
                    <td class="mono"><?= number_format($order->totalAmount, 2) ?> KES</td>
                    <td>
                        <a href="/kiosk/order/<?= (int)$order->id ?>/bill" target="_blank"
                           class="receipt-print" title="Print receipt for <?= htmlspecialchars($order->orderNumber) ?>">
                            Print Receipt
                        </a>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </section>
<?php endif ?>