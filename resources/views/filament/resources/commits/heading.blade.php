{{-- resources/views/filament/resources/commits/heading.blade.php --}}
<div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-bold tracking-tight">Commits Overview</h2>
    <div class="flex items-center space-x-4">
        <div class="flex flex-col items-center px-4 py-2 bg-gray-800 rounded-lg">
            <span class="text-sm text-gray-400">Today</span>
            <span class="text-xl font-bold text-white">{{ $todayCount }}</span>
        </div>
        <div class="flex flex-col items-center px-4 py-2 bg-gray-800 rounded-lg">
            <span class="text-sm text-gray-400">This Week</span>
            <span class="text-xl font-bold text-white">{{ $weekCount }}</span>
        </div>
        <div class="flex flex-col items-center px-4 py-2 bg-gray-800 rounded-lg">
            <span class="text-sm text-gray-400">This Month</span>
            <span class="text-xl font-bold text-white">{{ $monthCount }}</span>
        </div>
    </div>
</div>
