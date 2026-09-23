(() => {
    const dialog = document.getElementById('rf-cart-sidebar');
    if (!dialog) return;
    const find = name => dialog.querySelector(`[data-cart-${name}]`);
    const label = (en, bn) => dialog.dataset.bangla === '1' ? bn : en;
    const money = value => `৳${Number(value).toLocaleString('en-BD', { maximumFractionDigits: 2 })}`;
    let busy = false, opener, closeTimer, revision = 0;

    function element(tag, className, text) {
        const node = document.createElement(tag);
        node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    }

    function render(data) {
        find('items').replaceChildren();
        for (const item of data.items) {
            const row = element('li', 'rf-cart__item');
            row.dataset.rowId = item.rowId;
            const imageLink = element(item.url ? 'a' : 'span', 'rf-cart__image');
            if (item.url) imageLink.href = item.url;
            const image = element('img', '');
            image.src = item.image;
            image.alt = item.name;
            image.loading = 'lazy';
            imageLink.append(image);
            const info = element('div', 'rf-cart__info');
            const name = element(item.url ? 'a' : 'span', 'rf-cart__name', item.name);
            if (item.url) name.href = item.url;
            info.append(name);
            if (item.variant) info.append(element('p', 'rf-cart__variant', item.variant));
            info.append(element('strong', 'rf-cart__price', money(item.total)));
            const actions = element('div', 'rf-cart__item-actions');
            const quantity = element('div', 'rf-cart__quantity');
            for (const direction of [-1, 1]) {
                const button = element('button', '', direction === -1 ? '−' : '+');
                button.type = 'button';
                button.dataset.quantity = item.quantity + direction;
                button.disabled = direction === -1 ? item.quantity <= item.minimum : item.quantity >= item.maximum;
                button.setAttribute('aria-label', `${direction === -1 ? label('Decrease quantity', 'পরিমাণ কমান') : label('Increase quantity', 'পরিমাণ বাড়ান')}: ${item.name}`);
                quantity.append(button);
                if (direction === -1) quantity.append(element('span', '', item.quantity));
            }
            const remove = element('button', 'rf-cart__remove', label('Remove', 'সরান'));
            remove.type = 'button';
            remove.dataset.remove = '';
            remove.setAttribute('aria-label', `${label('Remove', 'সরান')}: ${item.name}`);
            actions.append(quantity, remove);
            info.append(actions);
            row.append(imageLink, info);
            find('items').append(row);
        }
        find('empty').hidden = data.items.length > 0;
        find('footer').hidden = !data.items.length;
        find('subtotal').textContent = money(data.subtotal);
        find('count').textContent = data.cartQty;
        document.querySelectorAll('.cartQty').forEach(node => { node.textContent = data.cartQty; });
    }

    async function request(method = 'GET', rowId = '', quantity) {
        if (busy && method !== 'GET') return;
        const current = ++revision;
        busy = true;
        dialog.setAttribute('aria-busy', 'true');
        find('retry').hidden = true;
        find('status').textContent = label('Updating your bag…', 'আপনার ব্যাগ আপডেট হচ্ছে…');
        try {
            const response = await fetch(dialog.dataset.cartUrl + (rowId ? `/${encodeURIComponent(rowId)}` : ''), {
                method,
                headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                ...(method === 'PATCH' ? { body: JSON.stringify({ quantity }) } : {})
            });
            const data = await response.json();
            if (!response.ok) throw new Error(Object.values(data.errors || {}).flat()[0] || data.message);
            if (current !== revision) return;
            render(data);
            find('status').textContent = '';
            if (method !== 'GET') {
                document.dispatchEvent(new CustomEvent('cart:updated', { detail: { ...data, source: 'sidebar' } }));
                const row = [...find('items').children].find(node => node.dataset.rowId === rowId);
                (row?.querySelector('button:not(:disabled)') || find('items').querySelector('button:not(:disabled)') || dialog.querySelector('[data-cart-close]')).focus();
            }
        } catch (error) {
            if (current !== revision) return;
            find('status').textContent = error.message || label('Could not load your bag. Please try again.', 'ব্যাগ লোড হয়নি। আবার চেষ্টা করুন।');
            find('retry').hidden = false;
        } finally {
            if (current === revision) { busy = false; dialog.setAttribute('aria-busy', 'false'); }
        }
    }

    function close() {
        if (!dialog.open || closeTimer) return;
        dialog.classList.remove('is-open');
        closeTimer = setTimeout(() => {
            dialog.close();
            closeTimer = null;
        }, matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 240);
    }

    document.addEventListener('click', event => {
        const trigger = event.target.closest('[data-cart-open]');
        if (!trigger || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button) return;
        event.preventDefault();
        clearTimeout(closeTimer);
        closeTimer = null;
        opener = trigger;
        if (!dialog.open) dialog.showModal();
        dialog.getBoundingClientRect();
        dialog.classList.add('is-open');
        document.querySelectorAll('[data-cart-open]').forEach(node => node.setAttribute('aria-expanded', 'true'));
        request();
    });
    dialog.addEventListener('click', event => {
        if (event.target.closest('[data-cart-close]')) return close();
        if (event.target === dialog) {
            const bounds = dialog.getBoundingClientRect();
            if (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom) close();
        }
        const button = event.target.closest('[data-quantity], [data-remove]');
        if (!button || busy) return;
        request(button.hasAttribute('data-remove') ? 'DELETE' : 'PATCH', button.closest('[data-row-id]').dataset.rowId, Number(button.dataset.quantity));
    });
    dialog.addEventListener('cancel', event => { event.preventDefault(); close(); });
    dialog.addEventListener('close', () => {
        dialog.classList.remove('is-open');
        document.querySelectorAll('[data-cart-open]').forEach(node => node.setAttribute('aria-expanded', 'false'));
        opener?.focus();
    });
    find('retry').addEventListener('click', () => request());
    document.addEventListener('cart:updated', event => {
        if (dialog.open && event.detail?.source !== 'sidebar') request();
    });
})();
