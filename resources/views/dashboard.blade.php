<x-layout>
    <x-dashboard title="Dashboard" summary="Quick overview of everything">
        Welcome, {{ auth()->user()->name }}
    </x-dashboard>
</x-layout>