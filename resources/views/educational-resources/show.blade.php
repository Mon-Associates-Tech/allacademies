<x-layouts.app :has-action="false" :page-name="$resource->title">
    <div class="container mx-auto px-4 py-6">
        <livewire:resources.resource-viewer :resource="$resource" />
    </div>
</x-layouts.app>
