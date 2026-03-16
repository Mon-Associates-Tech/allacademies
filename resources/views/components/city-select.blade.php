@props([
    'idPrefix' => 'loc',
    'name' => 'city',
    'value' => '',
    'wireModel' => null,
    'required' => false,
    'label' => 'City/Town',
    'allowOther' => true,
])

@php
    $req = $required ? 'required' : '';
    $cityId = $idPrefix.'-city';
    $cityCustomId = $idPrefix.'-city-custom';
@endphp

<div data-city-select="true"
     data-prefix="{{ $idPrefix }}"
     data-value="{{ $value }}">
    <label for="{{ $cityId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
        {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
    </label>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
        </div>
        <select id="{{ $cityId }}" name="{{ $name }}" {{ $req }} disabled wire:ignore
                class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus-border-blue-400 transition-colors disabled:bg-gray-100 dark:disabled:bg-gray-600">
            <option value="">Select city</option>
        </select>
    </div>
    <input type="hidden" data-city-value @if($wireModel) wire:model.live="{{ $wireModel }}" @endif value="{{ $value }}">
    @if($allowOther)
        <div id="{{ $cityCustomId }}" class="mt-2 hidden">
            <input type="text" placeholder="Enter your city/town"
                   class="block w-full px-3 py-3 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400"
                   oninput="document.getElementById('{{ $cityId }}').value='other';">
        </div>
    @endif
</div>

@pushOnce('scripts')
    <script>
        (function initCitySelects() {
            const init = (wrapper) => {
                if (!wrapper || wrapper.dataset.ready) return;
                const prefix = wrapper.dataset.prefix;
                const initial = wrapper.dataset.value || '';
                const select = wrapper.querySelector('select');
                const custom = wrapper.querySelector('#' + prefix + '-city-custom');
                const hiddenValue = wrapper.querySelector('[data-city-value]');
                let currentCountry = null;
                let currentRegion = null;
                wrapper.dataset.ready = 'true';

                const loadCities = (country, region) => {
                    if (!country || !region) {
                        select.innerHTML = '<option value=\"\">Select city</option>';
                        select.disabled = true;
                        if (custom) custom.classList.add('hidden');
                        return;
                    }
                    currentCountry = country;
                    currentRegion = region;
                    fetch(`/api/cities?country=${country}&region=${region}`)
                        .then(r => r.json())
                        .then(cities => {
                            const list = Array.isArray(cities) ? cities : [];
                            select.innerHTML = '<option value=\"\">Select city</option>';
                            list.forEach(city => {
                                const opt = new Option(city, city);
                                if (city === initial) opt.selected = true;
                                select.appendChild(opt);
                            });
                            @if($allowOther)
                            select.appendChild(new Option('Other', 'other'));
                            @endif
                            select.disabled = false;
                            if (hiddenValue) hiddenValue.value = select.value || '';
                            if (select.value === 'other' && custom) custom.classList.remove('hidden');
                        })
                        .catch(err => console.error('cities load error', err));
                };

                document.addEventListener(`${prefix}:region-changed`, (e) => {
                    loadCities(e.detail.country, e.detail.region);
                });

                select.addEventListener('change', () => {
                    if (select.value === 'other') {
                        if (custom) custom.classList.remove('hidden');
                    } else if (custom) {
                        custom.classList.add('hidden');
                    }
                    if (hiddenValue) hiddenValue.value = select.value || '';
                });
            };

            const hydrate = () => document.querySelectorAll('[data-city-select="true"]').forEach(init);
            document.addEventListener('DOMContentLoaded', hydrate);
            setTimeout(hydrate, 0);
            if (window.Livewire && window.Livewire.hook) {
                window.Livewire.hook('message.processed', hydrate);
            }
        })();
    </script>
@endPushOnce
