<h1>Create Category</h1>

<?php if (!empty($errors)) : ?>
<div class="errors">
  <ul>
    <?php foreach ($errors as $msg) : ?>
    <li><?= htmlspecialchars($msg) ?></li>
    <?php endforeach ?>
  </ul>
</div>
<?php endif ?>

<form action="/admin/categories/create" method="POST">
  <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">

  <label>Name
    <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
  </label>

  <label>Station
    <select name="station">
      <option value="KITCHEN" <?= ($old['station'] ?? '') === 'KITCHEN' ? 'selected' : '' ?>>Kitchen</option>
      <option value="BAR" <?= ($old['station'] ?? '') === 'BAR' ? 'selected' : '' ?>>Bar</option>
    </select>
  </label>

  <button type="submit">Create</button>
</form>

<a href="/admin/categories" class="button-outline" style="margin-top: 12px; display: inline-block;">
  ← Back to Categories
</a>