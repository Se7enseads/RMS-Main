<h1>Menu Items</h1>

<a href="/admin/items/create" class="button button-primary">+ Create New Item</a>

<div id="items-table"></div>

<script>
  const itemsData = <?= json_encode(array_map(fn($i) => [
      'id' => $i->id,
      'name' => $i->name,
      'category' => $i->categoryName ?? 'Uncategorized',
      'price' => $i->price,
      'cost' => $recipeCosts[$i->id] ?? 0.0,
      'type' => $i->isCombo ? 'Combo' : 'Single',
      'status' => $i->active ? 'Active' : 'Inactive',
  ], $items)) ?>;
  const csrfToken = <?= json_encode(\App\Core\Session::csrfToken()) ?>;

  new Tabulator('#items-table', {
    data: itemsData,
    layout: 'fitColumns',
    pagination: true,
    paginationMode: 'local',
    paginationSize: 15,
    paginationSizeSelector: [10, 15, 25, 50],
    initialSort: [{ column: 'id', dir: 'desc' }],
    columns: [
      { title: 'ID', field: 'id', width: 70, headerFilter: true },
      { title: 'Name', field: 'name', headerFilter: true },
      { title: 'Category', field: 'category', headerFilter: true },
      { title: 'Price', field: 'price', formatter: 'money', formatterParams: { precision: 2, symbol: 'KES ' }, headerFilter: true },
      { title: 'Ingredient Cost', field: 'cost', width: 130, align: 'right', formatter: 'money', formatterParams: { precision: 2, symbol: 'KES ' } },
      { title: 'Type', field: 'type', width: 100, headerFilter: true },
      { title: 'Status', field: 'status', width: 100, headerFilter: true },
      {
        title: 'Actions',
        field: 'id',
        width: 200,
        formatter: (cell) => {
          const id = cell.getValue();
          return '<a href="/admin/items/edit/' + id + '" class="button-outline action-button">Edit</a> ' +
            '<form action="/admin/items/deactivate/' + id + '" method="POST" class="inline-form">' +
            '<input type="hidden" name="csrf_token" value="' + csrfToken + '">' +
            '<button type="submit" class="button-danger action-button" onclick="return confirm(\'Are you sure?\')">Deactivate</button>' +
            '</form>';
        },
      },
    ],
    rowClick: (e, row) => {
      if (e.target.closest('a, form, button, input')) return;
      window.location.href = '/admin/items/edit/' + row.getData().id;
    },
  });
</script>