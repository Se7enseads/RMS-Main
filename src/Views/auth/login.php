<?php
$title = "Login - RMS app"
?>

<div class="login-container">
    <h2>RMS Login</h2>

    <?php if (!empty($error)) : ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="tabs">
        <button class="tab active" onclick="switchTab(event,'pin')">
            Fast Login (PIN)
        </button>
        <button class="tab" onclick="switchTab(event,'password')">
            Back Office
        </button>
    </div>

    <!-- PIN Login -->
    <div id="panel-pin" class="form-panel active">
        <form action="/login" method="POST">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
            <input type="hidden" name="login_type" value="pin">
            <div class="form-group">
                <input type="password" name="pin" id="pin-input" placeholder="Enter 4-digit PIN" readonly required>
            </div>

            <div class="keypad">
                <?php for ($i = 1; $i <= 9; $i++) : ?>
                    <button type="button" class="key" onclick="addPin('<?= $i ?>')"><?= $i ?></button>
                <?php endfor ?>
                <button type="button" class="key" onclick="clearPin()">C</button>
                <button type="button" class="key" onclick="addPin('0')">0</button>
                <button type="button" class="key" onclick="deletePin()">⌫</button>
            </div>
        </form>
    </div>

    <!-- Password Login -->
    <div id="panel-password" class="form-panel">
        <form action="/login" method="POST">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
            <input type="hidden" name="login_type" value="password">
            <div class="form-group">
                <label>Employee Number</label>
                <input autofocus type="text" name="employee_num" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</div>
