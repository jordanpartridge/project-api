<div class="grid grid-cols-3 gap-4 bg-gray-800 p-6 rounded-lg border border-gray-700">
    <div class="text-center">
        <div class="text-sm font-medium text-gray-400">Stars</div>
        <div class="mt-1 text-xl font-semibold text-gray-200">{{ $getRecord()->stars_count ?? '0' }}</div>
    </div>
    <div class="text-center">
        <div class="text-sm font-medium text-gray-400">Forks</div>
        <div class="mt-1 text-xl font-semibold text-gray-200">{{ $getRecord()->forks_count ?? '0' }}</div>
    </div>
    <div class="text-center">
        <div class="text-sm font-medium text-gray-400">Issues</div>
        <div class="mt-1 text-xl font-semibold text-gray-200">{{ $getRecord()->open_issues_count ?? '0' }}</div>
    </div>
</div>
