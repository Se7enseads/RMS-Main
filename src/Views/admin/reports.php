<h1>Reports</h1>

<p class="muted">Sales, item and payment reports for the selected date range.</p>

<form method="GET" action="/admin/reports" class="report-filters">
    <label>
        From
        <input type="date" name="from" value="<?= htmlspecialchars($from) ?>">
    </label>
    <label>
        To
        <input type="date" name="to" value="<?= htmlspecialchars($to) ?>">
    </label>
    <button type="submit" class="button button-primary">Run Report</button>
    <a href="/admin/reports" class="button-outline">Today</a>
</form>

<h2 class="report-title">Sales by Day</h2>
<div class="report-toolbar">
    <button type="button" class="button button-outline" onclick="tableSales.print(true)">Print</button>
    <button type="button" class="button button-outline" onclick="tableSales.download('csv', 'sales-by-day.csv')">CSV</button>
</div>
<div id="sales-table"></div>

<h2 class="report-title">Sales by Item</h2>
<div class="report-toolbar">
    <button type="button" class="button button-outline" onclick="tableItems.print(true)">Print</button>
    <button type="button" class="button button-outline" onclick="tableItems.download('csv', 'sales-by-item.csv')">CSV</button>
</div>
<div id="items-table"></div>

<h2 class="report-title">Payments by Method</h2>
<div class="report-toolbar">
    <button type="button" class="button button-outline" onclick="tablePayments.print(true)">Print</button>
    <button type="button" class="button button-outline" onclick="tablePayments.download('csv', 'payments-by-method.csv')">CSV</button>
</div>
<div id="payments-table"></div>

<h2 class="report-title">Sales by Category</h2>
<div class="report-toolbar">
    <button type="button" class="button button-outline" onclick="tableCategories.print(true)">Print</button>
    <button type="button" class="button button-outline" onclick="tableCategories.download('csv', 'sales-by-category.csv')">CSV</button>
</div>
<div id="categories-table"></div>

<h2 class="report-title">Hourly Sales</h2>
<div class="report-toolbar">
    <button type="button" class="button button-outline" onclick="tableHours.print(true)">Print</button>
    <button type="button" class="button button-outline" onclick="tableHours.download('csv', 'hourly-sales.csv')">CSV</button>
</div>
<div id="hours-table"></div>

<h2 class="report-title">Orders by Status</h2>
<div class="report-toolbar">
    <button type="button" class="button button-outline" onclick="tableStatus.print(true)">Print</button>
    <button type="button" class="button button-outline" onclick="tableStatus.download('csv', 'orders-by-status.csv')">CSV</button>
</div>
<div id="status-table"></div>

<script>
  const salesData = <?= json_encode(array_map(fn($row) => [
      'day' => $row['day'],
      'orders' => $row['orders'],
      'items' => $row['items'],
      'revenue' => number_format($row['revenue'], 2),
      'paid' => number_format($row['paid'], 2),
      'unpaid' => number_format($row['unpaid'], 2),
  ], $salesByDay)) ?>;

  const itemsData = <?= json_encode(array_map(fn($row) => [
      'item' => $row['item'],
      'category' => $row['category'],
      'quantity' => $row['quantity'],
      'revenue' => number_format($row['revenue'], 2),
  ], $itemSales)) ?>;

  const paymentsData = <?= json_encode(array_map(fn($row) => [
      'method' => $row['method'],
      'count' => $row['count'],
      'total' => number_format($row['total'], 2),
  ], $paymentMethods)) ?>;

  const categoriesData = <?= json_encode(array_map(fn($row) => [
      'category' => $row['category'],
      'quantity' => $row['quantity'],
      'revenue' => number_format($row['revenue'], 2),
  ], $categorySales)) ?>;

  const hoursData = <?= json_encode(array_map(fn($row) => [
      'hour' => str_pad((string) $row['hour'], 2, '0', STR_PAD_LEFT) . ':00',
      'orders' => $row['orders'],
      'revenue' => number_format($row['revenue'], 2),
  ], $hourlySales)) ?>;

  const statusData = <?= json_encode(array_map(fn($row) => [
      'status' => $row['status'],
      'count' => $row['count'],
  ], $statusSummary)) ?>;

  const tableSales = new Tabulator('#sales-table', {
    data: salesData,
    layout: 'fitColumns',
    columns: [
      { title: 'Day', field: 'day', headerFilter: true },
      { title: 'Orders', field: 'orders', headerFilter: true },
      { title: 'Items Sold', field: 'items', headerFilter: true },
      { title: 'Revenue (KES)', field: 'revenue', headerFilter: true },
      { title: 'Paid (KES)', field: 'paid', headerFilter: true },
      { title: 'Unpaid (KES)', field: 'unpaid', headerFilter: true },
    ],
  });

  const tableItems = new Tabulator('#items-table', {
    data: itemsData,
    layout: 'fitColumns',
    columns: [
      { title: 'Item', field: 'item', headerFilter: true },
      { title: 'Category', field: 'category', headerFilter: true },
      { title: 'Quantity', field: 'quantity', headerFilter: true },
      { title: 'Revenue (KES)', field: 'revenue', headerFilter: true },
    ],
  });

  const tablePayments = new Tabulator('#payments-table', {
    data: paymentsData,
    layout: 'fitColumns',
    columns: [
      { title: 'Method', field: 'method', headerFilter: true },
      { title: 'Count', field: 'count', headerFilter: true },
      { title: 'Total (KES)', field: 'total', headerFilter: true },
    ],
  });

  const tableCategories = new Tabulator('#categories-table', {
    data: categoriesData,
    layout: 'fitColumns',
    columns: [
      { title: 'Category', field: 'category', headerFilter: true },
      { title: 'Quantity', field: 'quantity', headerFilter: true },
      { title: 'Revenue (KES)', field: 'revenue', headerFilter: true },
    ],
  });

  const tableHours = new Tabulator('#hours-table', {
    data: hoursData,
    layout: 'fitColumns',
    columns: [
      { title: 'Hour', field: 'hour', headerFilter: true },
      { title: 'Orders', field: 'orders', headerFilter: true },
      { title: 'Revenue (KES)', field: 'revenue', headerFilter: true },
    ],
  });

  const tableStatus = new Tabulator('#status-table', {
    data: statusData,
    layout: 'fitColumns',
    columns: [
      { title: 'Status', field: 'status', headerFilter: true },
      { title: 'Orders', field: 'count', headerFilter: true },
    ],
  });
</script>