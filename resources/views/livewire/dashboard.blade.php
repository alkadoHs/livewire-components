<section class="flex">
    <div class="space-y-6 px-4">
        <div class="flex gap-6">
            <x-ui.card
                size="xl"
                class="mx-auto"
            >
                <x-ui.heading class="flex items-center justify-between mb-4" level="h3" size="sm">
                    Welcome to Sheaf UI
                    <x-ui.link href="https://sheafui.dev" openInNewTab>
                        <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
                    </x-ui.link>
                </x-ui.heading>
                <x-ui.text>
                    Powered by the TALL stack, our components offer speed, elegance,
                    and accessibility for modern web development.
                </x-ui.text>
            </x-ui.card>
        
            <x-ui.card
                size="xl"
                class="mx-auto"
            >
                <x-ui.heading class="flex items-center justify-between mb-4" level="h3" size="sm">
                    Start reading about SheafUI
                    <x-ui.link href="https://sheafui.dev/docs/guides/overview" openInNewTab>
                        <x-ui.icon name="arrow-up-right" class="text-gray-800 dark:text-white size-4" />
                    </x-ui.link>
                </x-ui.heading>
                <x-ui.text>
                    Our comprehensive documentation is AI-powered, clear, and consistent. Discover our copy-paste philosophy, learn about component architecture, and explore integration patterns.
                </x-ui.text>
            </x-ui.card>
        </div>

        <form wire:submit="saveUsers">
            <x-ui.select 
                wire:model="selectedUsers"
                placeholder="Search for a user..."
                searchable
                async-search
                async-url="/users"
                multiple
                async-paginated
                value-key="id"
                label-key="name"
                async-data-key="data"
            />

           <div>
            <x-ui.button type="submit"> Save </x-ui.button> 
           </div>
        </form>


    </div>

    <script type="module">
        const users = axios.get('/users').then(res => {
            console.log(res.data)
        }).catch( error => {
            console.log(error)
        });
    </script>
</section>