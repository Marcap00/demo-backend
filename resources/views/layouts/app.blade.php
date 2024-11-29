<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('logomark.svg') }}" type="image/x-icon">
    @yield('meta-additional')
    <title>@yield('head-title', '')</title>
    {{-- Font Awesome --}}
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css' integrity='sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==' crossorigin='anonymous'/>
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100italic,200,200italic,300,300italic,regular,italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic" rel="stylesheet" />
    @yield('cdn-additional')
</head>
<body>
    {{-- Include header --}}
    @include('includes.header')
    <main>
        {{-- Main Content --}}
        @yield('main-content')
    </main>
    {{-- Include footer --}}
    @include('includes.footer')
    {{-- JS Scripts --}}
    @yield('additional-scripts')
    @vite("resources/js/app.js")
</body>
</html>
