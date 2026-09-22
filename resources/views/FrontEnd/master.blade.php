<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{get_setting('business_name')->value}} | @yield('title')</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="shortcut icon" href="{{asset(get_setting('site_favicon')->value ?? '')}}" type="image/x-icon">
    
    @unless(request()->routeIs('home'))
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" />
    @endunless
    @include('FrontEnd.include.style')
    <link rel="stylesheet" href="{{ asset('FrontEnd/assect/css/products.css') }}?v={{ filemtime(public_path('FrontEnd/assect/css/products.css')) }}">
    @stack('styles')

</head>

<body class="{{ request()->routeIs('home') ? 'rf-home-page' : '' }}">
    
    @include('FrontEnd.include.header')

<main>
        @yield('content')
    </main>

@include('FrontEnd.include.footer')

@unless(request()->routeIs('home'))
        @include('FrontEnd.partials.legacy-quick-view')
    @endunless
    <x-product-quick-view />
    @if(request()->routeIs('home'))
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
    @else
        @include('FrontEnd.include.script')
    @endif
    <script src="{{ asset('FrontEnd/assect/js/products.js') }}?v={{ filemtime(public_path('FrontEnd/assect/js/products.js')) }}" defer></script>
    @stack('js')
</body>

</html>
