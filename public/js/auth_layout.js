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
document.addEventListener('DOMContentLoaded', () => {
    const error = document.querySelector('.error');
    if (!error || error.querySelector('.dismiss')) {
        return;
    }
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'dismiss';
    button.setAttribute('aria-label', 'Dismiss');
    button.innerHTML = '&times;';
    button.addEventListener('click', () => {
        error.classList.add('fading');
        setTimeout(() => error.remove(), 250);
    });
    error.appendChild(button);
    setTimeout(() => {
        error.classList.add('fading');
        setTimeout(() => error.remove(), 250);
    }, 8000);
});
