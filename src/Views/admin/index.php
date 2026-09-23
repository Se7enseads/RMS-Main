<?php

$categoryPalette = ['#2e7d32', '#1565c0', '#ef6c00', '#6a1b9a', '#00838f', '#c62828', '#4527a0', '#827717', '#d81b60', '#37474f'];
$categoryColors = [];
foreach (array_keys($categoryLabels ?? []) as $index => $label) {
    $categoryColors[] = $categoryPalette[$index % count($categoryPalette)];
}
?>

<div class="dashboard-header">
  <h1>Dashboard</h1>
  <form method="GET" action="/" class="date-filter">
    <input type="date" name="date" value="<?= htmlspecialchars($date ?? '') ?>">
    <button type="submit" class="button button-primary">Filter</button>
    <?php if (!empty($date)) : ?>
      <a href="/" class="button-outline">Today</a>
    <?php endif ?>
  </form>
</div>

<section class="business-day-banner">
  <div class="business-day-info">
    <span class="stat-label">Business day</span>
    <span class="stat-value"><?= htmlspecialchars($businessDay['date'] ?? '') ?>
      <?php if (!empty($businessDay['is_closed'])) : ?>
        <span class="banner-badge">closed</span>
      <?php else : ?>
        <span class="banner-badge badge-open">open</span>
      <?php endif ?></span>
  </div>
  <?php if (empty($businessDay['is_closed'])) : ?>
    <a href="/admin/close-day" class="button button-danger">Close Day</a>
  <?php endif ?>
</section>

<section class="stats-grid">
  <div class="stat-card">
    <span class="stat-label">Revenue <?= htmlspecialchars($date) ?></span>
    <span class="stat-value">KES <?= number_format($revenueToday ?? 0, 2) ?></span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Orders Today</span>
    <span class="stat-value"><?= $ordersToday ?? 0 ?></span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Open Orders</span>
    <span class="stat-value"><?= $openOrders ?? 0 ?></span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Menu Items</span>
    <span class="stat-value"><?= $menuItems ?? 0 ?></span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Active Users</span>
    <span class="stat-value"><?= $activeUsers ?? 0 ?></span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Tables Available</span>
    <span class="stat-value"><?= $tablesAvailable ?? 0 ?> / <?= $tablesTotal ?? 0 ?></span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Tables Occupied</span>
    <span class="stat-value"><?= $tablesOccupied ?? 0 ?></span>
  </div>
  <div class="stat-card">
    <span class="stat-label">Tables Reserved</span>
    <span class="stat-value"><?= $tablesReserved ?? 0 ?></span>
  </div>
</section>

<section class="charts-grid">
  <div class="chart-card">
    <h2>Revenue - Last 7 Days</h2>
    <div class="chart-wrap"><canvas id="revenueTrend"></canvas></div>
  </div>
  <div class="chart-card">
    <h2>Top 10 Items</h2>
    <div class="chart-wrap"><canvas id="topItems"></canvas></div>
  </div>
  <div class="chart-card">
    <h2>Revenue by Category</h2>
    <div class="chart-wrap"><canvas id="categoryShare"></canvas></div>
  </div>
</section>

<section class="quick-actions">
  <a href="/admin/items" class="card">
    <h2>Menu Items</h2>
    <p>Create and manage menu items.</p>
  </a>
  <a href="/admin/users" class="card">
    <h2>Users</h2>
    <p>Manage system users and their roles.</p>
  </a>
  <a href="/admin/roles" class="card">
    <h2>Roles</h2>
    <p>Define roles and their permissions.</p>
  </a>
  <a href="/kiosk" class="card">
    <h2>Kiosk</h2>
    <p>Take and manage orders.</p>
  </a>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Chart === 'undefined') {
            return;
        }

        const kes = (v) => 'KES ' + Number(v).toLocaleString();

        const trendEl = document.getElementById('revenueTrend');
        if (trendEl) {
            new Chart(trendEl, {
                type: 'line',
                data: {
                    labels: <?= json_encode($trendLabels ?? []) ?>,
                    datasets: [{
                        label: 'Revenue',
                        data: <?= json_encode(array_map('floatval', $trendRevenue ?? [])) ?>,
                        borderColor: '#2e7d32',
                        backgroundColor: 'rgba(46, 125, 50, 0.12)',
                        fill: true,
                        tension: 0.3,
                        pointRadius: 3,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: (v) => kes(v) } },
                    },
                },
            });
        }

        const itemsEl = document.getElementById('topItems');
        if (itemsEl) {
            const topRows = <?= json_encode($topItems ?? []) ?>;
            new Chart(itemsEl, {
                type: 'bar',
                data: {
                    labels: topRows.map((row) => row.item),
                    datasets: [{
                        label: 'Revenue',
                        data: topRows.map((row) => row.revenue),
                        backgroundColor: '#1565c0',
                    }],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, ticks: { callback: (v) => kes(v) } },
                    },
                },
            });
        }

        const catEl = document.getElementById('categoryShare');
        if (catEl) {
            new Chart(catEl, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode($categoryLabels ?? []) ?>,
                    datasets: [{
                        data: <?= json_encode($categoryRevenue ?? []) ?>,
                        backgroundColor: <?= json_encode($categoryColors ?? []) ?>,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { callbacks: { label: (ctx) => ctx.label + ': ' + kes(ctx.parsed) } },
                    },
                },
            });
        }
    });
</script>
