<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Maintenance</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="antialiased">
<div class="relative flex items-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center py-4 sm:pt-0">
    <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
        <div class="flex items-center pt-8 sm:justify-start sm:pt-0">
            <div class="px-4 text-lg text-gray-500 border-r border-gray-400 tracking-wider">
                503
            </div>
            <div class="ml-4 text-lg text-gray-500 uppercase tracking-wider">
                Under Maintenance
            </div>
        </div>
        <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="text-lg leading-7 font-semibold">
                        We'll be back soon!
                    </div>
                </div>
                <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                    The site is currently undergoing scheduled maintenance. We'll be back shortly.
                    Please try again in a few minutes.
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
