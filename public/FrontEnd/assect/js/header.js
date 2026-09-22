(() => {
    const header = document.querySelector('[data-roh-header]');
    if (!header) return;

    const menu = header.querySelector('[data-roh-menu]');
    const search = header.querySelector('[data-roh-search]');
    const searchToggle = search.querySelector('summary');
    const searchInput = search.querySelector('input[type="search"]');

    const setMenuOpen = (open) => {
        header.classList.toggle('is-menu-open', open);
        menu.setAttribute('aria-expanded', String(open));
    };

    header.setAttribute('data-enhanced', '');

    menu.addEventListener('click', () => {
        search.open = false;
        setMenuOpen(menu.getAttribute('aria-expanded') !== 'true');
    });

    search.addEventListener('toggle', () => {
        if (search.open) {
            setMenuOpen(false);
            searchInput.focus();
        }
    });

    document.addEventListener('click', (event) => {
        if (!search.contains(event.target)) search.open = false;
        if (!header.contains(event.target)) setMenuOpen(false);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        if (search.open) {
            search.open = false;
            searchToggle.focus();
        } else if (header.classList.contains('is-menu-open')) {
            setMenuOpen(false);
            menu.focus();
        }
    });

    header.addEventListener('focusout', (event) => {
        if (event.relatedTarget && !header.contains(event.relatedTarget)) {
            search.open = false;
            setMenuOpen(false);
        }
    });

    window.matchMedia('(min-width: 768px)').addEventListener('change', () => setMenuOpen(false));
})();
