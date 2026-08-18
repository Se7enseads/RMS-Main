<h1>User Activity</h1>

<a href="/admin/users" class="button-outline">&larr; Back to Users</a>

<div class="user-summary">
  <strong><?= htmlspecialchars($user->getFullName()) ?></strong>
  &mdash; <?= htmlspecialchars($user->roleName ?? 'Not assigned') ?>
  <span class="muted">(<?= htmlspecialchars($user->employeeNum) ?>)</span>
</div>

<div id="user-orders-table"></div>

<script>
  const userOrdersData = <?= json_encode(array_map(fn($o) => [
      'orderNumber' => $o->orderNumber,
      'date' => substr($o->createdAt ?? '', 0, 10),
      'time' => substr($o->createdAt ?? '', 11, 5),
      'type' => $o->type,
      'table' => $o->tableNumber ?? '-',
      'items' => $o->itemCount ?? 0,
      'total' => $o->totalAmount,
      'status' => $o->status,
      'payment' => $o->isCancelled() ? 'Cancelled' : ($o->isPaid ? 'Paid (' . ($o->paymentMethod ?? '?') . ')' : 'Unpaid'),
  ], $orders)) ?>;

  new Tabulator('#user-orders-table', {
    data: userOrdersData,
    layout: 'fitColumns',
    groupBy: 'date',
    groupHeader: (value) => 'Orders for ' + new Date(value + 'T00:00:00').toLocaleDateString('en-GB', { weekday: 'short', day: '2-digit', month: 'short', year: 'numeric' }),
    groupSort: [{ field: 'date', dir: 'desc' }],
    initialSort: [{ column: 'date', dir: 'desc' }],
    pagination: true,
    paginationMode: 'local',
    paginationSize: 10,
    paginationSizeSelector: [5, 10, 25, 50],
    columns: [
      { title: 'Order Number', field: 'orderNumber', headerFilter: true },
      { title: 'Time', field: 'time', width: 90 },
      { title: 'Type', field: 'type', width: 110, headerFilter: true },
      { title: 'Table', field: 'table', width: 90, headerFilter: true },
      { title: 'Items', field: 'items', width: 90, align: 'right' },
      { title: 'Total', field: 'total', width: 130, formatter: 'money', formatterParams: { precision: 2, symbol: 'KES ' } },
      { title: 'Status', field: 'status', width: 110, headerFilter: true },
      { title: 'Payment', field: 'payment', headerFilter: true },
    ],
  });
</script>