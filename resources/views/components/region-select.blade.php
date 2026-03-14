@props([
    'idPrefix' => 'loc',
    'name' => 'region',
    'value' => '',
    'wireModel' => null,
    'required' => false,
    'label' => 'Region/State',
    'allowOther' => true,
])

@php
    $req = $required ? 'required' : '';
    $regionId = $idPrefix.'-region';
    $regionCustomId = $idPrefix.'-region-custom';
@endphp

<div data-region-select="true"
     data-prefix="{{ $idPrefix }}"
     data-value="{{ $value }}">
    <label for="{{ $regionId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
        {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
    </label>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <select id="{{ $regionId }}" name="{{ $name }}" {{ $req }} disabled wire:ignore
                class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors disabled:bg-gray-100 dark:disabled:bg-gray-600">
            <option value="">Select region</option>
        </select>
    </div>
    <input type="hidden" data-region-value @if($wireModel) wire:model.live="{{ $wireModel }}" @endif value="{{ $value }}">
    @if($allowOther)
        <div id="{{ $regionCustomId }}" class="mt-2 hidden">
            <input type="text" placeholder="Enter your region/state"
                   class="block w-full px-3 py-3 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400"
                   oninput="document.getElementById('{{ $regionId }}').value='other';">
        </div>
    @endif
</div>

@pushOnce('scripts')
    <script>
        (function initRegionSelects() {
            const init = (wrapper) => {
                if (!wrapper || wrapper.dataset.ready) return;
                const prefix = wrapper.dataset.prefix;
                const initial = wrapper.dataset.value || '';
                const select = wrapper.querySelector('select');
                const custom = wrapper.querySelector('#' + prefix + '-region-custom');
                const hiddenValue = wrapper.querySelector('[data-region-value]');
                let currentCountry = null;
                wrapper.dataset.ready = 'true';

                const loadRegions = (countryCode) => {
                    if (!countryCode) {
                        select.innerHTML = '<option value=\"\">Select region</option>';
                        select.disabled = true;
                        if (custom) custom.classList.add('hidden');
                        return;
                    }
                    currentCountry = countryCode;
                    fetch(`/api/regions?country=${countryCode}`)
                        .then(r => r.json())
                        .then(regions => {
                            const list = Array.isArray(regions) ? regions : [];
                            select.innerHTML = '<option value=\"\">Select region</option>';
                            list.forEach(region => {
                                const opt = new Option(region, region);
                                if (region === initial) opt.selected = true;
                                select.appendChild(opt);
                            });
                            @if($allowOther)
                            select.appendChild(new Option('Other', 'other'));
                            @endif
                            select.disabled = false;
                            if (hiddenValue) hiddenValue.value = select.value || '';
                            if (select.value && select.value !== 'other') {
                                document.dispatchEvent(new CustomEvent(`${prefix}:region-changed`, {
                                    detail: {country: countryCode, region: select.value}
                                }));
                            } else if (select.value === 'other' && custom) {
                                custom.classList.remove('hidden');
                            }
                        })
                        .catch(err => console.error('regions load error', err));
                };

                document.addEventListener(`${prefix}:country-changed`, (e) => {
                    loadRegions(e.detail.country);
                });

                select.addEventListener('change', () => {
                    if (select.value === 'other') {
                        if (custom) custom.classList.remove('hidden');
                    } else {
                        if (custom) custom.classList.add('hidden');
                        if (hiddenValue) hiddenValue.value = select.value || '';
                        document.dispatchEvent(new CustomEvent(`${prefix}:region-changed`, {
                            detail: {country: currentCountry, region: select.value}
                        }));
                    }
                });
            };

            const hydrate = () => document.querySelectorAll('[data-region-select="true"]').forEach(init);
            document.addEventListener('DOMContentLoaded', hydrate);
            setTimeout(hydrate, 0);
            if (window.Livewire && window.Livewire.hook) {
                window.Livewire.hook('message.processed', hydrate);
            }
        })();
    </script>
@endPushOnce
