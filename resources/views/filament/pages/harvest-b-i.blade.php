<x-filament-panels::page>


<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach ($widgets as $widget)
        @livewire($widget)
    @endforeach
</div>

</x-filament-panels::page>
