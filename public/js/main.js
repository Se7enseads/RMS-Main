document.addEventListener('DOMContentLoaded', () => {
    const path = location.pathname;

    let active = null;
    for (const link of document.querySelectorAll('a.sidebar-link[href]')) {
        const href = link.getAttribute('href');
        if (path === href || path.startsWith(href.replace(/\/+$/, '') + '/')) {
            if (active === null || href.length > active.getAttribute('href').length) {
                active = link;
            }
        }
    }

    if (active !== null) {
        active.classList.add('active');
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const hide = (alert) => {
        alert.classList.add('fading');
        setTimeout(() => alert.remove(), 250);
    };

    const dismiss = (alert) => {
        if (alert.querySelector('.dismiss')) {
            return;
        }
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'dismiss';
        button.setAttribute('aria-label', 'Dismiss');
        button.innerHTML = '&times;';
        button.addEventListener('click', () => hide(alert));
        alert.appendChild(button);
        setTimeout(() => hide(alert), 8000);
    };

    document.querySelectorAll('.errors, .error').forEach(dismiss);
});
