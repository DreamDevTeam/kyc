<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Error!</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="{{asset('css/loader.css')}}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <!-- Styles -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</head>
<body class="container page-info">
<p class="text">This link has been used!</p>
</body>




<style>
    .page-info{
        display: flex;
        align-items: center;
        justify-content: center;
        /*background: #337ecc;*/
        min-height: 100vh;
    }
    .text{
        font-size: 60px;
        color:white;
        color:#337ecc;
    }
    @media (max-width: 768px) {
        .text{
            font-size: 60px;
            text-align: center;
            color:#337ecc;
        }
    }
</style>
</html>
