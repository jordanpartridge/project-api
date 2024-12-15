@php
$features = [
    [
        'title' => 'GitHub Integration',
        'description' => 'Seamless integration with GitHub repositories. Track commits, manage files, and monitor changes in real-time.',
        'icon' => 'github'
    ],
    [
        'title' => 'Project Management',
        'description' => 'Organize and track your projects with powerful management tools.',
        'icon' => 'project-diagram'
    ],
    [
        'title' => 'Real-time Updates',
        'description' => 'Stay in sync with automatic updates and real-time notifications.',
        'icon' => 'bell'
    ]
];
@endphp

<x-app-layout>
    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            @foreach ($features as $feature)
                <div class="p-6 bg-white shadow-xl rounded-lg overflow-hidden">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            @if(isset($feature['icon']))
                                <div class="inline-flex items-center justify-center p-3 rounded-md shadow-lg bg-blue-500">
                                    <x-dynamic-component 
                                        :component="'icon.' . $feature['icon']" 
                                        class="w-6 h-6 text-white"
                                        :fallback="'icon.default'"
                                    />
                                </div>
                            @else
                                <div class="inline-flex items-center justify-center p-3 rounded-md shadow-lg bg-gray-500">
                                    <x-icon name="default" class="w-6 h-6 text-white" />
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900">{{ $feature['title'] }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ $feature['description'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>