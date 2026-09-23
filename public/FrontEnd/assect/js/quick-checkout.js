(() => {
    const dialog = document.getElementById('rf-checkout');
    if (!dialog) return;
    const find = name => dialog.querySelector(`[data-checkout-${name}]`);
    const form = find('form');
    const label = (en, bn) => dialog.dataset.bangla === '1' ? bn : en;
    const money = value => `৳${Number(value).toLocaleString('en-BD', { maximumFractionDigits: 2 })}`;
    const submitText = find('submit').textContent;
    let quote, opener, submitting = false, loading;

    async function request(options = {}) {
        const response = await fetch(dialog.dataset.url, {
            ...options, headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        });
        const data = await response.json();
        if (!response.ok) {
            const error = new Error(Object.values(data.errors || {}).flat()[0] || data.message || label('Please try again.', 'আবার চেষ্টা করুন।'));
            error.fields = data.errors || {};
            throw error;
        }
        return data;
    }

    function totals() {
        if (!quote) return;
        const area = quote.areas.find(area => String(area.id) === form.elements.shipping_id.value);
        find('delivery').textContent = area ? money(area.shipping_charge) : label('Select area', 'এলাকা বাছুন');
        find('total').textContent = money(quote.subtotal - quote.discount + Number(area?.shipping_charge || 0));
    }

    async function load() {
        loading?.abort();
        loading = new AbortController();
        const current = loading;
        find('status').textContent = label('Loading your order…', 'আপনার অর্ডার লোড হচ্ছে…');
        find('retry').hidden = true;
        form.hidden = true;
        try {
            quote = await request({ signal: current.signal });
            const areaValue = form.elements.shipping_id.value;
            form.elements.shipping_id.replaceChildren(new Option(label('Select delivery area', 'ডেলিভারি এলাকা বাছুন'), ''));
            quote.areas.forEach(area => {
                const name = Number(area.type) === 1 ? label('Inside Dhaka', 'ঢাকার ভিতরে') : label('Outside Dhaka', 'ঢাকার বাইরে');
                form.elements.shipping_id.add(new Option(`${name} · ${area.name} — ${money(area.shipping_charge)}`, area.id));
            });
            if (quote.areas.some(area => String(area.id) === areaValue)) form.elements.shipping_id.value = areaValue;
            if (quote.customer) ['name', 'phone', 'address'].forEach(field => { if (!form.elements[field].value) form.elements[field].value = quote.customer[field] || ''; });
            find('items').replaceChildren();
            quote.items.forEach(item => {
                const row = document.createElement('li');
                const name = document.createElement('span');
                name.textContent = `${item.name}${item.variant ? ` · ${item.variant}` : ''} × ${item.quantity}`;
                const total = document.createElement('strong');
                total.textContent = money(item.total);
                row.append(name, total);
                find('items').append(row);
            });
            find('subtotal').textContent = money(quote.subtotal);
            find('discount-row').hidden = !quote.discount;
            find('discount').textContent = `−${money(quote.discount)}`;
            totals();
            find('status').textContent = quote.areas.length ? '' : label('No delivery areas are available right now.', 'এখন কোনো ডেলিভারি এলাকা পাওয়া যাচ্ছে না।');
            find('submit').disabled = !quote.areas.length;
            form.hidden = false;
            form.elements.name.focus();
        } catch (error) {
            if (error.name === 'AbortError') return;
            find('status').textContent = error.message;
            find('retry').hidden = false;
        }
    }

    function open(trigger) {
        if (dialog.open) return;
        opener = trigger || document.activeElement;
        document.querySelectorAll('#rf-cart-sidebar[open], #rf-product-dialog[open]').forEach(modal => modal.close());
        dialog.showModal();
        load();
    }
    function close() { if (!submitting) dialog.close(); }
    document.addEventListener('checkout:open', () => open());
    document.addEventListener('click', event => {
        const link = event.target.closest('a[href], [data-checkout-open]');
        if (!link || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button) return;
        const url = link.href ? new URL(link.href, location.href) : null;
        const target = new URL(dialog.dataset.checkoutPath);
        if (!link.hasAttribute('data-checkout-open') && (!url || url.origin !== target.origin || url.pathname.replace(/\/$/, '') !== target.pathname)) return;
        event.preventDefault();
        open(link);
    });
    dialog.addEventListener('click', event => {
        if (event.target.closest('[data-checkout-close]')) close();
        if (event.target === dialog) {
            const bounds = dialog.getBoundingClientRect();
            if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) close();
        }
    });
    dialog.addEventListener('cancel', event => { event.preventDefault(); close(); });
    dialog.addEventListener('close', () => { loading?.abort(); opener?.focus(); });
    find('retry').addEventListener('click', load);
    form.elements.shipping_id.addEventListener('change', totals);
    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (submitting || !quote || !form.reportValidity()) return;
        submitting = true;
        find('submit').disabled = true;
        find('submit').textContent = label('Placing order…', 'অর্ডার করা হচ্ছে…');
        find('status').textContent = '';
        dialog.setAttribute('aria-busy', 'true');
        try {
            const payload = Object.fromEntries(new FormData(form));
            payload.token = quote.token;
            const data = await request({ method: 'POST', body: JSON.stringify(payload) });
            document.querySelectorAll('.cartQty').forEach(node => { node.textContent = '0'; });
            window.location.assign(data.invoice_url);
        } catch (error) {
            find('status').textContent = error.message;
            const field = Object.keys(error.fields || {})[0];
            (form.elements[field] || find('status')).focus();
        } finally {
            submitting = false;
            find('submit').disabled = false;
            find('submit').textContent = submitText;
            dialog.removeAttribute('aria-busy');
        }
    });
    if (location.hash === '#checkout') {
        history.replaceState(null, '', location.pathname + location.search);
        open();
    }
})();
