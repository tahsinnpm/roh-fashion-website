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
        const selections = [...find('attributes').querySelectorAll('select')];
        const complete = selections.length > 0 && selections.every((select) => select.value);
        activeVariant = variantProduct && complete ? model.variants.find((variant) => variant.varient === selections.map((select) => select.value).join('-')) : null;
        const stock = variantProduct ? Number(activeVariant?.qty || 0) : Number(model.product.stock_qty);
        const minimum = Math.max(1, Number(model.product.minimum_buy_qty) || 1);
        const available = stock >= minimum && (!variantProduct || !!activeVariant);
        find('quantity').max = stock;
        find('price').textContent = money(activeVariant?.price ?? model.price);
        find('stock').textContent = variantProduct && !complete
            ? label('Choose your options to see availability', 'স্টক জানতে অপশন নির্বাচন করুন')
            : available ? label(`${stock} available`, `${stock}টি স্টকে আছে`) : label('This selection is unavailable', 'এই অপশনটি স্টকে নেই');
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
        dialog.showModal();
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
            if (Number(product.is_varient) === 1) {
                data.attributes.forEach((attribute, index) => {
                    const wrapper = document.createElement('label');
                    wrapper.textContent = attribute.name;
                    const select = document.createElement('select');
                    select.required = true;
                    select.dataset.attributeId = attribute.id;
                    select.dataset.attributeName = attribute.name;
                    select.id = `rf-option-${index}`;
                    select.add(new Option(label('Select an option', 'অপশন নির্বাচন করুন'), ''));
                    attribute.values.forEach((value) => select.add(new Option(value, value)));
                    wrapper.append(select);
                    find('attributes').append(wrapper);
                });
            }
            find('gallery').replaceChildren();
            const images = [...new Set([product.product_thumbnail, ...(product.multi_imgs || []).map((item) => item.photo_name)].filter(Boolean))];
            images.forEach((path, index) => {
                const thumbnail = document.createElement('button');
                thumbnail.type = 'button';
                thumbnail.setAttribute('aria-label', `${name} — ${index + 1}`);
                const image = document.createElement('img');
                image.src = asset(path);
                image.alt = '';
                thumbnail.append(image);
                thumbnail.addEventListener('click', () => { find('image').src = asset(path); });
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
        const preview = event.target.closest('[data-product-preview]');
        const wishlist = event.target.closest('[data-product-wishlist]');
        const button = event.target.closest('[data-product-add], [data-product-buy]');

        if (preview) return open(preview.dataset.productPreview, preview);
        if (wishlist) {
            const icon = wishlist.querySelector('i');
            if (icon) {
                const active = icon.classList.toggle('fa-solid');
                icon.classList.toggle('fa-regular', !active);
                wishlist.style.color = active ? '#e11d48' : '';
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
        find('attributes').querySelectorAll('select').forEach((select) => {
            options.push({ name: 'attribute_ids[]', value: select.dataset.attributeId }, { name: 'attribute_names[]', value: select.dataset.attributeName }, { name: 'attribute_options[]', value: select.value });
        });
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
    dialog.querySelector('[data-quick-close]').addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', (event) => { if (event.target === dialog && (event.clientX < dialog.getBoundingClientRect().left || event.clientX > dialog.getBoundingClientRect().right || event.clientY < dialog.getBoundingClientRect().top || event.clientY > dialog.getBoundingClientRect().bottom)) dialog.close(); });
    dialog.addEventListener('close', () => { controller?.abort(); opener?.focus(); });
})();
