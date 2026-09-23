(() => {
    const drawer = document.getElementById('roh-category-drawer');
    const trigger = document.querySelector('[data-category-open]');
    if (!drawer || !trigger) return;
    const mobile = matchMedia('(max-width: 767px)');
    let closing;
    const close = () => {
        if (!drawer.open || closing) return;
        drawer.classList.remove('is-open');
        closing = setTimeout(() => {
            drawer.close();
            closing = null;
        }, matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 220);
    };
    trigger.addEventListener('click', () => {
        if (!mobile.matches) return;
        clearTimeout(closing);
        closing = null;
        if (!drawer.open) drawer.showModal();
        drawer.getBoundingClientRect();
        drawer.classList.add('is-open');
        trigger.setAttribute('aria-expanded', 'true');
    });
    drawer.querySelector('[data-category-close]').addEventListener('click', close);
    drawer.addEventListener('cancel', event => { event.preventDefault(); close(); });
    drawer.addEventListener('click', event => {
        if (event.target !== drawer) return;
        const bounds = drawer.getBoundingClientRect();
        if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) close();
    });
    drawer.addEventListener('close', () => {
        drawer.classList.remove('is-open');
        trigger.setAttribute('aria-expanded', 'false');
        if (mobile.matches) trigger.focus();
    });
    mobile.addEventListener('change', () => { if (!mobile.matches) close(); });
})();
