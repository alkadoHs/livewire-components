@aware([
    'searchable' => false,
])

@props([
    'checkIcon' => 'check',
    'checkIconClass' => '',
])

<x-ui.popup
    x-show="open"
    x-on:click.away="open = false"
    x-anchor.offset.3="$refs.selectTrigger"
>
    @if ($searchable)
        <div class="grid items-center justify-center grid-cols-[20px_1fr] px-2 mb-1 w-full border-b border-neutral-200 dark:border-neutral-700">
            <x-ui.icon 
                name="magnifying-glass"
                class="col-span-1 col-start-1 row-start-1 !text-neutral-500 dark:!text-neutral-400 !size-5"
            />
            <input 
                x-model="search"
                type="text"
                placeholder="search..."
                class="w-full bg-transparent placeholder:text-neutral-500 ring-0 outline-none border-0 col-span-4 col-start-1 row-start-1 pl-6"
            >
        </div>
    @endif
    
    <ul 
        role="listbox" 
        class="grid grid-cols-[auto_auto_1fr] gap-y-1 overflow-y-auto max-h-60 p-1"
    >
        <!-- Initial Loading Indicator -->
        <template x-if="isLoading">
            <li class="col-span-full flex items-center justify-center h-14">
                <svg class="animate-spin size-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
            </li>
        </template>
        
        <!-- Rendered Options (using the original structure) -->
        <template x-if="!isLoading && options.length > 0">
            <template x-for="option in options" :key="option.value">
                <li
                    role="option"
                    data-slot="option"
                    :data-value="option.value"
                    :data-label="option.label"
                    x-on:click="select(option.value)"
                    :class="{ 
                        'bg-neutral-200 dark:bg-neutral-700': isSelected(option.value),
                        'font-semibold': isSelected(option.value)
                    }"
                    class="rounded-[calc(var(--popup-round)-var(--popup-padding))] col-span-full grid grid-cols-subgrid items-center focus:bg-neutral-100 dark:focus:bg-neutral-700 px-3 py-0.5 w-full text-[1rem] self-center gap-x-2 cursor-pointer hover:bg-neutral-100 dark:hover:bg-neutral-700"
                >
                    <x-ui.icon 
                        name="{{ $checkIcon }}"
                        :class="isSelected(option.value) ? 'opacity-100' : 'opacity-0'"
                        @class([
                            'z-10 place-self-center size-[1.15rem]',
                            $checkIconClass,
                        ])
                    />
                    {{-- Note: Per-option icons are not supported in async mode without API changes --}}
                    <span x-text="option.label" class="col-start-3 text-start text-neutral-950 dark:text-neutral-50"></span>
                </li>
            </template>
        </template>
        
        <!-- "Load More" Sentinel and Indicator -->
        <template x-if="isPaginated && nextPageUrl && !isLoading">
            <li
                x-intersect:enter="loadMore()"
                class="col-span-full flex items-center justify-center h-10 text-sm text-neutral-500"
            >
                <template x-if="isLoadingMore">
                    <svg class="animate-spin size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                </template>
            </li>
        </template>

        <!-- No Results Message -->
        <template x-if="!isLoading && options.length === 0">
             <li class="col-span-full px-3 py-2 text-center text-neutral-500">
                no results found
             </li>
        </template>
    </ul>
</x-ui.popup>