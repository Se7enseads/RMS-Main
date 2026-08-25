<h1>Logs</h1>

<p class="muted">Read-only record of staff actions.</p>

<div class="report-toolbar">
    <button type="button" class="button button-outline" onclick="tableLogs.print(true)">Print</button>
</div>

<div id="logs-table"></div>

<script>
  const logsData = <?= json_encode(array_map(fn($log) => [
      'id' => $log->id,
      'user' => $log->getFullName(),
      'what' => $log->what,
      'method' => $log->method,
      'url' => $log->url,
      'ip' => $log->ipAddress ?? '-',
      'created_at' => $log->createdAt,
  ], $logs)) ?>;

  const tableLogs = new Tabulator('#logs-table', {
    data: logsData,
    layout: 'fitColumns',
    pagination: true,
    paginationMode: 'local',
    paginationSize: 15,
    paginationSizeSelector: [10, 15, 25, 50],
    initialSort: [{ column: 'id', dir: 'desc' }],
    columns: [
      { title: 'ID', field: 'id', width: 70, headerFilter: true },
      { title: 'Staff', field: 'user', headerFilter: true },
      { title: 'Action', field: 'what', headerFilter: true },
      { title: 'Method', field: 'method', width: 90, headerFilter: true },
      { title: 'URL', field: 'url', headerFilter: true },
      { title: 'IP', field: 'ip', width: 130 },
      { title: 'Time', field: 'created_at', width: 180, sorter: 'datetime', headerFilter: true },
    ],
  });
</script>