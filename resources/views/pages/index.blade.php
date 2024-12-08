<?php

use function Livewire\Volt\state;

state(['showFeatures' => false]);
?>

@volt
<div class="relative overflow-hidden bg-slate-900">
    <!-- Hero Section -->
    <div class="relative pt-10 pb-20 sm:pt-16 sm:pb-24 lg:pt-24">
        <div class="px-4 mx-auto max-w-7xl sm:px-6">
            <div class="text-center">
                <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl md:text-6xl">
                    <span class="block">Project-API</span>
                    <span class="block text-blue-500">GitHub Project Management</span>
                </h1>
                <p class="max-w-md mx-auto mt-3 text-base text-slate-300 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                    The ultimate solution for managing GitHub projects, repositories, and documentation. Built with Laravel, enhanced with real-time capabilities.
                </p>
                <div class="max-w-md mx-auto mt-5 sm:flex sm:justify-center md:mt-8">
                    <div class="rounded-md shadow">
                        <a href="/docs" class="flex items-center justify-center w-full px-8 py-3 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 md:px-10 md:py-4 md:text-lg">
                            View Documentation
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="relative py-16 bg-white sm:py-24 lg:py-32">
        <div class="max-w-md px-4 mx-auto text-center sm:max-w-3xl sm:px-6 lg:max-w-7xl lg:px-8">
            <h2 class="text-base font-semibold tracking-wider text-blue-600 uppercase">Everything you need</h2>
            <p class="mt-2 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                Powerful features for modern development
            </p>

            <!-- Feature Grid -->
            <div class="mt-12">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Feature Cards -->
                    @foreach ([
                        [
                            'title' => 'GitHub Integration',
                            'description' => 'Seamless integration with GitHub repositories. Track commits, manage files, and monitor changes in real-time.',
                        ],
                        [
                            'title' => 'Project Management',
                            'description' => 'Organize and track your projects with powerful management tools.',
                        ],
                        [
                            'title' => 'Real-time Updates',
                            'description' => 'Stay in sync with automatic updates and real-time notifications.',
                        ]
                    ] as $feature)
                        <div class="pt-6">
                            <div class="flow-root px-6 pb-8 rounded-lg bg-slate-50">
                                <div class="-mt-6">
                                    <div>
                                        <span class="inline-flex items-center justify-center p-3 rounded-md shadow-lg bg-blue-500">
                                            <x-icon :name="$feature['icon']" class="w-6 h-6 text-white" />
                                        </span>
                                    </div>
                                    <h3 class="mt-8 text-lg font-medium tracking-tight text-slate-900">
                                        {{ $feature['title'] }}
                                    </h3>
                                    <p class="mt-5 text-base text-slate-500">
                                        {{ $feature['description'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
