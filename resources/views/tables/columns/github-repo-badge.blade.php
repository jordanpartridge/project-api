<div class="flex items-center">
    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm bg-gray-100 dark:bg-gray-800">
        <a href="/admin/repos/{{ $getRecord()->repo_id }}"
           class="text-gray-700 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-400 transition"
           title="View in Project API">
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-4 h-4"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
        </a>

        <div class="w-px h-4 bg-gray-300 dark:bg-gray-600"></div>

        <a href="{{ $getRecord()->repo?->url }}"
           target="_blank"
           title="View on GitHub"
           class="inline-flex items-center gap-1.5 text-gray-700 dark:text-gray-300 hover:text-primary-500 dark:hover:text-primary-400 transition">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
            </svg>
            <span class="font-mono">{{ Str::limit($getState(), 7) }}</span>
        </a>
    </div>
</div>
