<div class="relative flex items-center w-full px-4 {{ $backgroundColor }}">
    {{-- Current Panel Indicator --}}
    <div class="py-4">
        <span class="text-white font-medium">
            {{ ucfirst($currentPanel) }} Panel
        </span>
    </div>

    {{-- Panel Switcher --}}
    <div class="ml-auto flex items-center space-x-4">
        @if (auth()->check())
            @can('access-admin')
                <a href="/admin" class="px-3 py-2 text-sm text-white hover:bg-white/10 rounded-lg transition {{ $currentPanel === 'admin' ? 'bg-white/20' : '' }}">
                    Admin
                </a>
            @endcan

            @can('access-support')
                <a href="/support" class="px-3 py-2 text-sm text-white hover:bg-white/10 rounded-lg transition {{ $currentPanel === 'support' ? 'bg-white/20' : '' }}">
                    Support
                </a>
            @endcan

            <a href="/" class="px-3 py-2 text-sm text-white hover:bg-white/10 rounded-lg transition {{ $currentPanel === 'main' ? 'bg-white/20' : '' }}">
                Main Site
            </a>
        @endif
    </div>
</div>
