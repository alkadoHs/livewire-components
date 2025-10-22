@aware([
    'searchable' => false,
])

@props([
    // We keep these props as they are necessary for the dynamic list rendering
    'checkIcon' => 'check',
    'checkIconClass' => '',
])

<x-ui.popup
    x-show="open"
    x-on:click.away="close()"
    x-on:keydown.escape="close()"
    x-anchor.offset.3="$refs.selectTrigger"
>
    @if ($searchable)
        {{-- This is your original, preserved search box structure --}}
        <div
            @class([
                'grid items-center justify-center grid-cols-[20px_1fr] px-2 mb-1',
                '[&>[data-slot=icon]+[data-slot=search-control]]:pl-6', 
                'w-full border-b border-neutral-200 dark:border-neutral-700',
            ])    
        >
            <x-ui.icon 
                name="magnifying-glass"
                class="col-span-1 col-start-1 row-start-1 !text-neutral-500 dark:!text-neutral-400 !size-5"
            />

            <input 
                x-model="search"
                x-on:input.stop="isTyping = true"
                x-on:keydown.down.prevent.stop="handleKeydown($event)"
                x-on:keydown.up.prevent.stop="handleKeydown($event)"
                x-on:keydown.enter.prevent.stop="handleKeydown($event)"
                x-bind:aria-activedescendant="activeIndex !== null ? 'option-' + activeIndex : null"
                type="text"
                x-ref='searchControl'
                data-slot="search-control"
                placeholder="search..."
                @class([
                    'bg-transparent py-1.5 placeholder:text-neutral-500 dark:placeholder:text-neutral-400 dark:text-neutral-50 text-neutral-900 ',
                    'ring-0 ring-offset-0 outline-none focus:ring-0 border-0',
                    'col-span-4 col-start-1 row-start-1',
                ])
            >
        </div>
    @endif
    
    {{-- This is your original UL structure with dynamic content injected --}}
    <ul 
        role="listbox"
        {{-- The original keydown handlers are preserved for non-async focus navigation if needed --}}
        x-on:keydown.enter.prevent.stop="!isAsync && select($focus.focused().dataset.value)"
        x-on:keydown.up.prevent.stop="!isAsync && $focus.wrap().prev()"
        x-on:keydown.down.prevent.stop="!isAsync && $focus.wrap().next()"
        class="grid grid-cols-[auto_auto_1fr] gap-y-1 overflow-y-auto max-h-60 p-1"
    >
        <!-- Initial Loading Indicator -->
        <template x-if="isLoading">
            <li class="col-span-full flex items-center justify-center h-14">
                <svg class="animate-spin size-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
            </li>
        </template>
        
        <!-- Logic for static (non-async) options, using the original slot -->
        <template x-if="!isAsync">
             {{ $slot }}
        </template>

        <!-- Logic for ASYNC options, rendered with the original design -->
        <template x-if="isAsync && !isLoading">
            <template x-for="option in options" :key="option.value">
                <li
                    tabindex="0"
                    role="option"
                    data-slot="option"
                    :data-value="option.value"
                    :data-label="option.label"
                    x-on:click="select(option.value)"
                    x-on:mouseenter="handleMouseEnter(option.value)"
                    :id="'option-' + getFilteredIndex(option.value)"
                    :class="{ 
                        'bg-neutral-300 dark:bg-neutral-700': isFocused(option.value),
                        '[&>[data-slot=icon]]:opacity-100': isSelected(option.value),
                    }"
                    class="rounded-[calc(var(--popup-round)-var(--popup-padding))] col-span-full grid grid-cols-subgrid items-center focus:bg-neutral-100 dark:focus:bg-neutral-700 px-3 py-0.5 w-full text-[1rem] self-center gap-x-2 cursor-pointer"
                >
                    <x-ui.icon 
                        data-slot="icon"
                        name="{{ $checkIcon }}"
                        @class([
                            'z-10 place-self-center opacity-0 size-[1.15rem]',
                            $checkIconClass,
                        ])
                    />
                    <span x-text="option.label" class="col-start-3 text-start text-neutral-950 dark:text-neutral-50"></span>
                </li>
            </template>
        </template>
        
        <!-- "Load More" Sentinel and Indicator (for async paginated only) -->
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
    </ul>

    {{-- Adapted "No Results" message to work with our new logic --}}
    <template x-if="!isLoading && options.length === 0 && (isTyping || (isAsync && open))">
        <div class="h-14 flex items-center justify-center">
            <span class="text-neutral-500">no results found</span>
        </div>
    </template>
</x-ui.popup>