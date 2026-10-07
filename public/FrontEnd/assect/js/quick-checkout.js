(() => {
    const dialog = document.getElementById('rf-checkout');
    if (!dialog) return;
    const find = name => dialog.querySelector(`[data-checkout-${name}]`);
    const form = find('form');
    const label = (en, bn) => dialog.dataset.bangla === '1' ? bn : en;
    const money = value => `৳${Number(value || 0).toLocaleString('en-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    const submitBtn = find('submit');
    const submitText = submitBtn ? submitBtn.innerHTML : '';
    let quote, opener, submitting = false, loading;

    async function request(url, options = {}) {
        const response = await fetch(url, {
            ...options,
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                ...(options.headers || {})
            }
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            const error = new Error(Object.values(data.errors || {}).flat()[0] || data.error || data.message || label('Please try again.', 'আবার চেষ্টা করুন।'));
            error.fields = data.errors || {};
            throw error;
        }
        return data;
    }

    function totals() {
        if (!quote) return;
        const areaId = form.elements.shipping_id ? form.elements.shipping_id.value : '';
        const area = (quote.areas || []).find(a => String(a.id) === String(areaId));
        if (find('delivery')) {
            find('delivery').textContent = area ? money(area.shipping_charge) : label('Select area', 'এলাকা বাছুন');
        }
        if (find('total')) {
            const charge = area ? Number(area.shipping_charge || 0) : 0;
            const subtotal = Number(quote.subtotal || 0);
            const discount = Number(quote.discount || 0);
            find('total').textContent = money(Math.max(0, subtotal - discount + charge));
        }
    }

    async function updateQuantity(rowId, quantity) {
        if (submitting) return;
        if (quantity < 1) {
            return removeItem(rowId);
        }
        find('status').textContent = '';
        dialog.setAttribute('aria-busy', 'true');
        try {
            const data = await request(`/quick-checkout/item/${rowId}`, {
                method: 'POST',
                body: JSON.stringify({ quantity })
            });
            document.querySelectorAll('.cartQty').forEach(n => { n.textContent = data.cartQty || '0'; });
            await load();
        } catch (error) {
            find('status').textContent = error.message;
        } finally {
            dialog.removeAttribute('aria-busy');
        }
    }

    async function removeItem(rowId) {
        if (submitting) return;
        find('status').textContent = '';
        dialog.setAttribute('aria-busy', 'true');
        try {
            const data = await request(`/quick-checkout/item/${rowId}`, {
                method: 'DELETE'
            });
            document.querySelectorAll('.cartQty').forEach(n => { n.textContent = data.cartQty || '0'; });
            await load();
        } catch (error) {
            find('status').textContent = error.message;
        } finally {
            dialog.removeAttribute('aria-busy');
        }
    }

    async function load() {
        loading?.abort();
        loading = new AbortController();
        const current = loading;
        find('status').textContent = label('Loading your order…', 'আপনার অর্ডার লোড হচ্ছে…');
        find('retry').hidden = true;
        form.hidden = true;

        try {
            quote = await request(dialog.dataset.url, { signal: current.signal });
            const areaValue = form.elements.shipping_id.value;
            form.elements.shipping_id.replaceChildren(new Option(label('Select delivery area', 'ডেলিভারি এলাকা বাছুন'), ''));

            (quote.areas || []).forEach(area => {
                const name = Number(area.type) === 1 ? label('Inside Dhaka', 'ঢাকার ভিতরে') : label('Outside Dhaka', 'ঢাকার বাইরে');
                form.elements.shipping_id.add(new Option(`${name} · ${area.name} — ${money(area.shipping_charge)}`, area.id));
            });

            if (quote.areas && quote.areas.some(area => String(area.id) === areaValue)) {
                form.elements.shipping_id.value = areaValue;
            } else if (quote.areas && quote.areas.length === 1) {
                form.elements.shipping_id.value = quote.areas[0].id;
            }

            if (quote.customer) {
                ['name', 'phone', 'address', 'email'].forEach(field => {
                    if (form.elements[field] && !form.elements[field].value) {
                        form.elements[field].value = quote.customer[field] || '';
                    }
                });
            }

            const itemsContainer = find('items');
            itemsContainer.replaceChildren();

            if (!quote.items || quote.items.length === 0) {
                const emptyWrap = document.createElement('li');
                emptyWrap.className = 'rf-checkout__empty-state';
                emptyWrap.innerHTML = `
                    <div style="text-align: center; padding: 24px 12px; width: 100%;">
                        <div style="font-size: 28px; margin-bottom: 8px;">🛍️</div>
                        <p style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: #18181b;">
                            ${label('Your bag is currently empty.', 'আপনার কার্ট এখন খালি।')}
                        </p>
                        <button type="button" class="rf-checkout__btn-shop" data-checkout-close style="background: #09090b; color: #fff; border: none; padding: 8px 18px; border-radius: 6px; font-size: 13px; font-weight: 700; cursor: pointer;">
                            ${label('Continue Shopping', 'শপিং চালিয়ে যান')}
                        </button>
                    </div>
                `;
                itemsContainer.append(emptyWrap);
                find('submit').disabled = true;
            } else {
                find('submit').disabled = false;
                quote.items.forEach(item => {
                    const row = document.createElement('li');
                    row.className = 'rf-checkout__item';
                    row.innerHTML = `
                        <img src="${item.image}" alt="${item.name}" class="rf-checkout__item-thumb" onerror="this.src='/upload/product-default.jpg'">
                        <div class="rf-checkout__item-info">
                            <h4 class="rf-checkout__item-name">${item.name}</h4>
                            ${item.variant ? `<span class="rf-checkout__item-variant">${item.variant}</span>` : ''}
                            <div class="rf-checkout__item-actions-row">
                                <span class="rf-checkout__item-unit">${money(item.price)}</span>
                                <div class="rf-checkout__item-qty-box">
                                    <button type="button" class="rf-checkout__qty-btn" data-action="dec" data-row-id="${item.rowId}" title="Decrease">−</button>
                                    <span class="rf-checkout__qty-num">${item.quantity}</span>
                                    <button type="button" class="rf-checkout__qty-btn" data-action="inc" data-row-id="${item.rowId}" title="Increase">+</button>
                                </div>
                            </div>
                        </div>
                        <div class="rf-checkout__item-col-right">
                            <strong class="rf-checkout__item-total">${money(item.total)}</strong>
                            <button type="button" class="rf-checkout__item-btn-remove" data-action="del" data-row-id="${item.rowId}" title="${label('Remove item', 'মুছে ফেলুন')}">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                            </button>
                        </div>
                    `;

                    // Attach event listeners to quantity buttons and remove button
                    row.querySelector('[data-action="dec"]')?.addEventListener('click', () => updateQuantity(item.rowId, item.quantity - 1));
                    row.querySelector('[data-action="inc"]')?.addEventListener('click', () => updateQuantity(item.rowId, item.quantity + 1));
                    row.querySelector('[data-action="del"]')?.addEventListener('click', () => removeItem(item.rowId));

                    itemsContainer.append(row);
                });
            }

            find('subtotal').textContent = money(quote.subtotal);
            if (find('discount-row')) {
                find('discount-row').hidden = !quote.discount;
                find('discount').textContent = `−${money(quote.discount)}`;
            }

            totals();
            find('status').textContent = '';
            form.hidden = false;
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

    function close() {
        if (!submitting) dialog.close();
    }

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

    dialog.addEventListener('cancel', event => {
        event.preventDefault();
        close();
    });

    dialog.addEventListener('close', () => {
        loading?.abort();
        opener?.focus();
    });

    find('retry').addEventListener('click', load);
    if (form.elements.shipping_id) {
        form.elements.shipping_id.addEventListener('change', totals);
    }

    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (submitting || !quote || !form.reportValidity()) return;
        if (!quote.items || quote.items.length === 0) {
            find('status').textContent = label('Your bag is empty. Please add items first.', 'আপনার কার্ট খালি। অনুগ্রহ করে পণ্য যোগ করুন।');
            return;
        }

        submitting = true;
        find('submit').disabled = true;
        find('submit').innerHTML = `<span>${label('Placing order…', 'অর্ডার করা হচ্ছে…')}</span> <i class="fa-solid fa-spinner fa-spin"></i>`;
        find('status').textContent = '';
        dialog.setAttribute('aria-busy', 'true');

        try {
            const payload = Object.fromEntries(new FormData(form));
            payload.token = quote.token;
            const data = await request(dialog.dataset.url, {
                method: 'POST',
                body: JSON.stringify(payload)
            });
            document.querySelectorAll('.cartQty').forEach(node => { node.textContent = '0'; });
            window.location.assign(data.invoice_url);
        } catch (error) {
            find('status').textContent = error.message;
            const field = Object.keys(error.fields || {})[0];
            if (field && form.elements[field]) {
                form.elements[field].focus();
            } else {
                find('status').focus();
            }
        } finally {
            submitting = false;
            find('submit').disabled = false;
            find('submit').innerHTML = submitText;
            dialog.removeAttribute('aria-busy');
        }
    });

    if (location.hash === '#checkout') {
        history.replaceState(null, '', location.pathname + location.search);
        open();
    }
})();
