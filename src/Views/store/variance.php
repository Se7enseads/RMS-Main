<h1><?= $scope === 'BAR' ? 'Bar Variance' : 'Variance' ?></h1>

<p class="muted">
  Discrepancies between the counted and the system stock, valued at the weighted-average cost.
  A negative variance means stock is missing (shrinkage, spillage, theft); a positive one means surplus.
</p>

<?php if (empty($takes)) : ?>
  <p class="muted">No stock takes recorded yet.
    <a href="<?= $scope === 'BAR' ? '/store/stocktake/bar' : '/store/stocktake' ?>">Start a stock take</a>.
  </p>
<?php endif ?>

<?php foreach ($takes as $take) : ?>
<section class="user-summary">
  <h3 style="margin: 0 0 8px;">
    <?= htmlspecialchars($take['take_date']) ?>
    &mdash; <?= htmlspecialchars($take['staff_name']) ?>
    <span class="muted">(<?= $take['scope'] === 'BAR' ? 'Bar' : 'All' ?> &middot; <?= (int) $take['item_count'] ?> item<?= (int) $take['item_count'] === 1 ? '' : 's' ?>)</span>
  </h3>
  <p style="margin: 0 0 12px;">
    <strong>Total variance: KES <?= number_format((float) $take['total_variance'], 2) ?></strong>
  </p>

  <table class="plain-table">
    <thead>
      <tr>
        <th>Ingredient</th>
        <th class="num">System</th>
        <th class="num">Counted</th>
        <th class="num">Variance</th>
        <th class="num">Variance (KES)</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($takeItems[$take['id']] ?? [] as $item) : ?>
      <?php $variance = (float) $item['variance_qty']; ?>
      <tr style="<?= $variance == 0.0 ? 'opacity: 0.55;' : '' ?>">
        <td><?= htmlspecialchars($item['name']) ?></td>
        <td class="num"><?= number_format((float) $item['system_qty'], 3) ?> <?= htmlspecialchars($item['base_unit']) ?>
          <span class="muted">(<?= number_format((float) $item['system_qty'] / ($item['units_per_container'] ?: 1), 3) ?> <?= htmlspecialchars($item['receive_unit']) ?>)</span></td>
        <td class="num"><?= number_format((float) $item['counted_qty'], 3) ?> <?= htmlspecialchars($item['base_unit']) ?></td>
        <td class="num" style="color: <?= $variance < 0 ? 'var(--danger)' : ($variance > 0 ? 'var(--secondary)' : 'inherit') ?>;">
          <?= $variance > 0 ? '+' : '' ?><?= number_format($variance, 3) ?> <?= htmlspecialchars($item['base_unit']) ?></td>
        <td class="num" style="color: <?= (float) $item['variance_value'] < 0 ? 'var(--danger)' : ((float) $item['variance_value'] > 0 ? 'var(--secondary)' : 'inherit') ?>;">
          KES <?= number_format((float) $item['variance_value'], 2) ?></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</section>
<?php endforeach ?>

<a href="<?= $scope === 'BAR' ? '/store/stocktake/bar' : '/store/stocktake' ?>" class="button button-primary">
  <?= $scope === 'BAR' ? 'New Bar Stock Take' : 'New Stock Take' ?>
</a>