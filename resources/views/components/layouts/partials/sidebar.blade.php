<x-ui.sidebar class="w-[280px] h-dvh">
    <x-slot:brand>
        <x-ui.brand  
            name="Zakulike"
            href="/test"
        >
            <x-slot:logo>
                    <svg xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 100 100"
                    class="size-5">
                    <rect x="15" y="10" width="80" height="15" fill="currentColor" rx="5" ry="0" />
                    <rect x="15" y="30" width="60" height="15" fill="currentColor" />
                    <rect x="15" y="50" width="30" height="15" fill="currentColor" />
                    <rect x="15" y="55" width="10" height="30" fill="currentColor" />
                </svg>
            </x-slot:logo>
        </x-ui.brand>
    </x-slot:brand>

    <x-ui.navlist>
        <x-ui.navlist.item 
            label="Dashboard"
            icon="home"
            href="/dashboard"
        />
        <x-ui.navlist.item 
            label="Settings"
            icon="cog"
            href="/settings"
        />
    </x-ui.navlist>
</x-ui.sidebar>