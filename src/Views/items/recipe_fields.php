<h3>Recipe (ingredients)</h3>
<p class="muted">Check the ingredients this item is made from and enter the quantity per serving. Cost is calculated from current ingredient costs.</p>

<?php if (empty($ingredients)) : ?>
  <p class="muted">No ingredients yet. <a href="/store/ingredients/create">Create ingredients in the Store</a> first.</p>
<?php else : ?>
  <?php
    $recipeTotal = 0.0;
  ?>
  <table class="plain-table">
    <thead>
      <tr>
        <th></th>
        <th>Ingredient</th>
        <th>Quantity per serving</th>
        <th>Unit</th>
        <th>Cost / unit (KES)</th>
        <th>Cost per serving (KES)</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($ingredients as $ingredient) :
        $qty = (float) ($recipeQty[$ingredient->id] ?? 0);
        $lineCost = $qty * $ingredient->costPerUnit;
        $recipeTotal += $lineCost;
      ?>
        <tr>
          <td>
            <input type="checkbox" name="ingredient_id[]" value="<?= $ingredient->id ?>"
                   <?= $qty > 0 ? 'checked' : '' ?>>
          </td>
          <td><?= htmlspecialchars($ingredient->name) ?></td>
          <td>
            <input type="number" name="quantity[<?= $ingredient->id ?>]" step="0.01" min="0"
                   value="<?= $qty > 0 ? $qty : '' ?>" style="width: 100px;">
          </td>
          <td><?= $ingredient->baseUnit ?></td>
          <td class="num"><?= number_format($ingredient->costPerUnit, 4) ?></td>
          <td class="num"><?= number_format($lineCost, 2) ?></td>
        </tr>
      <?php endforeach ?>
      <tr>
        <td colspan="5" style="text-align: right;"><strong>Total ingredient cost per serving:</strong></td>
        <td class="num"><strong><?= number_format($recipeTotal, 2) ?></strong></td>
      </tr>
    </tbody>
  </table>
<?php endif ?>