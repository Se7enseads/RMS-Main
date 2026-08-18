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
