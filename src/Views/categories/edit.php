<h1>Edit Category</h1>

<?php if (!empty($errors)) : ?>
<div class="errors">
  <ul>
    <?php foreach ($errors as $msg) : ?>
    <li><?= htmlspecialchars($msg) ?></li>
    <?php endforeach ?>
  </ul>
</div>
<?php endif ?>

<form action="/admin/categories/edit/<?= $category->id ?>" method="POST">
  <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">

  <label>Name
    <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? $category->name) ?>" required>
  </label>

  <label>Station
    <select name="station">
      <option value="KITCHEN" <?= ($old['station'] ?? $category->station) === 'KITCHEN' ? 'selected' : '' ?>>Kitchen</option>
      <option value="BAR" <?= ($old['station'] ?? $category->station) === 'BAR' ? 'selected' : '' ?>>Bar</option>
    </select>
  </label>

  <button type="submit">Save</button>
</form>

<a href="/admin/categories" class="button-outline" style="margin-top: 12px; display: inline-block;">
  ← Back to Categories
</a>