<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Project-API' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50">
    <div class="min-h-screen">
        <nav class="bg-white shadow">
            <div class="container mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <a href="{{ route('home') }}" class="flex items-center text-slate-900">
                            Project-API
                        </a>
                    </div>
                    <div class="flex items-center">
                        <a href="{{ route('docs') }}" class="text-slate-600 hover:text-slate-900">Documentation</a>
                    </div>
                </div>
            </div>
        </nav>

        <main>
            {{ $slot }}
        </main>
    </div>
</body>
</html>