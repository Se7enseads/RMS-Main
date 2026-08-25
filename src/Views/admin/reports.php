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
</script>