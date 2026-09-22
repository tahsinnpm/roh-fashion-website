# Storefront product components

Render an Eloquent Product anywhere inside `FrontEnd.master`:

```blade
<x-product-card :product="$product" />
```

The layout loads `products.css`, `products.js`, and one `<x-product-quick-view />` automatically. Do not place a modal inside each card or product loop. Existing pages keep their legacy modal until their cards are migrated.

Cards link to the full product page. Simple products add their minimum quantity directly; variant products open the shared dialog. The eye button previews either type. The dialog loads images, description, available attributes and variant stock, and provides a details link.

Eager-load `stocks` when rendering collections of products. Load-more responses use `FrontEnd.partials.product-cards` so dynamically appended cards have the same markup and delegated interactions.

Prices come from `Product::storefrontPrice()`. The cart endpoint calculates prices and validates quantities, variant membership and stock on the server. Never send a client-calculated price as a source of truth.

Successful additions update `.cartQty` and emit `cart:updated` on `document`. Styles and dialog DOM selectors are scoped to avoid collisions with legacy product pages.
