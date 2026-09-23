<h1>Close Business Day</h1>

<p class="muted">
    Confirm the Z-report for <strong><?= htmlspecialchars(date('D, d M Y', strtotime($summary['date']))) ?></strong>
    below, then close the day. Closing moves the terminal forward to the next business day.
</p>

<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-label">Orders</span>
        <span class="stat-value"><?= (int) $summary['order_count'] ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Items sold</span>
        <span class="stat-value"><?= (int) $summary['item_count'] ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Gross total</span>
        <span class="stat-value">KES <?= number_format($summary['gross_total'], 2) ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Paid</span>
        <span class="stat-value">KES <?= number_format($summary['paid_total'], 2) ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Unpaid</span>
        <span class="stat-value">KES <?= number_format($summary['unpaid_total'], 2) ?></span>
    </div>
</div>

<h2 class="report-title">Payments by Method</h2>
<table class="plain-table">
    <thead>
    <tr>
        <th>Method</th>
        <th>Count</th>
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    <?php if (empty($summary['methods'])) : ?>
        <tr><td colspan="3" class="muted">No payments recorded.</td></tr>
    <?php else : ?>
        <?php foreach ($summary['methods'] as $method) : ?>
            <tr>
                <td><?= htmlspecialchars($method['method']) ?></td>
                <td><?= (int) $method['count'] ?></td>
                <td>KES <?= number_format($method['total'], 2) ?></td>
            </tr>
        <?php endforeach ?>
    <?php endif ?>
    </tbody>
</table>

<div class="close-day-actions">
    <a href="/admin" class="button-outline">Cancel</a>
    <form method="POST" action="/admin/close-day" onsubmit="return confirm('Close the business day? Unpaid orders will roll forward.')">
        <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
        <button type="submit" class="button button-danger">Close <?= htmlspecialchars($summary['date']) ?></button>
    </form>
</div>