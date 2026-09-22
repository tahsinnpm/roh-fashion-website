(() => {
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    document.querySelectorAll('[data-home-rail]').forEach((rail, index) => {
        rail.id ||= `home-rail-${index}`;
        rail.tabIndex = 0;
        const controls = document.createElement('div');
        controls.className = 'rf-rail-controls';
        const buttons = [-1, 1].map((direction) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = direction < 0 ? '←' : '→';
            button.setAttribute('aria-label', direction < 0 ? 'Previous items' : 'Next items');
            button.setAttribute('aria-controls', rail.id);
            button.addEventListener('click', () => rail.scrollBy({ left: direction * rail.clientWidth, behavior: reducedMotion.matches ? 'auto' : 'smooth' }));
            controls.append(button);
            return button;
        });
        rail.after(controls);
        const update = () => {
            const maximum = rail.scrollWidth - rail.clientWidth;
            controls.hidden = maximum < 2;
            buttons[0].disabled = rail.scrollLeft <= 1;
            buttons[1].disabled = rail.scrollLeft >= maximum - 1;
        };
        rail.addEventListener('scroll', update, { passive: true });
        new ResizeObserver(update).observe(rail);
        update();
    });

    const timer = document.querySelector('[data-countdown-end]');
    if (timer) {
        const start = Number(timer.dataset.countdownStart);
        const end = Number(timer.dataset.countdownEnd);
        const update = () => {
            const remaining = Math.max(0, Math.floor(((Date.now() < start ? start : end) - Date.now()) / 1000));
            timer.textContent = remaining > 0 ? `${Math.floor(remaining / 86400)}d ${Math.floor(remaining / 3600) % 24}h ${Math.floor(remaining / 60) % 60}m ${remaining % 60}s` : 'Expired';
            return remaining;
        };
        if (update()) {
            const interval = setInterval(() => { if (!update()) clearInterval(interval); }, 1000);
        }
    }

    const more = document.querySelector('#load-more-btn');
    if (!more) return;
    let offset = Number(more.dataset.offset);
    const bangla = more.dataset.bangla === '1';
    const originalLabel = more.textContent.trim();
    more.addEventListener('click', async () => {
        if (more.disabled) return;
        more.disabled = true;
        more.setAttribute('aria-busy', 'true');
        more.textContent = bangla ? 'লোড হচ্ছে…' : 'Loading…';
        try {
            const url = new URL(more.dataset.url);
            url.searchParams.set('offset', offset);
            url.searchParams.set('count', 8);
            const response = await fetch(url, { headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error('Unable to load products');
            const data = await response.json();
            document.querySelector('#latest-products-container').insertAdjacentHTML('beforeend', data.html);
            offset += data.products.length;
            more.disabled = data.products.length < 8;
            more.textContent = more.disabled ? (bangla ? 'সব পণ্য দেখানো হয়েছে' : 'All products shown') : originalLabel;
        } catch {
            more.disabled = false;
            more.textContent = bangla ? 'আবার চেষ্টা করুন' : 'Try again';
        } finally { more.removeAttribute('aria-busy'); }
    });
})();
