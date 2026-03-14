@props([
    'idPrefix' => 'loc',
    'name' => 'country',
    'codeName' => 'country_code',
    'value' => '',
    'codeValue' => '',
    'wireModel' => null,
    'wireCode' => null,
    'required' => false,
    'label' => 'Country',
])

@php
    $req = $required ? 'required' : '';
    $countryId = $idPrefix.'-country';
    $codeId = $idPrefix.'-country-code';
@endphp

<div data-country-select="true"
     data-prefix="{{ $idPrefix }}"
     data-value="{{ $value }}"
     data-code="{{ $codeValue }}">
    <label for="{{ $countryId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
        {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
    </label>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <select id="{{ $countryId }}" name="{{ $name }}" {{ $req }} wire:ignore
                class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors">
            <option value="">Select country</option>
        </select>
    </div>
    <input type="hidden" id="{{ $countryId }}-value" data-country-value
           @if($wireModel) wire:model.live="{{ $wireModel }}" @endif value="{{ $value }}">
    <input type="hidden" id="{{ $codeId }}" name="{{ $codeName }}" data-country-code value="{{ $codeValue }}"
           @if($wireCode) wire:model.live="{{ $wireCode }}" @endif>
</div>

@pushOnce('scripts')
    <script>
        (function initCountrySelects() {
            const init = (wrapper) => {
                if (!wrapper || wrapper.dataset.ready) return;
                wrapper.dataset.ready = 'true';
                const prefix = wrapper.dataset.prefix;
                const initial = wrapper.dataset.value || '';
                const initialCode = wrapper.dataset.code || '';
                const select = wrapper.querySelector('select');
                const hiddenValue = wrapper.querySelector('[data-country-value]');
                const hiddenCode = wrapper.querySelector('[data-country-code]');

                fetch('/api/countries', {headers: {'Accept': 'application/json'}})
                    .then(r => r.json())
                    .then(data => {
                        select.innerHTML = '<option value=\"\">Select country</option>';
                        const current = (hiddenValue?.value || hiddenCode?.value || select.value || initialCode || initial || '').toString();
                        const append = (code, name) => {
                            if (!code || !name) return;
                            const opt = new Option(name, code);
                            if (code.toString() === current) opt.selected = true;
                            select.appendChild(opt);
                        };
                        const source = data && typeof data === 'object' ? data : {};
                        if (Array.isArray(source)) {
                            source.forEach(item => {
                                if (typeof item === 'string') append(item, item);
                                else append(item.code || item.iso2 || item.id || item.value, item.name || item.label || item.title);
                            });
                        } else {
                            Object.entries(source).forEach(([code, name]) => append(code, name));
                        }
                        // set hidden code and notify dependents
                        // ensure select reflects desired value even if it wasn't matched during creation
                        if (current && select.value !== current) {
                            select.value = current;
                        }
                        if (hiddenValue) hiddenValue.value = select.value || '';
                        if (hiddenCode) hiddenCode.value = select.value || '';
                        const event = new CustomEvent(`${prefix}:country-changed`, {detail: {country: select.value, countryCode: select.value}});
                        document.dispatchEvent(event);
                    })
                    .catch(err => {
                        console.error('countries load error', err);
                        select.innerHTML = '<option value=\"\">Select country</option>';
                    });

                select.addEventListener('change', () => {
                    if (hiddenValue) hiddenValue.value = select.value || '';
                    if (hiddenCode) hiddenCode.value = select.value || '';
                    document.dispatchEvent(new CustomEvent(`${prefix}:country-changed`, {
                        detail: {country: select.value, countryCode: select.value}
                    }));
                });
            };

            const hydrate = () => document.querySelectorAll('[data-country-select="true"]').forEach(init);
            document.addEventListener('DOMContentLoaded', hydrate);
            // Alpine/Livewire script stacks may add the node later; run on next tick too
            setTimeout(hydrate, 0);
            if (window.Livewire && window.Livewire.hook) {
                window.Livewire.hook('message.processed', hydrate);
            }
        })();
    </script>
@endPushOnce
