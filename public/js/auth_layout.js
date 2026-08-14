function switchTab(event, tabId) {
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

function clearPin() {
    pinInput.value = '';
}

function deletePin() {
    pinInput.value = pinInput.value.slice(0, -1);
}