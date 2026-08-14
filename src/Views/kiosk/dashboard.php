<!-- TODO: have stacked sections than side-by-side, remove open orders -->
<!-- TODO: remove duplication, (use an invisible Table, if possible) -->
<section class="kiosk-dashboard">

    <!--TODO: Fix alignment-->
    <div class="dashboard-header">
        <h1>Dashboard</h1>
        <form method="GET" action="/kiosk" class="date-filter">
            <input type="date" name="date" value="<?= htmlspecialchars($dateFilter ?? '') ?>">
            <button type="submit" class="button button-primary">Filter</button>
            <?php if (!empty($dateFilter)) : ?>
                <!--TODO: Change to button style-->
                <a href="/kiosk" class="button-outline">Today</a>
            <?php endif ?>
        </form>
    </div>

    <h2>Open Orders</h2>
    <?php if (empty($openOrders)) : ?>
        <p class="empty-note">No open orders.</p>
    <?php else : ?>
        <div class="order-grid">
            <?php foreach ($openOrders as $order) : ?>
                <!--TODO: Change card style-->
                <div class="order-card open">
                    <div class="order-meta">
                        <span class="order-number"><?= htmlspecialchars($order->orderNumber) ?></span>
                        <!--TODO: Fix status button style-->
                        <span class="order-status"><?= htmlspecialchars($order->status) ?></span>
                    </div>
                    <!--TODO: Have the currency icon be from the db too-->
                    <div class="order-amount"><?= number_format($order->totalAmount, 2) ?>KES</div>
                    <div class="order-details">
                        <span>Table <?= htmlspecialchars($order->tableNumber ?? '—') ?></span>
                        <!--TODO: if count is more than one unit is items-->
                        <span><?= (int)$order->itemCount ?> items</span>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <h2>Orders for <?= htmlspecialchars($date) ?></h2>

    <div class="dashboard-sections">
        <div class="dashboard-section">
            <h3 class="section-title unpaid">Unpaid</h3>
            <?php if (empty($todayUnpaid)) : ?>
                <p class="muted">None</p>
            <?php else : ?>
                <!-- TODO: Add eating status, dine-in and takeaway-->
                <div class="order-grid">
                    <?php foreach ($todayUnpaid as $order) : ?>
                        <!--TODO: Change card style-->
                        <div class="order-card">
                            <div class="order-meta">
                                <span class="order-number"><?= htmlspecialchars($order->orderNumber) ?></span>
                                <!--TODO: Fix status button style-->
                                <span class="order-type"><?= htmlspecialchars($order->status) ?></span>
                            </div>
                            <!--TODO: Have the currency icon be from the db too-->
                            <div class="order-amount"><?= number_format($order->totalAmount, 2) ?></div>
                            <div class="order-details">
                                <span>Table <?= htmlspecialchars($order->tableNumber ?? '—') ?></span>
                                <!--TODO: if count is more than one unit is items-->
                                <span><?= (int)$order->itemCount ?> items</span>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        </div>

        <div class="dashboard-section">
            <h3 class="section-title paid">Paid</h3>
            <?php if (empty($todayPaid)) : ?>
                <p class="muted">None</p>
            <?php else : ?>
                <div class="order-grid">
                    <?php foreach ($todayPaid as $order) : ?>
                        <div class="order-card">
                            <div class="order-meta">
                                <span class="order-number"><?= htmlspecialchars($order->orderNumber) ?></span>
                                <span class="order-payment"><?= htmlspecialchars($order->paymentMethod ?? 'PAID') ?></span>
                            </div>
                            <div class="order-amount"><?= number_format($order->totalAmount, 2) ?></div>
                            <div class="order-details">
                                <span>Table <?= htmlspecialchars($order->tableNumber ?? '—') ?></span>
                                <span><?= (int)$order->itemCount ?> items</span>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        </div>

        <div class="dashboard-section">
            <h3 class="section-title voided">Voided</h3>
            <?php if (empty($todayVoided)) : ?>
                <p class="muted">None</p>
            <?php else : ?>
                <div class="order-grid">
                    <?php foreach ($todayVoided as $order) : ?>
                        <div class="order-card voided-card">
                            <div class="order-meta">
                                <span class="order-number"><?= htmlspecialchars($order->orderNumber) ?></span>
                                <span class="order-type"><?= htmlspecialchars($order->status) ?></span>
                            </div>
                            <div class="order-amount"><?= number_format($order->totalAmount, 2) ?></div>
                            <div class="order-details">
                                <span>Table <?= htmlspecialchars($order->tableNumber ?? '—') ?></span>
                            </div>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        </div>
    </div>

</section>