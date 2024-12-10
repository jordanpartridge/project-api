@volt('welcome')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project-API</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-4xl font-bold text-slate-900">Project-API</h1>
        <div class="mt-8">
            <a href="{{ route('docs') }}" class="text-blue-600 hover:underline">Documentation</a>
        </div>

        @if ($latestDocs->isNotEmpty())
        <div class="mt-8">
            <h2 class="text-2xl font-semibold">Latest Documentation</h2>
            <div class="mt-4 space-y-4">
                @foreach ($latestDocs as $doc)
                <div class="p-4 bg-white rounded-lg shadow-sm">
                    <h3 class="text-lg font-medium">{{ $doc->title }}</h3>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</body>
</html>