<h1>Create User</h1>

<?php if (!empty($errors)) : ?>
<div class="errors">
  <ul>
    <?php foreach ($errors as $field => $msg) : ?>
    <li><?= htmlspecialchars($msg) ?></li>
    <?php endforeach ?>
  </ul>
</div>
<?php endif ?>

<form action="/users/create" method="POST">
  <!-- TODO: Errors from sql -->

  <label>Employee Number
    <input type="text" name="employee_num" value="<?= htmlspecialchars($old['employee_num'] ?? '') ?>" required>
  </label>

  <label>First Name
    <input type="text" name="first_name" value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" required>
  </label>

  <label>Middle Name
    <input type="text" name="middle_name" value="<?= htmlspecialchars($old['middle_name'] ?? '') ?>">
  </label>

  <label>Last Name
    <input type="text" name="last_name" value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" required>
  </label>

  <label>National ID
    <input type="text" name="national_id" value="<?= htmlspecialchars($old['national_id'] ?? '') ?>" required>
  </label>

  <label>PIN
    <input type="text" name="pin" value="<?= htmlspecialchars($old['pin'] ?? '') ?>" required>
  </label>

  <label>Phone Number
    <input type="tel" name="phone_number" value="<?= htmlspecialchars($old['phone_number'] ?? '') ?>">
  </label>

  <label>Password
    <input type="password" name="password" required>
  </label>

  <label>Role
    <select name="role_id" required>
      <option value="">Select a role</option>
      <?php foreach ($roles as $role) : ?>
          <option value="<?= $role->id ?>" <?= ($old['role_id'] ?? '') == $role->id ? 'selected' : '' ?>>
            <?= htmlspecialchars($role->name) ?>
          </option>
      <?php endforeach ?>
    </select>
  </label>

  <button type="submit">Create</button>
</form>

<a href="/users" class="button-outline" style="margin-top: 12px; display: inline-block;">
  ← Back to Users
</a>
