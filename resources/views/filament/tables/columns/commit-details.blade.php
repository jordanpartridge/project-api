<div class="space-y-2">
    <div class="flex items-center space-x-2">
        <code class="px-2 py-1 bg-gray-800 rounded-md text-gray-300 text-xs font-mono">{{ $sha }}</code>
        <span class="text-xs text-gray-500">{{ $date }}</span>
    </div>
    <div class="text-sm text-gray-400">{{ $message }}</div>
    <div class="flex items-center space-x-2 text-xs text-gray-500">
        <span>{{ $authorName }}</span>
        <span class="text-gray-600">&lt;{{ $authorEmail }}&gt;</span>
    </div>
</div>
