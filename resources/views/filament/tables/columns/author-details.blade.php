
{{-- resources/views/filament/tables/columns/author-details.blade.php --}}
<div class="flex flex-col space-y-1">
    <div class="flex items-center space-x-2">
        <span class="text-sm font-medium">{{ $name }}</span>
        <span class="text-xs text-gray-500">&lt;{{ $email }}&gt;</span>
    </div>
    <span class="text-xs text-gray-500">{{ $date }}</span>
</div>
