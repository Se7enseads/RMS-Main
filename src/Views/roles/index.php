<h1>Role List</h1>

<a href="/roles/create">Create New Role</a>

<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
    </tr>
  </thead>
  <tbody>
    <?php

    if (!empty($roles)) {
        foreach ($roles as $role) : ?>
        <tr>
          <td><?= $role->id ?></td>
          <td><?= htmlspecialchars($role->name) ?></td>
        </tr>
        <?php endforeach;
    } else {
        echo '<tr><td colspan="2">No roles found.</td></tr>';
    } ?>
  </tbody>
</table>
