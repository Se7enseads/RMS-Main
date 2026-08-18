<h1>Role List</h1>

<a href="/admin/roles/create" class="button button-primary">+ Create New Role</a>

<div id="roles-table"></div>

<script>
  const rolesData = <?= json_encode(array_map(fn($r) => [
      'id' => $r->id,
      'name' => $r->name,
      'status' => $r->active ? 'Active' : 'Inactive',
  ], $roles)) ?>;
  const csrfToken = <?= json_encode(\App\Core\Session::csrfToken()) ?>;

  new Tabulator('#roles-table', {
    data: rolesData,
    layout: 'fitColumns',
    pagination: true,
    paginationMode: 'local',
    paginationSize: 15,
    paginationSizeSelector: [10, 15, 25, 50],
    initialSort: [{ column: 'id', dir: 'desc' }],
    columns: [
      { title: 'ID', field: 'id', width: 70, headerFilter: true },
      { title: 'Name', field: 'name', headerFilter: true },
      { title: 'Status', field: 'status', width: 100, headerFilter: true },
      {
        title: 'Actions',
        field: 'id',
        width: 200,
        formatter: (cell) => {
          const id = cell.getValue();
          return '<a href="/admin/roles/edit/' + id + '" class="button-outline action-button">Edit</a> ' +
            '<form action="/admin/roles/deactivate/' + id + '" method="POST" class="inline-form">' +
            '<input type="hidden" name="csrf_token" value="' + csrfToken + '">' +
            '<button type="submit" class="button-danger action-button" onclick="return confirm(\'Are you sure?\')">Deactivate</button>' +
            '</form>';
        },
      },
    ],
    rowClick: (e, row) => {
      if (e.target.closest('a, form, button, input')) return;
      window.location.href = '/admin/roles/edit/' + row.getData().id;
    },
  });
</script>