<h1>User List</h1>

<!--
<?php if (!empty($flash)): ?>
  <div class="flash"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>
-->

<a href="/users/create">Create New User</a>

<table>
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
    <?php if (!empty($users)) {
      foreach ($users as $user): ?>
        <tr>
          <td><?= $user['id'] ?></td>
          <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
          <td><?= htmlspecialchars($user['role_name'] ?? 'Not assigned') ?></td>
          <td><?= htmlspecialchars($user['employee_num']) ?></td>
          <td>
            <a href="/users/update/<?= $user['id'] ?>">Edit</a>
            |
            <form action="/users/deactivate/<?= $user['id'] ?>" method="POST" style="display:inline;">
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
