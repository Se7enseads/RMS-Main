<h1>Users</h1>

<a href="/admin/users/create" class="button button-primary">+ Create New User</a>

<div class="report-toolbar">
    <button type="button" class="button button-outline" onclick="tableUsers.print(true)">Print</button>
</div>

<div id="users-table"></div>

<script>
  const usersData = <?= json_encode(array_map(fn($u) => [
      'id' => $u->id,
      'name' => $u->getFullName(),
      'role' => $u->roleName ?? 'Not assigned',
      'employeeNum' => $u->employeeNum,
      'status' => $u->active ? 'Active' : 'Inactive',
  ], $users)) ?>;
  const csrfToken = <?= json_encode(\App\Core\Session::csrfToken()) ?>;

  const tableUsers = new Tabulator('#users-table', {
    data: usersData,
    layout: 'fitColumns',
    pagination: true,
    paginationMode: 'local',
    paginationSize: 15,
    paginationSizeSelector: [10, 15, 25, 50],
    initialSort: [{ column: 'id', dir: 'desc' }],
    columns: [
      { title: 'ID', field: 'id', width: 70, headerFilter: true },
      { title: 'Name', field: 'name', headerFilter: true },
      { title: 'Role', field: 'role', headerFilter: true },
      { title: 'Employee Num', field: 'employeeNum', headerFilter: true },
      { title: 'Status', field: 'status', width: 100, headerFilter: true },
      {
        title: 'Actions',
        field: 'id',
        width: 200,
        print: false,
        formatter: (cell) => {
          const id = cell.getValue();
          return '<a href="/admin/users/update/' + id + '" class="button-outline action-button">Edit</a> ' +
            '<form action="/admin/users/deactivate/' + id + '" method="POST" class="inline-form">' +
            '<input type="hidden" name="csrf_token" value="' + csrfToken + '">' +
            '<button type="submit" class="button-danger action-button" onclick="return confirm(\'Are you sure?\')">Deactivate</button>' +
            '</form>';
        },
      },
    ],
    rowClick: (e, row) => {
      if (e.target.closest('a, form, button, input')) return;
      window.location.href = '/admin/users/' + row.getData().id + '/activity';
    },
  });
</script>