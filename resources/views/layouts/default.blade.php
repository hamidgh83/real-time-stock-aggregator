<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite('resources/css/app.css')
    <title>Stock Price Monitoring</title>
</head>
<body>
    <header class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
        @include('header')
    </header>
    <main class="mx-auto max-w-screen-xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        @yield('content')
    </main>

    @vite('resources/js/app.js')
</body>
</html>
