@props([
    'name' => $attributes->whereStartsWith('wire:model')->first() ?? $attributes->whereStartsWith('x-model')->first(),
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
        options:[],
        
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

        init() {
            const initialState = this.$root?._x_model?.get();
            this.state = initialState;

            // Handle pre-selected values for async components (e.g., on an edit form)
            if (this.isAsync && this.state) {
                fetch(`${this.asyncUrl}/${this.state}`)
                    .then(r => r.json())
                    .then(response => {
                        // ❗ FIX: Unwrap the 'data' key from the single resource response
                        const data = this.isPaginated ? response.data : response; 
                        this.selectedItem = this.mapToOption(data);
                    })
                    .catch(error => console.error('Could not load pre-selected item.', error));
            }
            
            // Handle static options for non-async components
            if (!this.isAsync) {
                this.$nextTick(() => {
                    this.options = Array.from(this.$el.querySelectorAll('[data-slot=option]:not([hidden])'))
                        .map(opt => ({ value: opt.dataset.value, label: opt.dataset.label }));
                });
            }

            // Watch for external changes to the state (e.g., from Livewire)
            this.$watch('state', (value) => { this.$root?._x_model?.set(value); });

            // Watch the search input to trigger API calls
            this.$watch('search', Alpine.debounce((val) => {
                if (!this.isAsync) {
                    // Handle local filtering for non-async
                    this.options = Array.from(this.$el.querySelectorAll('[data-slot=option]:not([hidden])'))
                        .map(opt => ({ value: opt.dataset.value, label: opt.dataset.label }))
                        .filter(opt => this.contains(opt.label, val));
                    return;
                }
                
                // For async, reset pagination and fetch new results
                this.options = [];
                this.nextPageUrl = null;
                this.fetchOptions(val);
            }, 300));
        },

        // Helper to map an API item to our standard {value, label} format
        mapToOption(item) {
            if (!item) return null;
            return { value: item[this.valueKey], label: item[this.labelKey] };
        },
        
        fetchOptions(query) {
            if (!this.asyncUrl) return;
            this.isLoading = true;

            const url = new URL(this.asyncUrl, window.location.origin);
            url.searchParams.set('search', query);

            fetch(url.toString())
                .then(r => r.json())
                .then(response => {
                    const results = this.isPaginated ? response[this.dataKey] : response;
                    this.options = results.map(item => this.mapToOption(item));
                    if (this.isPaginated) {
                        this.nextPageUrl = response.links?.next; // Use links.next for Laravel Resource Collections
                    }
                }).catch(err => console.error(err)).finally(() => { this.isLoading = false; });
        },

        loadMore() {
            if (!this.nextPageUrl || this.isLoadingMore || this.isLoading) return;
            
            this.isLoadingMore = true;
            fetch(this.nextPageUrl)
                .then(r => r.json())
                .then(response => {
                    const newOptions = response[this.dataKey].map(item => this.mapToOption(item));
                    this.options = [...this.options, ...newOptions];
                    this.nextPageUrl = response.links?.next;
                }).catch(err => console.error(err)).finally(() => { this.isLoadingMore = false; });
        },
        
        select(optionValue) {
            this.search = '';
            if (!this.isMultiple) {
                this.open = false;
                this.state = optionValue;
                this.selectedItem = this.options.find(opt => opt.value == optionValue);
            }
            // Multi-select logic would go here
        },
        
        clear() {
            this.state = null;
            this.open = false;
            this.selectedItem = null;
        },

        toggle() {
            if (this.isDisabled) return;
            this.open = !this.open;
            if (this.open && this.isAsync && this.options.length === 0) {
                this.fetchOptions('');
            }
        },

        get label() {
            if (this.hasSelection) {
                if (this.selectedItem) return this.selectedItem.label;
                if (!this.isAsync) {
                    const opt = this.options.find(o => o.value == this.state);
                    if(opt) return opt.label;
                }
            }
            return this.placeholder;
        },
        
        get hasSelection() { return this.state !== null && this.state !== ''; },
        isSelected(value) { return this.state == value; },
        contains: (str, sub) => String(str).toLowerCase().includes(String(sub).toLowerCase()),
    }"
    {{ $attributes->class([
        'relative',
        'dark:border-red-400! dark:shadow-red-400 text-red-400! placeholder:text-red-400!' => $invalid,
    ]) }}
>
    @if ($name)
        <input type="hidden" name="{{ $name }}" x-bind:value="state"/>
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