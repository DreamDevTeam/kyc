<!doctype html>
<html>
<head>
    <meta name=”color-scheme” content=”light dark”>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css">
    @include('template.head')
</head>

<body class="bg-light d-flex w-100" style="font-family: Open sans, Arial, san-sarif; min-height: 100vh">
    <div class="d-flex flex-column tx-center align-items-center justify-content-center m-3">
        <div class="card rounded-10 shadow-sm p-4 align-items-center overflow-hidden" style="max-width: 512px;" id='card'>

            @include('template.header')
            @yield('content')
            @include('template.footer')

        </div>
    </div>
<script src="/js/country.js"></script>
</body>
</html>
