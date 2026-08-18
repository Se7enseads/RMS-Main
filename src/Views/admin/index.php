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
