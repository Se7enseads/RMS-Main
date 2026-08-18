<h1>Store Dashboard</h1>

<section class="stats-grid">
    <div class="stat-card">
        <span class="stat-label">Active Ingredients</span>
        <span class="stat-value"><?= $totalIngredients ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Low Stock</span>
        <span class="stat-value"><?= $lowStockCount ?></span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Inventory Value</span>
        <span class="stat-value">KES <?= number_format($inventoryValue, 2) ?></span>
    </div>
</section>

<section class="quick-actions">
    <a href="/store/inventory" class="card">
        <h2>Inventory</h2>
        <p>View and manage ingredients, stock levels and costs.</p>
    </a>
    <a href="/store/stocktake" class="card">
        <h2>Stock Take</h2>
        <p>Count physical stock and record daily discrepancies.</p>
    </a>
    <a href="/store/variance" class="card">
        <h2>Variance</h2>
        <p>Review differences between counted and system stock.</p>
    </a>
    <a href="/store/inventory/create" class="card">
        <h2>New Ingredient</h2>
        <p>Add an ingredient and start recording stock.</p>
    </a>
</section>