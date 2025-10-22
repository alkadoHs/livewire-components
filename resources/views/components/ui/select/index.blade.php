@props([
    // --- ORIGINAL PROPS ---
    'name' => $attributes->whereStartsWith('wire:model')->first() ?? $attributes->whereStartsWith('x-model')->first(),
    'label' => null,
    'triggerLabel' => null,
    'placeholder' => null,
    'searchable' => false,
    'multiple' => false,
    'clearable' => false,
    'disabled' => false,
    'icon' => null,
    'iconAfter' => 'chevron-up-down',
    'checkIcon' => 'check',
    'checkIconClass' => null,
    'invalid' => null,
    'triggerClass' => null,
    
    // --- ASYNC & PAGINATION PROPS ---
    'asyncSearch' => false,
    'asyncUrl' => '',
    'asyncPaginated' => false,
    'valueKey' => 'value',
    'labelKey' => 'label',
    'asyncDataKey' => 'data',
])

<div 
    x-data="{
        search: '',
        open: false,
        isTyping: false,
        activeIndex: null,
        
        // --- DATA STATE ---
        options:[],
        staticOptions: [],
        
        isMultiple: @js($multiple),
        isDisabled: @js($disabled),
        isSearchable: @js($searchable),
        
        state: @js($multiple) ? [] : null,
        placeholder: @js($placeholder) ?? 'select ...',

        // --- ASYNC STATE ---
        isAsync: @js($asyncSearch),
        asyncUrl: @js($asyncUrl),
        isLoading: false,
        selectedItem: null,

        // --- PAGINATION STATE ---
        isPaginated: @js($asyncPaginated),
        valueKey: @js($valueKey),
        labelKey: @js($labelKey),
        dataKey: @js($asyncDataKey),
        nextPageUrl: null,
        isLoadingMore: false,

        // ❗ NEW: Add the cache object for client-side caching
        cache: {},

        init() {
            const initialState = this.$root?._x_model?.get();
            this.state = initialState;

            if (this.isAsync && this.state && !this.isMultiple) {
                fetch(`${this.asyncUrl}/${this.state}`)
                    .then(r => r.json())
                    .then(response => {
                        const data = response.data ?? response; 
                        this.selectedItem = this.mapToOption(data);
                    })
                    .catch(error => console.error('Could not load pre-selected item.', error));
            }
            
            if (!this.isAsync) {
                this.$nextTick(() => {
                    this.staticOptions = Array.from(this.$el.querySelectorAll('[data-slot=option]:not([hidden])'))
                        .map(opt => ({ value: opt.dataset.value, label: opt.dataset.label, element: opt }));
                    this.options = this.staticOptions;
                });
            }

            this.$watch('state', (value) => { this.$root?._x_model?.set(value); });

            this.$watch('search', Alpine.debounce((val) => {
                this.isTyping = val.trim().length > 0;
                if (!this.isAsync) {
                    this.options = this.staticOptions.filter(opt => this.contains(opt.label, val));
                    this.activeIndex = this.options.length > 0 ? 0 : null;
                    return;
                }
                this.options = [];
                this.nextPageUrl = null;
                this.fetchOptions(val);
            }, 300));
        },

        mapToOption(item) {
            if (!item) return null;
            return { value: item[this.valueKey], label: item[this.labelKey] };
        },
        
        // ❗ MODIFIED: Checks the cache before making an API call
        fetchOptions(query) {
            if (!this.asyncUrl) return;

            // --- CACHE HIT ---
            if (this.cache[query]) {
                const cached = this.cache[query];
                this.options = cached.options;
                this.nextPageUrl = cached.nextPageUrl;
                this.activeIndex = 0;
                return; // Use cached data and exit
            }
            // --- END CACHE HIT ---

            // --- CACHE MISS ---
            this.isLoading = true;
            const url = new URL(this.asyncUrl, window.location.origin);
            url.searchParams.set('search', query);

            fetch(url.toString())
                .then(r => r.json())
                .then(response => {
                    const results = this.isPaginated ? response[this.dataKey] : response;
                    const mappedOptions = results.map(item => this.mapToOption(item));
                    const nextPage = this.isPaginated ? (response.links?.next ?? response.next_page_url) : null;
                    
                    this.options = mappedOptions;
                    this.nextPageUrl = nextPage;

                    // ❗ NEW: Store fresh results in the cache
                    this.cache[query] = { options: mappedOptions, nextPageUrl: nextPage };

                }).catch(err => console.error(err)).finally(() => { this.isLoading = false; this.activeIndex = 0; });
        },

        // ❗ MODIFIED: Updates the cache after loading more items
        loadMore() {
            if (!this.nextPageUrl || this.isLoadingMore || this.isLoading) return;
            
            this.isLoadingMore = true;
            fetch(this.nextPageUrl)
                .then(r => r.json())
                .then(response => {
                    const newOptions = response[this.dataKey].map(item => this.mapToOption(item));
                    const newNextPageUrl = response.links?.next ?? response.next_page_url;

                    this.options = [...this.options, ...newOptions];
                    this.nextPageUrl = newNextPageUrl;
                    
                    // ❗ NEW: Update the cache with the appended data and new next page URL
                    const currentQuery = this.search;
                    if (this.cache[currentQuery]) {
                        this.cache[currentQuery].options = this.options;
                        this.cache[currentQuery].nextPageUrl = newNextPageUrl;
                    }

                }).catch(err => console.error(err)).finally(() => { this.isLoadingMore = false; });
        },
        
        select(optionValue) {
            this.isTyping = false;
            this.search = '';

            if (!this.isMultiple) {
                this.open = false;
                this.state = optionValue;
                if(this.isAsync) {
                    this.selectedItem = this.options.find(opt => opt.value == optionValue);
                }
                return;
            }
            
            if(!Array.isArray(this.state)){ console.error('Multiple select requires an array value.'); }        
            const itemIndex = this.state.findIndex(item => item == optionValue);
            if (itemIndex === -1) { this.state.push(optionValue); } 
            else { this.state.splice(itemIndex, 1); }
        },
        
        clear() {
            this.state = this.isMultiple ? [] : null;
            this.open = false;
            this.selectedItem = null;
        },

        close() {
            this.open = false;
            this.search = '';
            this.isTyping = false;
            this.activeIndex = null;
        },

        toggle() {
            if (this.isDisabled) return;
            this.open = !this.open;
            if (this.open && this.isAsync && this.options.length === 0) {
                this.fetchOptions(this.search); // Use current search term or '' for default
            }
        },

        handleKeydown(event) {
            if (event.key === 'ArrowDown') {
                event.preventDefault(); event.stopPropagation();
                if (this.activeIndex === null || this.activeIndex >= this.options.length - 1) { this.activeIndex = 0; } 
                else { this.activeIndex++; }
            } else if (event.key === 'ArrowUp') {
                event.preventDefault(); event.stopPropagation();
                if (this.activeIndex === null || this.activeIndex <= 0) { this.activeIndex = this.options.length - 1; } 
                else { this.activeIndex--; }
            } else if (event.key === 'Enter' && this.activeIndex !== null) {
                event.preventDefault(); event.stopPropagation();
                let option = this.options[this.activeIndex];
                if(option) this.select(option.value);
            } else if (event.key === 'Home') {
                this.activeIndex = 0;
            } else if (event.key === 'End') {
                this.activeIndex = this.options.length - 1;
            }
        },
        
        getFilteredIndex(value) { return this.options.findIndex(option => option.value == value); },
        handleMouseEnter(value) { this.activeIndex = this.getFilteredIndex(value); },
        isFocused(value) { return this.activeIndex !== null && this.getFilteredIndex(value) === this.activeIndex; },
        
        get label() {
            if (!this.hasSelection) return this.placeholder;

            if (!this.isMultiple) {
                if (this.selectedItem) return this.selectedItem.label;
                const source = this.isAsync ? this.options : this.staticOptions;
                const opt = source.find(o => o.value == this.state);
                return opt?.label ?? this.placeholder;
            }

            if (this.state.length === 1) {
                const source = this.isAsync ? this.options : this.staticOptions;
                const opt = source.find(o => o.value == this.state[0]);
                return opt?.label ?? this.state[0];
            }
            return `${this.state.length} items selected`;
        },
        
        get hasSelection() { return this.isMultiple ? this.state?.length > 0 : (this.state !== null && this.state !== ''); },
        isSelected(value) { return this.isMultiple ? this.state?.includes(value) : this.state == value; },
        contains: (str, sub) => String(str).toLowerCase().includes(String(sub).toLowerCase()),
    }"
    {{ $attributes->class([
        'relative',
        'dark:border-red-400! dark:shadow-red-400 text-red-400! placeholder:text-red-400!' => $invalid,
    ]) }}
>
    @if ($name)
        <input type="hidden" name="{{ $name }}" x-bind:value="isMultiple ? state.join(',') : state"/>
    @endif

    <div>
        <x-ui.select.trigger/>
        <x-ui.select.options 
            :checkIconClass="$checkIconClass"
            :checkIcon="$checkIcon"
        >
            {{ $slot }}
        </x-ui.select.options>
    </div>
</div>