(() => {
    // 1. Mobile Drawer Navigation
    const drawer = document.getElementById('roh-category-drawer');
    const mobile = matchMedia('(max-width: 767px)');
    let closing = null;

    const openDrawer = () => {
        if (!drawer || !mobile.matches) return;
        clearTimeout(closing);
        closing = null;
        if (!drawer.open) drawer.showModal();
        drawer.getBoundingClientRect(); // force reflow
        drawer.classList.add('is-open');
        document.querySelectorAll('[data-category-open]').forEach(btn => btn.setAttribute('aria-expanded', 'true'));
        closeSearch();
    };

    const closeDrawer = () => {
        if (!drawer || !drawer.open || closing) return;
        drawer.classList.remove('is-open');
        closing = setTimeout(() => {
            drawer.close();
            closing = null;
        }, matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 220);
        document.querySelectorAll('[data-category-open]').forEach(btn => btn.setAttribute('aria-expanded', 'false'));
    };

    // Global click listener for drawer triggers
    document.addEventListener('click', event => {
        const openTrigger = event.target.closest('[data-category-open]');
        if (openTrigger) {
            event.preventDefault();
            openDrawer();
            return;
        }

        const closeTrigger = event.target.closest('[data-category-close]');
        if (closeTrigger) {
            event.preventDefault();
            closeDrawer();
            return;
        }

        // When clicking a real navigation link inside the drawer, close smoothly
        if (drawer && drawer.open) {
            const navLink = event.target.closest('#roh-category-drawer a');
            if (navLink && navLink.getAttribute('href') && !navLink.getAttribute('href').startsWith('#')) {
                drawer.classList.remove('is-open');
            }
        }
    });

    if (drawer) {
        drawer.addEventListener('cancel', event => {
            event.preventDefault();
            closeDrawer();
        });
        drawer.addEventListener('click', event => {
            if (event.target !== drawer) return;
            const bounds = drawer.getBoundingClientRect();
            if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) {
                closeDrawer();
            }
        });
        drawer.addEventListener('close', () => {
            drawer.classList.remove('is-open');
            document.querySelectorAll('[data-category-open]').forEach(btn => btn.setAttribute('aria-expanded', 'false'));
        });
    }

    mobile.addEventListener('change', () => {
        if (!mobile.matches) {
            closeDrawer();
            closeSearch();
        }
    });

    // 2. Mobile Search Bar Dropdown Toggle
    const searchForm = document.querySelector('.roh-header__search');
    const searchInput = document.getElementById('roh-search-input');

    const openSearch = () => {
        if (!searchForm) return;
        searchForm.classList.add('is-open');
        const toggleBtn = document.querySelector('[data-search-toggle]');
        if (toggleBtn) {
            toggleBtn.classList.add('is-active');
            const icon = toggleBtn.querySelector('i');
            if (icon) {
                icon.className = 'fa-solid fa-xmark';
            }
        }
        if (searchInput) {
            setTimeout(() => searchInput.focus(), 80);
        }
    };

    const closeSearch = () => {
        if (!searchForm) return;
        searchForm.classList.remove('is-open');
        const toggleBtn = document.querySelector('[data-search-toggle]');
        if (toggleBtn) {
            toggleBtn.classList.remove('is-active');
            const icon = toggleBtn.querySelector('i');
            if (icon) {
                icon.className = 'fa-solid fa-magnifying-glass';
            }
        }
    };

    const toggleSearch = () => {
        if (!searchForm) return;
        if (searchForm.classList.contains('is-open')) {
            closeSearch();
        } else {
            openSearch();
        }
    };

    document.addEventListener('click', event => {
        const toggleBtn = event.target.closest('[data-search-toggle]');
        if (toggleBtn) {
            event.preventDefault();
            toggleSearch();
            return;
        }

        // Close search if clicking outside of the search box and not on toggle button
        if (searchForm && searchForm.classList.contains('is-open')) {
            if (!event.target.closest('.roh-header__search') && !event.target.closest('[data-search-toggle]')) {
                closeSearch();
            }
        }
    });

    // 3. Center Active Category Pill in Mobile Nav Strip
    const centerActiveCategory = () => {
        const activePill = document.querySelector('.roh-category-nav a.is-active');
        if (!activePill) return;
        const navInner = activePill.closest('.roh-category-nav__inner');
        if (!navInner) return;
        requestAnimationFrame(() => {
            const pillLeft = activePill.offsetLeft;
            const pillWidth = activePill.offsetWidth;
            const containerWidth = navInner.clientWidth;
            navInner.scrollLeft = Math.max(0, pillLeft - (containerWidth / 2) + (pillWidth / 2));
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', centerActiveCategory);
    } else {
        centerActiveCategory();
    }
})();

