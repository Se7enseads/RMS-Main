<h1><?= $scope === 'BAR' ? 'Bar Stock Take' : 'Stock Take' ?></h1>

<p class="muted">Count the physical stock in <strong><?= $scope === 'BAR' ? 'receive' : 'receive' ?></strong> units.
  Leave a row blank to skip it. Saving balances the system stock to the counted amount and records any variance.</p>

<?php if (!empty($errors)) : ?>
<div class="errors">
  <ul>
    <?php foreach ($errors as $msg) : ?>
    <li><?= htmlspecialchars($msg) ?></li>
    <?php endforeach ?>
  </ul>
</div>
<?php endif ?>

<?php if (empty($ingredients)) : ?>
  <p class="muted"><?= $scope === 'BAR'
      ? 'No ingredients are used by bar menu items yet. <a href="/store/inventory/create">Create ingredients</a> and link them to bar items first.'
      : 'No active ingredients. <a href="/store/inventory/create">Create one</a> first.' ?></p>
<?php else : ?>
<form action="/store/stocktake" method="POST" class="wide-form">
  <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
  <input type="hidden" name="scope" value="<?= $scope ?>">

  <label>Take Date
    <input type="date" name="take_date" value="<?= htmlspecialchars($old['take_date'] ?? date('Y-m-d')) ?>" required>
  </label>

  <div class="report-toolbar">
    <button type="button" class="button button-outline" onclick="window.print()">Print</button>
  </div>

  <table class="plain-table">
    <thead>
      <tr>
        <th>Ingredient</th>
        <th class="num">System (base units)</th>
        <th class="num">System (receive units)</th>
        <th>Physical count (receive units)</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($ingredients as $ingredient) : ?>
      <tr>
        <td><?= htmlspecialchars($ingredient->name) ?></td>
        <td class="num"><?= number_format($ingredient->stock, 3) ?> <?= $ingredient->baseUnit ?></td>
        <td class="num"><?= number_format($ingredient->stockInReceiveUnits(), 3) ?> <?= $ingredient->receiveUnit ?></td>
        <td>
          <input type="number" name="count[<?= $ingredient->id ?>]" step="0.01" min="0"
                 value="<?= htmlspecialchars($old['count'][$ingredient->id] ?? '') ?>"
                 placeholder="<?= number_format($ingredient->stockInReceiveUnits(), 3) ?>"
                 style="width: 140px;">
        </td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>

  <button type="submit" class="button button-primary">Save Stock Take</button>
</form>
<?php endif ?>

<a href="/store/inventory" class="button-outline" style="margin-top: 12px; display: inline-block;">← Back to Inventory</a>