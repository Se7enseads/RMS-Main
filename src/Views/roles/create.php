<h1>Create Role</h1>

<?php if (!empty($errors)) : ?>
<div class="errors">
  <ul>
    <?php foreach ($errors as $msg) : ?>
    <li><?= htmlspecialchars($msg) ?></li>
    <?php endforeach ?>
  </ul>
</div>
<?php endif ?>

<form action="/admin/roles/create" method="POST" class="wide-form">
  <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
  <label>Role Name
    <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
  </label>

  <?php
    $selectedIds = $old['permissions'] ?? [];
    include __DIR__ . '/permission_fields.php';
  ?>

  <button type="submit">Create</button>
</form>

<a href="/admin/roles" class="button-outline" style="margin-top: 12px; display: inline-block;">← Back to Roles</a>