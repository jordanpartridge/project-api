<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Maintenance</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/lucide.min.js"></script>
    <style>
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        @keyframes pulse {
            50% { opacity: .5; }
        }
        @keyframes dots {
            0%, 20% { content: '.'; }
            40%, 60% { content: '..'; }
            80%, 100% { content: '...'; }
        }
        .animate-dots::after {
            content: '';
            animation: dots 1.5s infinite;
        }
        .gear-icon:hover {
            animation: spin 2s linear infinite;
        }
    </style>
</head>
<body class="antialiased">
<div class="min-h-screen bg-gradient-to-br from-gray-900 to-gray-800 flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <!-- Main Card -->
        <div class="bg-gray-800/50 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-700/50 p-8">
            <!-- Header Section -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center space-x-3">
                    <i class="gear-icon text-blue-400" data-lucide="settings-2" style="width: 32px; height: 32px;"></i>
                    <div>
                        <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                            @isset($errorCode)
                                {{ $errorCode }}
                            @else
                                503
                            @endisset
                        </h1>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="text-blue-400" data-lucide="clock" style="width: 20px; height: 20px;"></i>
                    <span class="text-gray-400 font-mono">System Maintenance</span>
                </div>
            </div>

            <!-- Message Section -->
            <div class="space-y-6">
                <h2 class="text-2xl font-bold text-white animate-dots">
                    @isset($title)
                        {{ $title }}
                    @else
                        We're upgrading our systems
                    @endisset
                </h2>

                <p class="text-gray-300 leading-relaxed">
                    @isset($message)
                        {{ $message }}
                    @else
                        Our team is performing scheduled maintenance to improve your experience.
                        We'll be back shortly with new features and improvements.
                    @endisset
                </p>

                <!-- Status Indicator -->
                <div class="flex items-center space-x-2 bg-blue-500/10 p-4 rounded-lg border border-blue-500/20">
                    <i class="text-blue-400" data-lucide="alert-circle" style="width: 20px; height: 20px;"></i>
                    <span class="text-blue-300">
                            @isset($estimatedTime)
                            Estimated completion: {{ $estimatedTime }}
                        @else
                            Estimated completion: ~15 minutes
                        @endisset
                        </span>
                </div>

                <!-- Progress Bar -->
                <div class="relative h-2 bg-gray-700 rounded-full overflow-hidden">
                    <div class="absolute top-0 left-0 h-full w-2/3 bg-gradient-to-r from-blue-500 to-purple-500 animate-pulse rounded-full"></div>
                </div>

                <!-- Additional Info -->
                <div class="pt-6 border-t border-gray-700/50">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-400">
                        <div class="space-y-1">
                            <div class="font-medium text-gray-300">Need assistance?</div>
                            <div>Contact {{ config('app.support_email', 'support@example.com') }}</div>
                        </div>
                        <div class="space-y-1">
                            <div class="font-medium text-gray-300">Status updates</div>
                            <div>Follow us {{ config('app.status_handle', '@status_example') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Initialize Lucide icons
    lucide.createIcons();
</script>
</body>
</html>
