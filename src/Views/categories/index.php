<h1>Category List</h1>

<a href="/admin/categories/create" class="button button-primary">+ Create New Category</a>

<div id="categories-table"></div>

<script>
  const categoriesData = <?= json_encode(array_map(fn($c) => [
      'id' => $c->id,
      'name' => $c->name,
      'station' => $c->station,
      'status' => $c->active ? 'Active' : 'Inactive',
  ], $categories)) ?>;
  const csrfToken = <?= json_encode(\App\Core\Session::csrfToken()) ?>;

  new Tabulator('#categories-table', {
    data: categoriesData,
    layout: 'fitColumns',
    pagination: true,
    paginationMode: 'local',
    paginationSize: 15,
    paginationSizeSelector: [10, 15, 25, 50],
    initialSort: [{ column: 'id', dir: 'desc' }],
    columns: [
      { title: 'ID', field: 'id', width: 70, headerFilter: true },
      { title: 'Name', field: 'name', headerFilter: true },
      { title: 'Station', field: 'station', width: 120, headerFilter: true },
      { title: 'Status', field: 'status', width: 100, headerFilter: true },
      {
        title: 'Actions',
        field: 'id',
        width: 200,
        formatter: (cell) => {
          const id = cell.getValue();
          return '<a href="/admin/categories/edit/' + id + '" class="button-outline action-button">Edit</a> ' +
            '<form action="/admin/categories/deactivate/' + id + '" method="POST" class="inline-form">' +
            '<input type="hidden" name="csrf_token" value="' + csrfToken + '">' +
            '<button type="submit" class="button-danger action-button" onclick="return confirm(\'Are you sure?\')">Deactivate</button>' +
            '</form>';
        },
      },
    ],
    rowClick: (e, row) => {
      if (e.target.closest('a, form, button, input')) return;
      window.location.href = '/admin/categories/edit/' + row.getData().id;
    },
  });
</script>