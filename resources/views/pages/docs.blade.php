@php
    $selectedCategory = request('category');
    $docs = App\Models\Documentation::query()
        ->published()
        ->when($selectedCategory, function($query) use ($selectedCategory) {
            $query->where('category', $selectedCategory);
        })
        ->orderBy('order')
        ->get();

    $categories = App\Models\Documentation::published()
        ->select('category')
        ->distinct()
        ->orderBy('category')
        ->pluck('category');
@endphp

<x-app-layout>
    <div class="min-h-screen bg-slate-50">
        <!-- Top Navigation Bar -->
        <nav class="bg-white border-b">
            <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex items-center shrink-0">
                            <a href="{{ route('home') }}" class="text-xl font-bold text-slate-900">
                                Project-API
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="py-10">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200 lg:p-8">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <!-- Sidebar -->
                            <div class="md:col-span-1">
                                <nav class="space-y-2">
                                    <a
                                        href="{{ route('docs') }}"
                                        class="block px-3 py-2 rounded-md text-sm font-medium {{ !$selectedCategory ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50' }}"
                                    >
                                        All Documentation
                                    </a>
                                    @foreach ($categories as $category)
                                        <a
                                            href="{{ route('docs', ['category' => $category]) }}"
                                            class="block px-3 py-2 rounded-md text-sm font-medium {{ $selectedCategory === $category ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-50' }}"
                                        >
                                            {{ ucfirst($category) }}
                                            <span class="ml-2 text-xs text-slate-500">
                                                ({{ $docs->where('category', $category)->count() }})
                                            </span>
                                        </a>
                                    @endforeach
                                </nav>
                            </div>

                            <!-- Main Content -->
                            <div class="md:col-span-3">
                                <div class="space-y-6">
                                    @foreach ($docs as $doc)
                                        <div class="p-6 bg-white rounded-lg shadow-sm">
                                            <h2 class="text-xl font-semibold text-slate-900">
                                                {{ $doc->title }}
                                            </h2>
                                            <div class="mt-2 prose max-w-none">
                                                {!! Str::markdown($doc->content) !!}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>