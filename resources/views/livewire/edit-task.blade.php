<div>
    <h3>Edit Task Assignment</h3>

    <div class="mt-4 max-w-sm">
        <label for="user-select">Assign to User</label>

        <!-- 
            HERE IS HOW YOU USE IT:
            1. wire:model="assignedUserId" passes the initial value (5) to the component.
            2. async-search and async-url enable the async functionality.
            3. The component handles the rest automatically on page load.
        -->
        <x-ui.select 
            wire:model="assignedUserId"
            placeholder="Search for a user..."
            searchable
            async-search
            async-url="/users"

            async-paginated
            value-key="id"
            label-key="name"
            async-data-key="data"
        />
    </div>

    <div class="mt-4">
        <button wire:click="save" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
        @if (session()->has('message'))
            <span class="ml-2">{{ session('message') }}</span>
        @endif
    </div>

    <div class="mt-8">
        Current Assigned User ID: {{ $assignedUserId }}
    </div>
</div>