<x-filament::section>
    <x-slot name="heading">Recent Documentation Activity</x-slot>

    <div class="space-y-4">
        @foreach ($activities as $activity)
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    @if ($activity->event === 'created')
                        <x-heroicon-o-plus-circle class="w-6 h-6 text-success-500" />
                    @elseif ($activity->event === 'updated')
                        <x-heroicon-o-pencil class="w-6 h-6 text-warning-500" />
                    @elseif ($activity->event === 'deleted')
                        <x-heroicon-o-trash class="w-6 h-6 text-danger-500" />
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                        {{ $activity->description }}
                    </p>

                    <div class="mt-1 flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                        @if ($activity->causer)
                            <span>{{ $activity->causer->name }}</span>
                            <span>&middot;</span>
                        @endif
                        <span>{{ $activity->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-filament::section>