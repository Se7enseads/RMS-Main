<h1>Edit Ingredient</h1>

<?php if (!empty($errors)) : ?>
<div class="errors">
  <ul>
    <?php foreach ($errors as $msg) : ?>
    <li><?= htmlspecialchars($msg) ?></li>
    <?php endforeach ?>
  </ul>
</div>
<?php endif ?>

<form action="/store/inventory/edit/<?= $ingredient->id ?>" method="POST">
  <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">

  <label>Name
    <input type="text" name="name" value="<?= htmlspecialchars($old['name'] ?? $ingredient->name) ?>" required>
  </label>

  <label>Base Unit
    <select name="base_unit">
      <?php foreach (['g' => 'Grams (g)', 'ml' => 'Millilitres (ml)', 'pcs' => 'Pieces (pcs)'] as $value => $label) : ?>
        <option value="<?= $value ?>" <?= ($old['base_unit'] ?? $ingredient->baseUnit) === $value ? 'selected' : '' ?>><?= $label ?></option>
      <?php endforeach ?>
    </select>
  </label>

  <label>Receive Unit
    <select name="receive_unit">
      <?php foreach (['g' => 'Grams (g)', 'ml' => 'Millilitres (ml)', 'pcs' => 'Pieces (pcs)', 'case' => 'Case', 'packet' => 'Packet', 'carton' => 'Carton', 'box' => 'Box'] as $value => $label) : ?>
        <option value="<?= $value ?>" <?= ($old['receive_unit'] ?? $ingredient->receiveUnit) === $value ? 'selected' : '' ?>><?= $label ?></option>
      <?php endforeach ?>
    </select>
  </label>

  <label>Pieces per Container (for cases/packets/cartons/boxes)
    <input type="number" name="units_per_container" step="0.01" min="0"
           value="<?= htmlspecialchars($old['units_per_container'] ?? $ingredient->unitsPerContainer ?? '') ?>"
           placeholder="e.g. 12 bottles per case">
  </label>

  <label>Reorder Level (in base units)
    <input type="number" name="reorder_level" step="0.01" min="0"
           value="<?= htmlspecialchars($old['reorder_level'] ?? $ingredient->reorderLevel) ?>"
           placeholder="e.g. 1000 g">
  </label>

  <button type="submit">Save</button>
</form>

<a href="/store" class="button-outline" style="margin-top: 12px; display: inline-block;">
  ← Back to Store
</a>