@php
  $prefix = Request::route() ? Request::route()->getPrefix() : '';
  $route = Route::current() ? Route::current()->getName() : '';
@endphp

<style>
    /* ========================================================
       PREMIUM DARK / BLACK SIDEBAR THEME
       ======================================================== */
    .navbar-aside {
        background: #0f141c !important;
        border-right: 1px solid rgba(255, 255, 255, 0.07) !important;
        box-shadow: 4px 0 24px rgba(0, 0, 0, 0.5) !important;
        transition: all 0.25s ease;
    }

    /* Top Brand / Header Section */
    .navbar-aside .aside-top {
        background: #090d13 !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.07) !important;
        box-shadow: none !important;
        padding: 14px 18px !important;
        min-height: 70px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .navbar-aside .aside-top .brand-wrap img {
        max-height: 34px !important;
        object-fit: contain;
    }

    .navbar-aside .aside-top .brand-wrap {
        background: #fff !important;
        border-radius: 6px;
        padding: 6px 8px !important;
    }

    /* Minimize Button */
    .btn-aside-minimize {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        border-radius: 8px !important;
        color: #94a3b8 !important;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 0 !important;
    }

    .btn-aside-minimize:hover {
        background: rgba(255, 255, 255, 0.12) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.2) !important;
    }

    .btn-aside-minimize i {
        font-size: 13px;
        color: inherit !important;
    }

    /* Navigation Container */
    .navbar-aside nav {
        padding: 12px 0 35px;
    }

    .navbar-aside .menu-aside {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .navbar-aside .menu-item {
        position: relative;
        margin: 2px 10px;
    }

    /* Menu Link (Top-level items) */
    .navbar-aside .menu-link {
        display: flex !important;
        align-items: center !important;
        padding: 10px 14px !important;
        color: #94a3b8 !important;
        font-size: 13.5px !important;
        font-weight: 500 !important;
        border-radius: 8px !important;
        text-decoration: none !important;
        position: relative !important;
        transition: all 0.2s ease !important;
        background-color: transparent !important;
        border: 1px solid transparent !important;
    }

    /* Uniform Icons */
    .navbar-aside .menu-link .fontawesome_icon_custom,
    .navbar-aside .menu-link .icon {
        width: 22px !important;
        min-width: 22px !important;
        margin-right: 12px !important;
        font-size: 15px !important;
        color: #64748b !important;
        text-align: center;
        transition: all 0.2s ease;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
    }

    .navbar-aside .menu-link .text {
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-weight: inherit;
    }

    /* Top-level Menu Hover */
    .navbar-aside .menu-link:hover {
        color: #f1f5f9 !important;
        background-color: rgba(255, 255, 255, 0.06) !important;
        border-color: rgba(255, 255, 255, 0.04) !important;
    }

    .navbar-aside .menu-link:hover .fontawesome_icon_custom,
    .navbar-aside .menu-link:hover .icon {
        color: #38bdf8 !important;
        transform: scale(1.08);
    }

    /* Active Menu Item */
    .navbar-aside .menu-item.active > .menu-link {
        color: #ffffff !important;
        background: linear-gradient(90deg, rgba(56, 189, 248, 0.16) 0%, rgba(56, 189, 248, 0.03) 100%) !important;
        border-left: 3px solid #38bdf8 !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
    }

    .navbar-aside .menu-item.active > .menu-link .fontawesome_icon_custom,
    .navbar-aside .menu-item.active > .menu-link .icon {
        color: #38bdf8 !important;
    }

    /* Dropdown Arrow / Chevron */
    .navbar-aside .menu-item.has-submenu > .menu-link:after {
        content: "\f107" !important;
        font-family: "Font Awesome 6 Free", "FontAwesome" !important;
        font-weight: 900 !important;
        font-size: 11px !important;
        border: none !important;
        position: static !important;
        margin-left: auto !important;
        color: #64748b !important;
        transition: transform 0.25s ease, color 0.2s ease !important;
        display: inline-block !important;
        vertical-align: middle !important;
        top: auto !important;
        right: auto !important;
    }

    .navbar-aside .menu-item.has-submenu.active > .menu-link:after,
    .navbar-aside .menu-item.has-submenu.submenu-open > .menu-link:after {
        color: #38bdf8 !important;
        transform: rotate(180deg) !important;
    }

    /* Submenu Box */
    .navbar-aside .submenu {
        background: #090d13 !important;
        margin: 4px 0 8px 14px !important;
        padding: 6px 6px 6px 12px !important;
        border-radius: 8px !important;
        border-left: 2px solid rgba(56, 189, 248, 0.25) !important;
        border-top: none !important;
        border-right: none !important;
        border-bottom: none !important;
        box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.4) !important;
    }

    /* Submenu Individual Links */
    .navbar-aside .submenu a {
        display: flex !important;
        align-items: center !important;
        padding: 7px 12px !important;
        color: #8b949e !important;
        font-size: 12.5px !important;
        font-weight: 400 !important;
        border-radius: 6px !important;
        text-decoration: none !important;
        margin: 2px 0 !important;
        position: relative !important;
        transition: all 0.18s ease !important;
        background: transparent !important;
    }

    .navbar-aside .submenu a:before {
        content: "" !important;
        display: inline-block !important;
        width: 5px !important;
        height: 5px !important;
        border-radius: 50% !important;
        background-color: #475569 !important;
        margin-right: 10px !important;
        position: static !important;
        transform: none !important;
        transition: all 0.2s ease !important;
        flex-shrink: 0;
    }

    /* Submenu Hover */
    .navbar-aside .submenu a:hover {
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.05) !important;
        padding-left: 15px !important;
    }

    .navbar-aside .submenu a:hover:before {
        background-color: #38bdf8 !important;
        box-shadow: 0 0 6px #38bdf8;
    }

    /* Submenu Active */
    .navbar-aside .submenu a.active {
        color: #38bdf8 !important;
        font-weight: 600 !important;
        background: rgba(56, 189, 248, 0.08) !important;
    }

    .navbar-aside .submenu a.active:before {
        background-color: #38bdf8 !important;
        box-shadow: 0 0 8px rgba(56, 189, 248, 0.8) !important;
        width: 6px !important;
        height: 6px !important;
    }

    /* Subtle Divider */
    .navbar-aside hr {
        border: 0 !important;
        height: 1px !important;
        background: rgba(255, 255, 255, 0.07) !important;
        margin: 16px 14px !important;
        opacity: 1 !important;
    }

    /* Modern Dark Scrollbar */
    .navbar-aside::-webkit-scrollbar {
        width: 5px;
    }

    .navbar-aside::-webkit-scrollbar-track {
        background: #0f141c;
    }

    .navbar-aside::-webkit-scrollbar-thumb {
        background: #21262d;
        border-radius: 4px;
    }

    .navbar-aside::-webkit-scrollbar-thumb:hover {
        background: #30363d;
    }

    /* Collapsed Mini Sidebar (body.aside-mini) */
    body.aside-mini .navbar-aside .aside-top {
        padding: 12px 6px !important;
        justify-content: center !important;
    }

    body.aside-mini .navbar-aside .menu-item {
        margin: 4px 6px !important;
    }

    body.aside-mini .navbar-aside .menu-link {
        justify-content: center !important;
        padding: 10px !important;
    }

    body.aside-mini .navbar-aside .menu-link .fontawesome_icon_custom,
    body.aside-mini .navbar-aside .menu-link .icon {
        margin-right: 0 !important;
    }
