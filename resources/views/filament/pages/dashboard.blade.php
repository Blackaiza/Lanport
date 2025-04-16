<x-filament::page>
    <div class="space-y-6">
        @foreach ($this->getWidgets() as $widget)
            <livewire:dynamic-component :is="$widget" :key="$widget" />
        @endforeach
    </div>
</x-filament::page>
