<h1>Add Stock &mdash; <?= htmlspecialchars($ingredient->name) ?></h1>

<div class="user-summary">
  <strong>Current stock:</strong> <?= number_format($ingredient->stock, 3) ?> <?= $ingredient->baseUnit ?>
  (<?= number_format($ingredient->stockInReceiveUnits(), 3) ?> <?= $ingredient->receiveUnit ?>)
  <br>
  <strong>Cost per <?= $ingredient->baseUnit ?>:</strong> KES <?= number_format($ingredient->costPerUnit, 4) ?>
</div>

<?php if (!empty($errors)) : ?>
<div class="errors">
  <ul>
    <?php foreach ($errors as $msg) : ?>
    <li><?= htmlspecialchars($msg) ?></li>
    <?php endforeach ?>
  </ul>
</div>
<?php endif ?>

<form action="/store/inventory/stock/<?= $ingredient->id ?>" method="POST">
  <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">

  <label>Quantity
    <input type="number" name="quantity" step="0.01" min="0"
           value="<?= htmlspecialchars($old['quantity'] ?? '') ?>" required>
  </label>

  <label>Unit
    <select name="unit">
      <option value="<?= $ingredient->receiveUnit ?>" selected><?= $ingredient->receiveUnit ?></option>
    </select>
    <small>Stock is received in <?= $ingredient->receiveUnit ?>.
      <?php if ($ingredient->isContainerUnit()) : ?>
        Each <?= $ingredient->receiveUnit ?> contains <?= number_format((float) $ingredient->unitsPerContainer, 0) ?> <?= $ingredient->baseUnit ?> and is converted automatically.
      <?php endif ?>
    </small>
  </label>

  <label>Cost per <?= $ingredient->receiveUnit ?> (KES)
    <input type="number" name="unit_cost" step="0.01" min="0"
           value="<?= htmlspecialchars($old['unit_cost'] ?? '') ?>" required>
  </label>

  <button type="submit">Add Stock</button>
</form>

<a href="/store" class="button-outline" style="margin-top: 12px; display: inline-block;">
  ← Back to Store
</a>