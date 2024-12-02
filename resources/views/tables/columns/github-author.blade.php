<div class="flex items-center gap-2">
    <div class="text-sm">
        <div class="font-medium">{{ $getState()['name'] }}</div>
        <div class="text-gray-500">{{ $getState()['email'] }}</div>
        <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($getState()['date'])->diffForHumans() }}</div>
    </div>
</div>
