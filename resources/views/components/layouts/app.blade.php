<x-slot:title>
    {{ $title ?? 'Kadolab' }}
</x-slot:title>

<x-layouts.base>
    <x-ui.layout variant="header-sidebar" :collapsible="true">
        <x-ui.layout.header>
            <x-ui.sidebar.toggle class="md:hidden" />
            <x-slot:brand>
                <x-ui.brand name="Kadolab" href="" class="hidden md:block" />
            </x-slot:brand>
            
            <x-ui.navbar class="flex-1">
                <x-ui.navbar.item label="Home" icon="home" />
                <x-ui.navbar.item label="Products" icon="shopping-bag" />
            </x-ui.navbar>
            
            <div class="ml-auto flex items-center gap-4">
                <x-user-dropdown />
                <x-ui.theme-switcher />
            </div>
        </x-ui.layout.header>
        
        <x-ui.sidebar>
            <x-ui.navlist>
                <x-ui.navlist.item label="Dashboard" icon="home" href="/dashboard" />
                <x-ui.navlist.group label="Content">
                    <x-ui.navlist.item label="Posts" icon="document" />
                    <x-ui.navlist.item label="Pages" icon="folder" />
                </x-ui.navlist.group>
            </x-ui.navlist>
        </x-ui.sidebar>

        <x-ui.layout.main>
            <!-- Your page content -->
            <div class="p-6">
                {{ $slot }}
            </div>
        </x-ui.layout.main>
    </x-ui.layout>
</x-layouts.base>
