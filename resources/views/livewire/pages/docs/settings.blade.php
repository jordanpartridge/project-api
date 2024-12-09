<div>
    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" id="title" wire:model="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
            <textarea id="content" wire:model="content" rows="10" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
            @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
            <select id="category" wire:model="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <option value="">Select a category...</option>
                <option value="guides">Guides</option>
                <option value="tutorials">Tutorials</option>
                <option value="api">API</option>
                <option value="examples">Examples</option>
            </select>
            @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="order" class="block text-sm font-medium text-gray-700">Order</label>
            <input type="number" id="order" wire:model="order" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            @error('order') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center">
            <input type="checkbox" id="is_published" wire:model="is_published" class="rounded border-gray-300 text-indigo-600 shadow-sm">
            <label for="is_published" class="ml-2 block text-sm text-gray-900">Published</label>
        </div>

        <div>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Save Documentation
            </button>
        </div>
    </form>
</div>