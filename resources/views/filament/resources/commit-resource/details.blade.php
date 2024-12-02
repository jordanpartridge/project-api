{{-- resources/views/filament/resources/commit-resource/details.blade.php --}}
<div class="space-y-6">
    <div class="space-y-2">
        <div class="text-lg font-medium">Commit Details</div>
        <div class="text-sm text-gray-500">Full information about this commit</div>
    </div>

    <div class="grid grid-cols-1 gap-4">
        <div class="space-y-2">
            <div class="text-sm font-medium">Repository</div>
            <div class="text-sm text-gray-500">{{ $commit->repo->full_name }}</div>
        </div>

        <div class="space-y-2">
            <div class="text-sm font-medium">SHA</div>
            <code class="px-2 py-1 bg-gray-800 rounded-md text-gray-300 text-sm font-mono">{{ $commit->sha }}</code>
        </div>

        <div class="space-y-2">
            <div class="text-sm font-medium">Message</div>
            <div class="text-sm text-gray-500 whitespace-pre-wrap">{{ $commit->message }}</div>
        </div>

        <div class="space-y-2">
            <div class="text-sm font-medium">Author</div>
            <div class="text-sm text-gray-500">
                {{ $commit->author['name'] }} &lt;{{ $commit->author['email'] }}&gt;
            </div>
            <div class="text-xs text-gray-500">
                Committed {{ \Carbon\Carbon::parse($commit->author['date'])->diffForHumans() }}
                ({{ \Carbon\Carbon::parse($commit->author['date'])->format('F j, Y g:i A') }})
            </div>
        </div>
    </div>
</div>