</style>

<aside class="navbar-aside" id="offcanvas_aside">
    <div class="aside-top">
        <a href="{{ route('admin.dashboard') }}" class="brand-wrap">
            @php
                $logo = get_setting('site_logo');
            @endphp
            @if($logo != null)
                <img src="{{ asset(get_setting('site_logo')->value ?? ' ') }}" alt="{{ env('APP_NAME') }}" style="height: 30px !important;">
            @else
                <img src="{{ asset('upload/no_image.jpg') }}" alt="{{ env('APP_NAME') }}" style="height: 30px !important; width: 80px !important; min-width: 80px !important;">
            @endif
        </a>
        <div>
            <button class="btn btn-icon btn-aside-minimize" type="button"><i class="fa fa-arrow-left"></i></button>
        </div>
    </div>
    <nav>
        <ul class="menu-aside">
            <li class="menu-item {{ ($route == 'admin.dashboard')? 'active':'' }}">
                <a class="menu-link" href="{{ route('admin.dashboard') }}">
                   <i class="fa-solid fa-house fontawesome_icon_custom"></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>

            <li class="menu-item {{ ($route == 'pos.index')? 'active':'' }}">
                <a class="menu-link" href="{{ route('pos.index') }}">
                    <i class="fa-solid fa-calculator fontawesome_icon_custom"></i>
                    <span class="text">POS System</span>
                </a>
            </li>

            <li class="menu-item has-submenu
                {{ ($route == 'slider.index')? 'active':'' }}
                {{ ($route == 'slider.edit')? 'active':'' }}
                {{ ($route == 'slider.create')? 'active':'' }}
            ">
                @if(Auth::guard('admin')->user()->role == '1' || in_array('37', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                    <a class="menu-link" href="#">
                        <i class="fa-solid fa-sliders fontawesome_icon_custom"></i>
                        <span class="text">Sliders</span>
                    </a>
                @endif
                <div class="submenu">
                    @if(Auth::guard('admin')->user()->role == '1' || in_array('37', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($route == 'slider.index') ? 'active':'' }}" href="{{ route('slider.index') }}">Slider List</a>
                    @endif
                    @if(Auth::guard('admin')->user()->role == '1' || in_array('37', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($route == 'slider.create') ? 'active':'' }}" href="{{ route('slider.create') }}">Slider Add</a>
                    @endif
                </div>
            </li>

            <li class="menu-item has-submenu
                {{ ($prefix == 'admin/product') || ($prefix == 'admin/unit') || ($route == 'attribute.index') || ($prefix == 'admin/brand') ? 'active' : '' }}
            ">
                @if(Auth::guard('admin')->user()->role == '1' || in_array('1', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                    <a class="menu-link" href="#">
                        <i class="fa-solid fa-cart-shopping fontawesome_icon_custom"></i>
                        <span class="text">Products</span>
                    </a>
                @endif
                <div class="submenu">
                    @if(Auth::guard('admin')->user()->role == '1' || in_array('1', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($route == 'product.add') ? 'active':'' }}" href="{{ route('product.add') }}">Product Add</a>
                    @endif
                    @if(Auth::guard('admin')->user()->role == '1' || in_array('2', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($route == 'product.all') ? 'active':'' }}" href="{{ route('product.all') }}">Products</a>
                    @endif

                    <!-- @if(Auth::guard('admin')->user()->role == '1' || in_array('2', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($route == 'product.affiliate') ? 'active':'' }}" href="{{ route('product.affiliate') }}">Affiliate Products</a>
                    @endif -->

                    @if(Auth::guard('admin')->user()->role == '1' || in_array('13', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($route == 'attribute.index') ? 'active':'' }}" href="{{ route('attribute.index') }}">Attributes</a>
                    @endif
                  <!--   @if(Auth::guard('admin')->user()->role == '1' || in_array('53', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($route == 'unit.index') ? 'active':'' }}" href="{{ route('unit.index') }}">Unit</a>
                    @endif
                    @if(Auth::guard('admin')->user()->role == '1' || in_array('9', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($prefix == 'admin/brand') ? 'active':'' }}" href="{{ route('brand.all') }}">Brands</a>
                    @endif -->
                </div>
            </li>

            @if(Auth::guard('admin')->user()->role == '1' || in_array('5', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                <li class="menu-item has-submenu {{ ($prefix == 'admin/category') ? 'active' : '' }}">
                    <a class="menu-link" href="#">
                        <i class="fa-solid fa-list fontawesome_icon_custom"></i>
                        <span class="text">Categories</span>
                    </a>
                    <div class="submenu">
                        <a class="{{ ($route == 'category.index') ? 'active' : '' }}" href="{{ route('category.index') }}">Category List</a>
                        <a class="{{ ($route == 'category.create') ? 'active' : '' }}" href="{{ route('category.create') }}">Add Category</a>
                    </div>
                </li>
            @endif

            <li class="menu-item has-submenu {{ ($prefix == 'admin/landing-page') ? 'active' : '' }}">
                <a class="menu-link" href="#">
                    <i class="fa-solid fa-file-invoice fontawesome_icon_custom"></i>
                    <span class="text">Landing Page</span>
                </a>
                <div class="submenu">
                    <a class="{{ ($route == 'landing.page.index') ? 'active':'' }}" href="{{ route('landing.page.index') }}">Landing Page List</a>
                    <a class="{{ ($route == 'landing.page.create') ? 'active':'' }}" href="{{ route('landing.page.create') }}">Create Landing Page</a>
                </div>
            </li>

            <li class="menu-item has-submenu {{ ($prefix == 'admin/dynamic-sections') || \Illuminate\Support\Str::startsWith($route, 'dynamic-section.') ? 'active' : '' }}">
                <a class="menu-link" href="#">
                    <i class="fa-solid fa-layer-group fontawesome_icon_custom"></i>
                    <span class="text">Dynamic Sections</span>
                </a>
                <div class="submenu">
                    <a class="{{ ($route == 'dynamic-section.index') ? 'active':'' }}" href="{{ route('dynamic-section.index') }}">Sections List</a>
                    <a class="{{ ($route == 'dynamic-section.create') ? 'active':'' }}" href="{{ route('dynamic-section.create') }}">Add New Section</a>
                </div>
            </li>

            <!-- @if(Auth::guard('admin')->user()->role == '1')
                @if(get_setting('multi_vendor')->value)
                    <li class="menu-item has-submenu
                        {{ ($route == 'vendor.index')? 'active':'' }}
                        {{ ($route == 'vendor.edit')? 'active':'' }}
                        {{ ($route == 'vendor.create')? 'active':'' }}
                    ">
                        <a class="menu-link" href="#">
                            <i class="fa-solid fa-user-plus fontawesome_icon_custom"></i>
                            <span class="text">Vendors</span>
                        </a>
                        <div class="submenu">
                            <a class="{{ ($route == 'vendor.request') ? 'active':'' }}" href="{{ route('vendor.sell.request') }}">Vendor Request</a>
                            <a class="{{ ($route == 'vendor.index') ? 'active':'' }}" href="{{ route('vendor.index') }}">Vendor List</a>
                            <a class="{{ ($route == 'vendor.create') ? 'active':'' }}" href="{{ route('vendor.create') }}">Vendor Add</a>
                        </div>
                    </li>
                @endif
            @endif -->

            <!-- @if(Auth::guard('admin')->user()->role == '2' || Auth::guard('admin')->user()->role == '1')
                <li class="menu-item has-submenu
                    {{ ($route == 'transaction.summary')? 'active':'' }}
                    {{ ($route == 'payment.index')? 'active':'' }}
                    {{ ($route == 'payment.edit')? 'active':'' }}
                    {{ ($route == 'payment.create')? 'active':'' }}
                    {{ ($route == 'withdraw-requests.index')? 'active':'' }}
                    {{ ($route == 'withdraw-requests.create')? 'active':'' }}
                    {{ ($route == 'transaction.index')? 'active':'' }}">
                    <a class="menu-link" href="#">
                        <i class="fa-solid fa-money-bill-wave fontawesome_icon_custom"></i>
                        <span class="text">Vendor Accounts</span>
                    </a>
                    <div class="submenu">
                        @if(Auth::guard('admin')->user()->role == 1)
                            <a class="{{ ($route == 'payment.create') ? 'active':'' }}" href="{{ route('transaction.summary') }}">Account Summary</a>
                        @endif
                        <a class="{{ ($route == 'withdraw-requests.index') ? 'active':'' }}" href="{{ route('withdraw-requests.index') }}">Withdrawal Requests</a>
                        @if(Auth::guard('admin')->user()->role == 2)
                            <a class="{{ ($route == 'withdraw-requests.create') ? 'active':'' }}" href="{{ route('withdraw-requests.create') }}">Add Withdrawal Request</a>
                            <a class="{{ ($route == 'transaction.index') ? 'active':'' }}" href="{{ route('transaction.index') }}">View Transactions</a>
                        @endif
                        @if(Auth::guard('admin')->user()->role == 1)
                            <a class="{{ ($route == 'payment.create') ? 'active':'' }}" href="{{ route('payment.create') }}">Payment Add</a>
                            <a class="{{ ($route == 'payment.index') ? 'active':'' }}" href="{{ route('payment.index') }}">Payment List</a>
                        @endif
                    </div>
                </li>
            @endif -->

            <li class="menu-item has-submenu
                {{ ($route == 'campaing.index')? 'active':'' }}
                {{ ($route == 'campaing.create')? 'active':'' }}
                {{ ($route == 'campaing.edit')? 'active':'' }}
            ">
                @if(Auth::guard('admin')->user()->role == '1' || in_array('41', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                    <a class="menu-link" href="#">
                       <i class="fa-solid fa-rectangle-ad fontawesome_icon_custom"></i>
                        <span class="text">Campaigns</span>
                    </a>
                @endif
                <div class="submenu">
                    @if(Auth::guard('admin')->user()->role == '1' || in_array('41', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($route == 'campaing.index') ? 'active':'' }}" href="{{ route('campaing.index') }}">Campaign List</a>
                    @endif
                    @if(Auth::guard('admin')->user()->role == '1' || in_array('42', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($route == 'campaing.create') ? 'active':'' }}" href="{{ route('campaing.create') }}">Campaign Add</a>
                    @endif
                </div>
            </li>

            @if(Auth::guard('admin')->user()->role == '1')
            <li class="menu-item has-submenu
                {{ ($route == 'coupons.index')? 'active':'' }}
                {{ ($route == 'coupons.create')? 'active':'' }}
                {{ ($route == 'coupons.edit')? 'active':'' }}
            ">
                <a class="menu-link" href="#">
                    <i class="fa-solid fa-ticket fontawesome_icon_custom"></i>
                    <span class="text">Coupons</span>
                </a>
                <div class="submenu">
                    <a class="{{ ($route == 'coupons.index') ? 'active':'' }}" href="{{ route('coupons.index') }}">Coupon List</a>
                    <a class="{{ ($route == 'coupons.create') ? 'active':'' }}" href="{{ route('coupons.create') }}">Coupon Add</a>
                </div>
            </li>
            @endif

            <!-- <li class="menu-item has-submenu {{ ($prefix == 'admin/supplier')?'active':'' }}">
                @if(Auth::guard('admin')->user()->role == '1' || in_array('45', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                    <a class="menu-link" href="#">
                        <i class="fa-solid fa-truck fontawesome_icon_custom"></i>
                        <span class="text">Suppliers</span>
                    </a>
                @endif
                <div class="submenu">
                    @if(Auth::guard('admin')->user()->role == '1' || in_array('45', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($route == 'supplier.all') ? 'active':'' }}" href="{{ route('supplier.all') }}">Supplier List</a>
                    @endif
                    @if(Auth::guard('admin')->user()->role == '1' || in_array('46', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($route == 'supplier.create') ? 'active':'' }}" href="{{ route('supplier.create') }}">Supplier Add</a>
                    @endif
                </div>
            </li> -->

            <li class="menu-item has-submenu {{ in_array($route, ['all_orders.index', 'orders.trash', 'abandoned-checkouts.index']) ? 'active' : '' }}">
                @if(Auth::guard('admin')->user()->role == '1' || in_array('17', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                    <a class="menu-link" href="#">
                        <i class="fa-solid fa-bag-shopping fontawesome_icon_custom"></i>
                        <span class="text">Order</span>
                    </a>
                @endif
                <div class="submenu">
                    @if(Auth::guard('admin')->user()->role == '1' || in_array('17', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($route == 'all_orders.index') ? 'active':'' }}" href="{{ route('all_orders.index') }}">All Orders</a>
                        <a class="{{ ($route == 'orders.trash') ? 'active':'' }}" href="{{ route('orders.trash') }}">Order Trash</a>
                        <a class="{{ ($route == 'abandoned-checkouts.index') ? 'active':'' }}" href="{{ route('abandoned-checkouts.index') }}">Abandoned Checkouts</a>
                    @endif
                </div>
            </li>

            <!-- <li class="menu-item has-submenu {{ ($route == 'return-request.all')?'active':'' }}">
                @if(Auth::guard('admin')->user()->role == '1')
                    <a class="menu-link" href="#">
                        <i class="fa-solid fa-arrow-rotate-left fontawesome_icon_custom"></i>
                        <span class="text">Return Requests</span>
                    </a>
                @endif
                <div class="submenu">
                    @if(Auth::guard('admin')->user()->role == '1')
                        <a class="{{ ($route == 'return-request.all') ? 'active':'' }}" href="{{ route('return-request.all') }}">All Requests</a>
                    @endif
                </div>
            </li> -->

            <!-- <li class="menu-item has-submenu {{ ($route == 'sms.templates') || ($route == 'sms.providers')?'active':'' }}">
                @if(Auth::guard('admin')->user()->role == '1' || in_array('34', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                    <a class="menu-link" href="#">
                        <i class="fa-solid fa-comment-sms fontawesome_icon_custom"></i>
                        <span class="text">OTP System</span>
                    </a>
                @endif
                <div class="submenu">
                    @if(Auth::guard('admin')->user()->role == '1' || in_array('34', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($route == 'sms.templates') ? 'active':'' }}" href="{{ route('sms.templates') }}">SMS Templates</a>
                    @endif

                    @if(Auth::guard('admin')->user()->role == '1' || in_array('34', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($route == 'sms.providers') ? 'active':'' }}" href="{{ route('sms.providers') }}">SMS Providers</a>
                    @endif
                </div>
            </li> -->

            <!-- <li class="menu-item has-submenu
                {{ ($route == 'staff.index')? 'active':'' }}
                {{ ($route == 'staff.create')? 'active':'' }}
                {{ ($route == 'staff.edit')? 'active':'' }}
                {{ ($route == 'roles.index')? 'active':'' }}
                {{ ($route == 'roles.create')? 'active':'' }}
                {{ ($route == 'roles.edit')? 'active':'' }}
            ">
                @if(Auth::guard('admin')->user()->role == '1' || in_array('25', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                    <a class="menu-link" href="#">
                       <i class="fa-solid fa-user-group fontawesome_icon_custom"></i>
                        <span class="text">Staff</span>
                    </a>
                @endif
                <div class="submenu">
                    @if(Auth::guard('admin')->user()->role == '1' || in_array('25', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($route == 'staff.index') ? 'active':'' }}" href="{{ route('staff.index') }}">All Staff</a>
                    @endif
                    @if(Auth::guard('admin')->user()->role == '1' || in_array('29', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($route == 'roles.index') ? 'active':'' }}" href="{{ route('roles.index') }}">Staff Permissions</a>
                    @endif
                </div>
            </li> -->

            <!-- @if(Auth::guard('admin')->user()->role == '1')
            <li class="menu-item has-submenu
                {{ ($route == 'stock_report.index')? 'active':'' }}
            ">
                <a class="menu-link" href="#">
                   <i class="fa-solid fa-chart-line fontawesome_icon_custom"></i>
                    <span class="text">Report</span>
                </a>
                <div class="submenu">
                    <a class="{{ ($route == 'stock_report.index') ? 'active':'' }}" href="{{ route('stock_report.index') }}">Product Stock</a>
                </div>
            </li>
            @endif -->

            <!-- @if(Auth::guard('admin')->user()->role == '1')
            <li class="menu-item has-submenu
                {{ ($route == 'banner.index')? 'active':'' }}
                {{ ($route == 'banner.edit')? 'active':'' }}
                {{ ($route == 'banner.create')? 'active':'' }}
            ">
                <a class="menu-link" href="#">
                    <i class="fa-solid fa-images fontawesome_icon_custom"></i>
                    <span class="text">Banner</span>
                </a>
                <div class="submenu">
                    <a class="{{ ($route == 'banner.index') ? 'active':'' }}" href="{{ route('banner.index') }}">Banner List</a>
                    <a class="{{ ($route == 'banner.create') ? 'active':'' }}" href="{{ route('banner.create') }}">Banner Add</a>
                </div>
            </li>
            @endif -->

            <!-- @if(Auth::guard('admin')->user()->role == '1')
            <li class="menu-item has-submenu
                {{ ($route == 'subscribers.index')? 'active':'' }}
            ">
                <a class="menu-link" href="#">
                    <i class="fa-solid fa-envelope-open-text fontawesome_icon_custom"></i>
                    <span class="text">Subscribers</span>
                </a>
                <div class="submenu">
                    <a class="{{ ($route == 'subscribers.index') ? 'active':'' }}" href="{{ route('subscribers.index') }}">Subscribers List</a>
                </div>
            </li>
            @endif -->

            <!-- <li class="menu-item has-submenu
                {{ ($route == 'blog.index')? 'active':'' }}
                {{ ($route == 'blog.edit')? 'active':'' }}
                {{ ($route == 'blog.create')? 'active':'' }}
            ">
                @if(Auth::guard('admin')->user()->role == '1' || in_array('21', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                    <a class="menu-link" href="#">
                        <i class="fa-solid fa-newspaper fontawesome_icon_custom"></i>
                        <span class="text">Blog</span>
                    </a>
                @endif
                <div class="submenu">
                    @if(Auth::guard('admin')->user()->role == '1' || in_array('21', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($route == 'blog.index') ? 'active':'' }}" href="{{ route('blog.index') }}">Blog List</a>
                    @endif
                    @if(Auth::guard('admin')->user()->role == '1' || in_array('22', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($route == 'blog.create') ? 'active':'' }}" href="{{ route('blog.create') }}">Blog Add</a>
                    @endif
                </div>
            </li> -->

            <!-- <li class="menu-item has-submenu
                {{ ($route == 'page.index')? 'active':'' }}
                {{ ($route == 'page.edit')? 'active':'' }}
                {{ ($route == 'page.create')? 'active':'' }}
            ">
                @if(Auth::guard('admin')->user()->role == '1' || in_array('49', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                    <a class="menu-link" href="#">
                        <i class="fa-solid fa-file-lines fontawesome_icon_custom"></i>
                        <span class="text">Pages</span>
                    </a>
                @endif
                <div class="submenu">
                    @if(Auth::guard('admin')->user()->role == '1' || in_array('50', json_decode(Auth::guard('admin')->user()->staff->role->permissions)))
                        <a class="{{ ($route == 'page.create') ? 'active':'' }}" href="{{ route('page.create') }}">Pages</a>
                    @endif
                </div>
            </li> -->

            <!-- @if(Auth::guard('admin')->user()->role == '1')
                <li class="menu-item has-submenu
                {{ ($route == 'accounts.heads')? 'active':'' }}
                {{ ($route == 'accounts.ledgers')? 'active':'' }}
                {{ ($route == 'accounts.heads.create')? 'active':'' }}
                ">
                    <a class="menu-link" href="#">
                        <i class="fa-solid fa-wallet fontawesome_icon_custom"></i>
                        <span class="text">Accounts</span>
                    </a>
                    <div class="submenu">
                        <a class="{{ ($route == 'accounts.heads')? 'active':'' }} {{ ($route == 'accounts.heads.create')? 'active':'' }}" href="{{ route('accounts.heads') }}">Account Heads</a>
                        <a class="{{ ($route == 'accounts.ledgers')? 'active':'' }}" href="{{ route('accounts.ledgers') }}">Cashbook</a>
                    </div>
                </li>
            @endif -->

            <!-- @if(Auth::guard('admin')->user()->role == '1')
                <li class="menu-item has-submenu
                {{ ($route == 'customer.index')? 'active':'' }}
                ">
                    <a class="menu-link" href="#">
                        <i class="fa-solid fa-users fontawesome_icon_custom"></i>
                        <span class="text">Customers</span>
                    </a>
                    <div class="submenu">
                        <a class="{{ ($route == 'customer.index')? 'active':'' }}" href="{{ route('customer.index') }}">Customer list</a>
                    </div>
                </li>

                <li class="menu-item has-submenu
                {{ ($route == 'messages.list')? 'active':'' }}
                ">
                    <a class="menu-link" href="#">
                        <i class="fa-solid fa-envelope fontawesome_icon_custom"></i>
                        <span class="text">Messages</span>
                    </a>
                    <div class="submenu">
                        <a class="{{ ($route == 'messages.list')? 'active':'' }}" href="{{ route('messages.list') }}">User Messages</a>
                    </div>
                </li>
            @endif -->
        </ul>

        <hr />

        @if(Auth::guard('admin')->user()->role == '1')
            <ul class="menu-aside">
                <li class="menu-item has-submenu
                {{ ($route == 'setting.index')? 'active':'' }}
                {{ ($route == 'shipping.index')? 'active':'' }}
                {{ ($route == 'shipping.create')? 'active':'' }}
                {{ ($route == 'shipping.edit')? 'active':'' }}
                ">
                    <a class="menu-link" href="#">
                        <i class="fa-solid fa-gear fontawesome_icon_custom"></i>
                        <span class="text">Settings</span>
                    </a>
                    <div class="submenu">
                        <a class="{{ ($route == 'setting.index') ? 'active':'' }}" href="{{ route('setting.index') }}">Home</a>
                        <a class="{{ ($route == 'shipping.index')||($route == 'shipping.create')||($route == 'shipping.edit') ? 'active':'' }}" href="{{ route('shipping.index') }}">Shipping Methods</a>
                    </div>
                </li>
            </ul>
        @endif
    </nav>
</aside>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var menuItems = document.querySelectorAll(".navbar-aside .menu-item.has-submenu > .menu-link");
        menuItems.forEach(function(item) {
            item.addEventListener("click", function() {
                var parent = this.parentElement;
                document.querySelectorAll(".navbar-aside .menu-item.has-submenu").forEach(function(other) {
                    if (other !== parent) {
                        other.classList.remove("submenu-open");
                    }
                });
                parent.classList.toggle("submenu-open");
            });
        });
    });
</script>
