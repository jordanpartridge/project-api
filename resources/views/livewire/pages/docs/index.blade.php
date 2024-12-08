<div>
    <div class="space-y-4">
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