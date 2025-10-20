<x-slot:title>
    {{ $title ?? 'Sheaf UI' }}
</x-slot:title>

<x-layouts.base>
    <x-layouts.partials.nav />

    <div class="max-w-7xl mx-auto mt-20">
        {{ $slot }}
    </div>
</x-layouts.base>
