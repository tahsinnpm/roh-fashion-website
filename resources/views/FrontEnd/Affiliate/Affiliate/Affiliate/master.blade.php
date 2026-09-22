<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Affiliate Panel')</title>

    {{-- Bootstrap & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />

    {{-- Custom CSS --}}
    @include('FrontEnd.Affiliate.include.style')
</head>
<body class="bg-light">

    {{-- Header --}}
    @include('FrontEnd.Affiliate.include.header')

    <div class="container-fluid">
        <div class="row">

            {{-- Sidebar --}}
            <div class="col-lg-3 col-md-4 mb-3">
                @include('FrontEnd.Affiliate.include.sidebar')
            </div>

            {{-- Main Content --}}
            <div class="col-lg-9 col-md-8">
                @yield('content')
            </div>

        </div>
    </div>

    {{-- Footer Scripts --}}
    @include('FrontEnd.Affiliate.include.scripts')

    {{-- Clipboard JS --}}
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert("ক্লিপবোর্ডে কপি হয়েছে!");
            }).catch(err => {
                console.error("কপি করা যায়নি: ", err);
            });
        }
    </script>

    @stack('scripts') {{-- For pushing page-specific scripts --}}
</body>
</html>
