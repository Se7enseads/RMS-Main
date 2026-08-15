<h1>Create Menu Item</h1>

<?php if (!empty($errors)) : ?>
<div class="errors">
  <ul>
    <?php foreach ($errors as $msg) : ?>
    <li><?= htmlspecialchars($msg) ?></li>
    <?php endforeach ?>
  </ul>
</div>
<?php endif ?>

<form action="/items/create" method="POST">
  <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
  <label>Name
    <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>" required>
  </label>

  <label>Description
    <textarea name="description" rows="3"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
  </label>

  <label>Price (KES)
    <input type="number" name="price" step="0.01" min="0" value="<?= htmlspecialchars($old['price'] ?? '') ?>" required>
  </label>

  <label>Category
    <select name="category_id">
      <option value="">Uncategorized</option>
      <?php foreach ($categories as $category) : ?>
        <option value="<?= $category->id ?>" <?= ($old['category_id'] ?? '') == $category->id ? 'selected' : '' ?>>
          <?= htmlspecialchars($category->name) ?>
        </option>
      <?php endforeach ?>
    </select>
  </label>

  <label class="checkbox-label">
    <input type="checkbox" name="is_combo" value="1" <?= !empty($old['is_combo']) ? 'checked' : '' ?>>
    Is a combo
  </label>

  <button type="submit">Create</button>
</form>

<a href="/items" class="button-outline" style="margin-top: 12px; display: inline-block;">
  ← Back to Items
</a>
