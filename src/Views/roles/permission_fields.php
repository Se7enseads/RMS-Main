<h3>Permissions</h3>
<p class="muted">Select the permissions this role can perform.</p>

<?php
    $grouped = [];
    foreach ($permissions as $permission) {
        $module = explode('.', $permission['name'])[0];
        $grouped[$module][] = $permission;
    }
    ksort($grouped);
?>

<?php if (empty($permissions)) : ?>
  <p class="muted">No permissions defined yet.</p>
<?php else : ?>
  <div class="permission-groups">
    <?php foreach ($grouped as $module => $modulePermissions) : ?>
      <fieldset class="permission-group">
        <legend><?= htmlspecialchars(ucfirst($module)) ?></legend>
        <?php foreach ($modulePermissions as $permission) : ?>
          <label class="checkbox-label">
            <input type="checkbox" name="permissions[]" value="<?= $permission['id'] ?>" <?= in_array($permission['id'], $selectedIds, true) ? 'checked' : '' ?>>
            <?= htmlspecialchars($permission['name']) ?>
          </label>
        <?php endforeach ?>
      </fieldset>
    <?php endforeach ?>
  </div>
<?php endif ?>