(() => {
    const dialog = document.querySelector('#rf-product-dialog');
    if (!dialog) return;
    const find = (name) => dialog.querySelector(`[data-quick-${name}]`);
    const bangla = dialog.dataset.bangla === '1';
    const label = (en, bn) => bangla ? bn : en;
    const base = dialog.dataset.baseUrl.replace(/\/$/, '');
    const asset = (path) => /^https?:\/\//i.test(path || '') ? path : `${base}/${String(path || '').replace(/^\//, '')}`;
    const money = (value) => `৳${Number(value).toLocaleString('en-BD', { maximumFractionDigits: 2 })}`;
    const notice = document.querySelector('[data-cart-notice]');
    let model, activeVariant, controller, opener, noticeTimer, submitting = false, buyIntent = false;

    async function request(path, options = {}) {
        const response = await fetch(`${base}${path}`, {
            ...options,
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, ...options.headers }
        });
        const data = await response.json();
        if (!response.ok || data.error) throw new Error(data.error || Object.values(data.errors || {}).flat()[0] || data.message || 'Request failed. Please try again.');
        return data;
    }

    function announce(message) {
        clearTimeout(noticeTimer);
        notice.textContent = message;
        notice.hidden = false;
        noticeTimer = setTimeout(() => { notice.hidden = true; }, 4500);
    }

    async function add(id, quantity, variant = '', options = []) {
        const data = await request(`/cart/data/store/${id}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ quantity, product_varient: variant, options: JSON.stringify(options) })
        });
        document.querySelectorAll('.cartQty').forEach((element) => { element.textContent = data.cartQty; });
        announce(label('Added to your cart', 'কার্টে যোগ হয়েছে'));
        document.dispatchEvent(new CustomEvent('cart:updated', { detail: data }));
    }

    function updateSelection() {
        if (!model) return;
        const variantProduct = Number(model.product.is_varient) === 1;
        const variantSelect = find('attributes').querySelector('#rf-quick-variant-select');

        if (variantProduct && variantSelect) {
            activeVariant = model.variants.find((v) => v.varient === variantSelect.value) || null;
            const labelEl = find('attributes').querySelector('#rf-quick-var-label');
            if (labelEl && activeVariant) {
                labelEl.textContent = activeVariant.varient;
            }
        } else {
            activeVariant = null;
        }

        const stock = variantProduct ? Number(activeVariant?.qty || 0) : Number(model.product.stock_qty);
        const minimum = Math.max(1, Number(model.product.minimum_buy_qty) || 1);
        const available = stock >= minimum && (!variantProduct || !!activeVariant);
        find('quantity').max = stock;

        const curPrice = Number(activeVariant?.price ?? model.price);
        const regPrice = Number(activeVariant?.regular_price ?? model.regular_price ?? model.product?.regular_price ?? 0);
        if (regPrice > curPrice && curPrice > 0) {
            const pct = Math.round(((regPrice - curPrice) / regPrice) * 100);
            find('price').innerHTML = `<strong>${money(curPrice)}</strong> <del style="color:#777;font-size:0.85em;margin-left:8px;">${money(regPrice)}</del> <span style="color:#16a34a;font-size:0.85em;margin-left:6px;font-weight:600;">Save ${pct}%</span>`;
        } else {
            find('price').innerHTML = `<strong>${money(curPrice)}</strong>`;
        }

        const stockEl = find('stock');
        if (stockEl) {
            stockEl.textContent = variantProduct && !activeVariant
                ? label('Choose your options to see availability', 'স্টক জানতে অপশন নির্বাচন করুন')
                : available ? label(`${stock} available`, `${stock}টি স্টকে আছে`) : label('This selection is unavailable', 'এই অপশনটি স্টকে নেই');
            stockEl.className = 'rf-quick__stock ' + (available ? 'in-stock' : 'out-of-stock');
        }
        find('submit').disabled = submitting || !available;
        find('buy').disabled = submitting || !available;
        find('image').src = asset(activeVariant?.image || model.product.product_thumbnail);
    }

    async function open(id, button, buy = false) {
        if (submitting) return;
        buyIntent = buy;
        controller?.abort();
        controller = new AbortController();
        const currentRequest = controller;
        opener = button;
        model = null;
        activeVariant = null;
        find('content').hidden = true;
        find('status').textContent = label('Loading product…', 'পণ্যের তথ্য আসছে…');
        find('status').hidden = false;
        find('title').textContent = label('Product details', 'পণ্যের তথ্য');
        find('error').textContent = '';
        
        // Preserve exact window scroll position to prevent jumping to top
        const savedScrollY = window.pageYOffset || document.documentElement.scrollTop || window.scrollY || 0;
        dialog.showModal();
        window.scrollTo({ top: savedScrollY, left: 0, behavior: 'instant' });
        dialog.querySelector('[data-quick-close]')?.focus({ preventScroll: true });
        try {
            const data = await request(`/product/view/modal/${id}`, { signal: currentRequest.signal });
            if (currentRequest !== controller || !dialog.open) return;
            model = data;
            const product = data.product;
            const name = bangla ? product.name_bn || product.name_en : product.name_en;
            find('title').textContent = name;
            find('image').alt = name;
            find('description').textContent = data.description || '';
            find('details').href = data.details_url;
            find('quantity').value = Math.max(1, Number(product.minimum_buy_qty) || 1);
            find('quantity').min = find('quantity').value;
            find('attributes').replaceChildren();
            if (Number(product.is_varient) === 1 && data.variants && data.variants.length > 0) {
                const block = document.createElement('div');
                block.className = 'rf-quick-variant-block';

                const defaultVariant = data.variants.find((v) => Number(v.qty) > 0) || data.variants[0];

                const lbl = document.createElement('label');
                lbl.htmlFor = 'rf-quick-variant-select';
                lbl.className = 'rf-quick-variant-lbl';
                lbl.innerHTML = `<span>${label('Select Variant:', 'ভ্যারিয়েন্ট পছন্দ করুন:')}</span> <strong id="rf-quick-var-label">${defaultVariant ? defaultVariant.varient : ''}</strong>`;
                block.append(lbl);

                const select = document.createElement('select');
                select.id = 'rf-quick-variant-select';
                select.className = 'rf-quick-variant-select';
                select.setAttribute('aria-label', label('Select Variant', 'ভ্যারিয়েন্ট পছন্দ করুন'));

                data.variants.forEach((v) => {
                    const isOut = Number(v.qty) <= 0;
                    const priceLabel = v.price > 0 ? ` — ${money(v.price)}` : '';
                    const stockLabel = isOut ? ` (${label('Out of stock', 'স্টক শেষ')})` : '';
                    const opt = new Option(`${v.varient}${priceLabel}${stockLabel}`, v.varient);
                    if (isOut) opt.disabled = true;
                    if (defaultVariant && v.varient === defaultVariant.varient) {
                        opt.selected = true;
                    }
                    select.add(opt);
                });

                select.addEventListener('change', () => {
                    find('error').textContent = '';
                    updateSelection();
                });

                block.append(select);
                find('attributes').append(block);
            }
            find('gallery').replaceChildren();
            const images = [...new Set([product.product_thumbnail, ...(product.multi_imgs || []).map((item) => item.photo_name)].filter(Boolean))];
            images.forEach((path, index) => {
                const thumbnail = document.createElement('button');
                thumbnail.type = 'button';
                thumbnail.className = index === 0 ? 'is-active' : '';
                thumbnail.setAttribute('aria-label', `${name} — ${index + 1}`);
                const image = document.createElement('img');
                image.src = asset(path);
                image.alt = '';
                thumbnail.append(image);
                thumbnail.addEventListener('click', () => {
                    find('image').src = asset(path);
                    find('gallery').querySelectorAll('button').forEach((b) => b.classList.remove('is-active'));
                    thumbnail.classList.add('is-active');
                });
                find('gallery').append(thumbnail);
            });
            updateSelection();
            find('status').hidden = true;
            find('content').hidden = false;
        } catch (error) {
            if (error.name !== 'AbortError') find('status').textContent = error.message;
        }
    }

    const pendingCards = new WeakMap();
    document.addEventListener('click', async (event) => {
        const preview = event.target.closest('[data-product-preview], .rf-product__preview');
        const wishlist = event.target.closest('[data-product-wishlist]');
        const button = event.target.closest('[data-product-add], [data-product-buy]');

        if (preview) {
            event.preventDefault();
            event.stopPropagation();
            const id = preview.dataset.productPreview || preview.getAttribute('data-product-preview');
            return open(id, preview);
        }
        if (wishlist) {
            const icon = wishlist.querySelector('i');
            if (icon) {
                const active = icon.classList.toggle('fa-solid');
                icon.classList.toggle('fa-regular', !active);
                wishlist.style.color = active ? '#000000' : '';
            }
            announce(label('Saved to your wishlist', 'পছন্দের তালিকায় রাখা হয়েছে'));
            return;
        }
        if (!button || button.disabled) return;
        const buy = button.hasAttribute('data-product-buy');
        const id = button.dataset.productBuy || button.dataset.productAdd;
        if (button.dataset.variant === '1') return open(id, button, buy);
        const card = button.closest('.rf-product') || button.parentElement;
        if (pendingCards.has(card)) {
            if (buy) {
                try {
                    await pendingCards.get(card);
                    document.dispatchEvent(new CustomEvent('checkout:open'));
                } catch { /* The original request displays the error. */ }
            }
            return;
        }
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        const pending = add(id, Number(button.dataset.quantity));
        pendingCards.set(card, pending);
        try {
            await pending;
            if (buy) document.dispatchEvent(new CustomEvent('checkout:open'));
        }
        catch (error) { announce(error.message); }
        finally { pendingCards.delete(card); button.disabled = false; button.removeAttribute('aria-busy'); }
    });

    find('attributes').addEventListener('change', () => { find('error').textContent = ''; updateSelection(); });
    find('form').addEventListener('submit', async (event) => {
        event.preventDefault();
        if (submitting || !model || find('submit').disabled || !find('form').reportValidity()) return;
        const checkout = event.submitter ? event.submitter === find('buy') : buyIntent;
        const options = [];
        if (activeVariant && model.attributes && model.attributes.length > 0) {
            const parts = activeVariant.varient.split('-');
            model.attributes.forEach((attr, idx) => {
                options.push(
                    { name: 'attribute_ids[]', value: attr.id },
                    { name: 'attribute_names[]', value: attr.name },
                    { name: 'attribute_options[]', value: parts[idx] !== undefined ? parts[idx] : activeVariant.varient }
                );
            });
        }
        submitting = true;
        find('submit').disabled = true;
        find('buy').disabled = true;
        find('submit').setAttribute('aria-busy', 'true');
        find('error').textContent = '';
        try {
            await add(model.product.id, Number(find('quantity').value), activeVariant?.varient || '', options);
            dialog.close();
            if (checkout) document.dispatchEvent(new CustomEvent('checkout:open'));
        } catch (error) { find('error').textContent = error.message; }
        finally { submitting = false; find('submit').removeAttribute('aria-busy'); updateSelection(); }
    });
    dialog.querySelector('[data-quick-qty-down]')?.addEventListener('click', () => {
        const q = find('quantity');
        if (q) {
            const min = Number(q.min) || 1;
            q.value = Math.max(min, Number(q.value) - 1);
        }
    });
    dialog.querySelector('[data-quick-qty-up]')?.addEventListener('click', () => {
        const q = find('quantity');
        if (q) {
            const max = Number(q.max) || 999;
            q.value = Math.min(max, Number(q.value) + 1);
        }
    });
    dialog.querySelector('[data-quick-close]').addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', (event) => { if (event.target === dialog && (event.clientX < dialog.getBoundingClientRect().left || event.clientX > dialog.getBoundingClientRect().right || event.clientY < dialog.getBoundingClientRect().top || event.clientY > dialog.getBoundingClientRect().bottom)) dialog.close(); });
    dialog.addEventListener('close', () => {
        controller?.abort();
        if (opener && typeof opener.focus === 'function') {
            opener.focus({ preventScroll: true });
        }
    });
})();
