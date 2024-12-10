<?php

use App\Models\Documentation;

use function Livewire\Volt\state;

state(['doc' => fn ($slug) => Documentation::where('slug', $slug)->firstOrFail()]);

?>

<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1>{{ $doc->title }}</h1>
                    {!! $doc->content !!}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>