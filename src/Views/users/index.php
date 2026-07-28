<h1>User List</h1>

<a href="/users/create" class="button button-primary">+ Create New User</a>

<table class="users-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Role</th>
      <th>Employee Num</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php

    if (!empty($users)) {
        foreach ($users as $user) : ?>
        <tr>
          <td><?= $user->id ?></td>
          <td><?= htmlspecialchars($user->getFullName()) ?></td>
          <td><?= htmlspecialchars($user->roleName ?? 'Not assigned') ?></td>
          <td><?= htmlspecialchars($user->employeeNum) ?></td>
          <td>
            <a href="/users/update/<?= $user->id ?>" class="button-outline">Edit</a>
            <form action="/users/deactivate/<?= $user->id ?>" method="POST">
              <button type="submit" onclick="return confirm('Are you sure?')">Deactivate</button>
            </form>
          </td>
        </tr>
        <?php endforeach;
    } else {
        echo '<tr><td colspan="5">No users found.</td></tr>';
    } ?>
  </tbody>
</table>
