<h1>Edit User</h1>

<?php use App\Core\Session;

if (!empty($errors)) : ?>
    <div class="errors">
        <ul>
            <?php foreach ($errors as $field => $msg) : ?>
                <li><?= htmlspecialchars($msg) ?></li>
            <?php endforeach ?>
        </ul>
    </div>
<?php endif ?>

<form action="/admin/users/update/<?= $user->id ?>" method="POST">
    <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

    <label>First Name
        <input type="text" name="first_name" value="<?= htmlspecialchars($old['first_name'] ?? $user->firstName) ?>"
               required>
    </label>

    <label>Middle Name
        <input type="text" name="middle_name"
               value="<?= htmlspecialchars($old['middle_name'] ?? $user->middleName ?? '') ?>">
    </label>

    <label>Last Name
        <input type="text" name="last_name" value="<?= htmlspecialchars($old['last_name'] ?? $user->lastName) ?>"
               required>
    </label>

    <label>Password
        <input type="password" name="password" id="password" minlength="12" maxlength="64" required>
    </label>

    <label>PIN
        <input type="text" name="pin" value="<?= htmlspecialchars($old['pin'] ?? $user->pin) ?>" required>
    </label>

    <label>Phone Number
        <input type="tel" name="phone_number"
               value="<?= htmlspecialchars($old['phone_number'] ?? $user->phoneNumber ?? '') ?>">
    </label>

    <label>Role
        <select name="role_id" required>
            <?php foreach ($roles as $role) : ?>
                <option value="<?= $role->id ?>" <?= ($old['role_id'] ?? $user->roleId) == $role->id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($role->name) ?>
                </option>
            <?php endforeach ?>
        </select>
    </label>

    <button type="submit">Save</button>
</form>

<a href="/admin/users" class="button-outline" style="margin-top: 12px; display: inline-block;">
    ← Back to Users
</a>
