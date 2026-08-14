<h1>Menu Items</h1>

<a href="/items/create" class="button button-primary">+ Create New Item</a>

<table class="items-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Category</th>
      <th>Price</th>
      <th>Type</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($items)) : ?>
      <?php foreach ($items as $item) : ?>
        <tr>
          <td><?= $item->id ?></td>
          <td><?= htmlspecialchars($item->name) ?></td>
          <td><?= htmlspecialchars($item->categoryName ?? 'Uncategorized') ?></td>
          <td><?= number_format($item->price, 2) ?></td>
          <td><?= $item->isCombo ? 'Combo' : 'Single' ?></td>
          <td><?= $item->active ? 'Active' : 'Inactive' ?></td>
        </tr>
      <?php endforeach ?>
    <?php else : ?>
      <tr><td colspan="6">No menu items found.</td></tr>
    <?php endif ?>
  </tbody>
</table>
