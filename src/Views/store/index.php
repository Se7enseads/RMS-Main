<h1>Store Inventory</h1>

<a href="/store/ingredients/create" class="button button-primary">+ Create New Ingredient</a>

<div id="ingredients-table"></div>

<script>
  const ingredientsData = <?= json_encode(array_map(fn($i) => [
      'id' => $i->id,
      'name' => $i->name,
      'baseUnit' => $i->baseUnit,
      'receiveUnit' => $i->receiveUnit,
      'unitsPerContainer' => $i->unitsPerContainer,
      'stock' => $i->stock,
      'stockReceive' => $i->stockInReceiveUnits(),
      'costPerUnit' => $i->costPerUnit,
      'reorderLevel' => $i->reorderLevel,
      'status' => $i->active ? 'Active' : 'Inactive',
      'low' => $i->isLowStock(),
  ], $ingredients)) ?>;
  const csrfToken = <?= json_encode(\App\Core\Session::csrfToken()) ?>;

  new Tabulator('#ingredients-table', {
    data: ingredientsData,
    layout: 'fitColumns',
    pagination: true,
    paginationMode: 'local',
    paginationSize: 15,
    paginationSizeSelector: [10, 15, 25, 50],
    initialSort: [{ column: 'id', dir: 'desc' }],
    columns: [
      { title: 'ID', field: 'id', width: 60, headerFilter: true },
      { title: 'Name', field: 'name', headerFilter: true },
      { title: 'Base Unit', field: 'baseUnit', width: 100, headerFilter: true },
      { title: 'Receive Unit', field: 'receiveUnit', width: 110, headerFilter: true },
      {
        title: 'Pieces / Container',
        field: 'unitsPerContainer',
        width: 140,
        formatter: (cell) => cell.getValue() ? cell.getValue() : '-',
      },
      {
        title: 'Stock',
        field: 'stock',
        width: 120,
        align: 'right',
        formatter: (cell) => {
          const d = cell.getData();
          const low = d.low && d.status === 'Active' ? ' (LOW)' : '';
          return cell.getValue() + ' ' + d.baseUnit + low;
        },
      },
      {
        title: 'Cost / ' + 'Base Unit',
        field: 'costPerUnit',
        width: 130,
        align: 'right',
        formatter: 'money',
        formatterParams: { precision: 4, symbol: 'KES ' },
      },
      {
        title: 'Reorder Level',
        field: 'reorderLevel',
        width: 110,
        align: 'right',
        formatter: (cell) => {
          const d = cell.getData();
          return d.reorderLevel ? d.reorderLevel + ' ' + d.baseUnit : '-';
        },
      },
      { title: 'Status', field: 'status', width: 100, headerFilter: true },
      {
        title: 'Actions',
        field: 'id',
        width: 250,
        formatter: (cell) => {
          const id = cell.getValue();
          return '<a href="/store/ingredients/' + id + '/stock" class="button-primary action-button">+ Stock</a> ' +
            '<a href="/store/ingredients/edit/' + id + '" class="button-outline action-button">Edit</a> ' +
            '<form action="/store/ingredients/deactivate/' + id + '" method="POST" class="inline-form">' +
            '<input type="hidden" name="csrf_token" value="' + csrfToken + '">' +
            '<button type="submit" class="button-danger action-button" onclick="return confirm(\'Are you sure?\')">Deactivate</button>' +
            '</form>';
        },
      },
    ],
    rowClick: (e, row) => {
      if (e.target.closest('a, form, button, input')) return;
      window.location.href = '/store/ingredients/' + row.getData().id + '/stock';
    },
  });
</script>