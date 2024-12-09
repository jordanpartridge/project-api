<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="font-sans antialiased bg-gray-50 text-gray-800">
<div class="min-h-screen flex flex-col">
    <!-- Page Heading -->
    @if (isset($header))
        <header class="bg-white shadow-md sticky top-0 z-10">
            <div class="container mx-auto px-4 py-4 sm:px-6 lg:px-8">
                <h1 class="text-lg font-semibold text-gray-900">
                    {{ $header }}
                </h1>
            </div>
        </header>
    @endif

    <!-- Page Content -->
    <main class="flex-1">
        <div class="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-100 border-t border-gray-200">
        <div class="container mx-auto px-4 py-4 sm:px-6 lg:px-8 text-center text-sm text-gray-600">
            © {{ now()->year }} {{ config('app.name', 'Laravel') }}. All rights reserved.
        </div>
    </footer>
</div>
</body>
</html>
