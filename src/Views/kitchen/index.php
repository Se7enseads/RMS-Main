<section class="kitchen-dashboard">

    <div class="kitchen-header">
        <h1>Kitchen Display</h1>
        <span class="kitchen-updated">Updated <?= date('h:i A') ?></span>
    </div>

    <h2>Waiting to be Served</h2>
    <?php if (empty($waiting)) : ?>
        <p class="empty-note">No orders waiting.</p>
    <?php else : ?>
        <div class="kitchen-grid">
            <?php foreach ($waiting as $order) : ?>
                <article class="receipt-card">
                    <header class="receipt-header">
                        <span class="receipt-store">RMS</span>
                        <span class="receipt-order"><?= htmlspecialchars($order->orderNumber) ?></span>
                    </header>
                    <div class="receipt-meta">
                        <span><?= date('h:i A', strtotime($order->createdAt)) ?></span>
                        <span><?= $order->type === 'DINE_IN' ? 'Table ' . htmlspecialchars($order->tableNumber ?? '—') : htmlspecialchars($order->type) ?></span>
                    </div>
                    <div class="receipt-rule"></div>
                    <ul class="receipt-items">
                        <?php foreach ($items[$order->id] ?? [] as $item) : ?>
                            <li>
                                <span class="receipt-item-name">
                                    <strong><?= (int) $item->quantity ?>x</strong> <?= htmlspecialchars($item->menuItemName) ?>
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
                    <form method="POST" action="/kitchen/serve/<?= $order->id ?>" class="receipt-actions">
                        <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
                        <button type="submit" class="button button-primary">Mark as Served</button>
                    </form>
                </article>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <details class="served-history">
        <summary>Served Orders (<?= (int) $servedCount ?>)</summary>
        <?php if (empty($served)) : ?>
            <p class="muted">No served orders today.</p>
        <?php else : ?>
            <div class="kitchen-grid served">
                <?php foreach ($served as $order) : ?>
                    <article class="receipt-card served-card">
                        <header class="receipt-header">
                            <span class="receipt-store">RMS</span>
                            <span class="receipt-order"><?= htmlspecialchars($order->orderNumber) ?></span>
                        </header>
                        <div class="receipt-meta">
                            <span><?= date('h:i A', strtotime($order->createdAt)) ?></span>
                            <span><?= $order->type === 'DINE_IN' ? 'Table ' . htmlspecialchars($order->tableNumber ?? '—') : htmlspecialchars($order->type) ?></span>
                        </div>
                        <div class="receipt-rule"></div>
                        <ul class="receipt-items">
                            <?php foreach ($items[$order->id] ?? [] as $item) : ?>
                                <li>
                                    <span class="receipt-item-name">
                                        <strong><?= (int) $item->quantity ?>x</strong> <?= htmlspecialchars($item->menuItemName) ?>
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
                    </article>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </details>

</section>