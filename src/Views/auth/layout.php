<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RMS</title>
    <link rel="stylesheet" href="/assets/css/auth.css">
</head>
<body>
<main><?= $slot ?></main>

<script>
    function switchTab(event,tabId) {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.form-panel').forEach(p => p.classList.remove('active'));
        event.target.classList.add('active');
        document.getElementById('panel-' + tabId).classList.add('active');
    }
    const pinInput = document.getElementById('pin-input');
    function addPin(num) {
        if (pinInput.value.length < 4) {
            pinInput.value += num;
            if (pinInput.value.length === 4) {
                pinInput.form.submit();
            }
        }
    }
    function clearPin() { pinInput.value = ''; }
    function deletePin() { pinInput.value = pinInput.value.slice(0, -1); }
</script>
</body>
</html>
