@if (app()->environment('local'))
    <div class="mt-2 space-y-2">
        <h3 class="text-center font-medium text-gray-600">Quick Login Links (Local Only)</h3>
        <div class="flex flex-col space-y-2">
            @foreach (\App\Models\User::all() as $user)
                <a href="{{ \Spatie\LoginLink\Http\Controllers\LoginLinkController::createLink($user) }}"
                   class="text-sm text-center text-gray-600 hover:text-primary-500">
                    Login as {{ $user->name }} ({{ $user->roles->pluck('name')->implode(', ') }})
                </a>
            @endforeach
        </div>
    </div>
@endif