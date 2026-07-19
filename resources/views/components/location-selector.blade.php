@props([
    // Unique prefix to avoid ID collisions when component is used multiple times on a page
    'idPrefix' => 'location',
    // Static form names (for non-Livewire usage)
    'countryName' => 'country',
    'regionName' => 'region',
    'cityName' => 'city',
    'countryCodeName' => 'country_code',
    // Initial values (old() or model values)
    'countryValue' => '',
    'regionValue' => '',
    'cityValue' => '',
    'countryCodeValue' => '',
    // Livewire bindings (optional)
    'wireCountry' => null,
    'wireRegion' => null,
    'wireCity' => null,
    'wireCountryCode' => null,
    // Label text
    'showLabels' => true,
    'required' => false,
])

@php
    $reqAttr = $required ? 'required' : '';
    $countryId = $idPrefix.'-country';
    $regionId = $idPrefix.'-region';
    $cityId = $idPrefix.'-city';
    $regionCustomId = $idPrefix.'-region-custom';
    $cityCustomId = $idPrefix.'-city-custom';
@endphp

<div class="space-y-4"
     data-location-selector="true"
     data-prefix="{{ $idPrefix }}"
     data-country-value="{{ $countryValue }}"
     data-region-value="{{ $regionValue }}"
     data-city-value="{{ $cityValue }}"
     data-country-code-value="{{ $countryCodeValue }}">
    <div>
        @if($showLabels)
            <label for="{{ $countryId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">
                Country @if($required)<span class="text-red-500">*</span>@endif
            </label>
        @endif
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <select id="{{ $countryId }}" name="{{ $countryName }}" {{ $reqAttr }}
                    @if($wireCountry) wire:model.live="{{ $wireCountry }}" @endif
                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors">
                <option value="">Select country</option>
            </select>
        </div>
    </div>

    <div>
        @if($showLabels)
            <label for="{{ $regionId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">Region/State</label>
        @endif
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <select id="{{ $regionId }}" name="{{ $regionName }}" {{ $reqAttr }} disabled
                    @if($wireRegion) wire:model.live="{{ $wireRegion }}" @endif
                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors disabled:bg-gray-100 dark:disabled:bg-gray-600">
                <option value="">Select region</option>
            </select>
        </div>
        <div id="{{ $regionCustomId }}" class="mt-2 hidden">
            <input type="text" placeholder="Enter your region/state"
                   class="block w-full px-3 py-3 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400"
                   oninput="document.getElementById('{{ $regionId }}').value='other';">
        </div>
    </div>

    <div>
        @if($showLabels)
            <label for="{{ $cityId }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-2">City</label>
        @endif
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <select id="{{ $cityId }}" name="{{ $cityName }}" {{ $reqAttr }} disabled
                    @if($wireCity) wire:model.live="{{ $wireCity }}" @endif
                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 transition-colors disabled:bg-gray-100 dark:disabled:bg-gray-600">
                <option value="">Select city</option>
            </select>
        </div>
        <div id="{{ $cityCustomId }}" class="mt-2 hidden">
            <input type="text" placeholder="Enter your city/town"
                   class="block w-full px-3 py-3 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400"
                   oninput="document.getElementById('{{ $cityId }}').value='other';">
        </div>
    </div>

    <input type="hidden" name="{{ $countryCodeName }}" id="{{ $idPrefix }}-country-code"
           value="{{ $countryCodeValue }}"
           @if($wireCountryCode) wire:model.live="{{ $wireCountryCode }}" @endif>
</div>

@pushOnce('scripts')
    <script>
        function initLocationSelector(prefix, initialCountry, initialRegion, initialCity, initialCountryCode) {
            const countrySelect = document.getElementById(`${prefix}-country`);
            const regionSelect = document.getElementById(`${prefix}-region`);
            const citySelect = document.getElementById(`${prefix}-city`);
            const regionCustom = document.getElementById(`${prefix}-region-custom`);
            const cityCustom = document.getElementById(`${prefix}-city-custom`);
            const countryCodeInput = document.getElementById(`${prefix}-country-code`);

            if (!countrySelect || countrySelect.dataset.initialized) return;
            countrySelect.dataset.initialized = 'true';

            // Load countries
            fetch('/api/countries')
                .then(res => {
                    console.log('Countries fetch - status:', res.status);
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    return res;
                })
                .then(res => res.text())
                .then(text => {
                    console.log('Raw response text:', text.substring(0, 200));
                    try {
                        const data = JSON.parse(text);
                        console.log('Parsed data:', data);
                        console.log('Data type:', typeof data);
                        console.log('Data is null:', data === null);
                        console.log('Data is undefined:', data === undefined);
                        return data;
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        console.error('Response text:', text);
                        throw new Error('Invalid JSON response: ' + e.message);
                    }
                })
                .then(countries => {
                    console.log('=== START Processing Countries ===');
                    console.log('Value:', countries);
                    console.log('Type:', typeof countries);
                    console.log('IsNull:', countries === null);
                    console.log('IsUndefined:', countries === undefined);

                    countrySelect.innerHTML = '<option value="">Select country</option>';

                    if (countries === null || countries === undefined) {
                        console.error('ERROR: countries is null or undefined!');
                        countrySelect.innerHTML += '<option disabled>No countries data</option>';
                        return;
                    }

                    const appendOption = (code, name) => {
                        if (!code || !name) return;
                        const option = new Option(name, code);
                        if (code === initialCountry) option.selected = true;
                        countrySelect.appendChild(option);
                    };

                    if (Array.isArray(countries)) {
                        console.log('Processing as array with', countries.length, 'items');
                        countries.forEach(item => {
                            if (typeof item === 'string') {
                                appendOption(item, item);
                            } else if (item && typeof item === 'object') {
                                appendOption(item.code || item.iso2 || item.id || item.value, item.name || item.label || item.title);
                            }
                        });
                    } else if (typeof countries === 'object') {
                        console.log('Processing as object');
                        // DEFENSIVE CHECK: Ensure it's safe for Object.entries
                        if (Object(countries) !== countries) {
                            console.error('ERROR: countries is not an object despite typeof check');
                            return;
                        }

                        const keys = Object.keys(countries);
                        console.log('Keys count:', keys.length);
                        console.log('Sample keys:', keys.slice(0, 5));

                        if (keys.length === 0) {
                            console.warn('WARNING: Empty countries object');
                            return;
                        }

                        try {
                            const entries = Object.entries(countries);
                            console.log('Entries count:', entries.length);
                            console.log('Sample entries:', entries.slice(0, 3));
                            entries.forEach(([code, name]) => appendOption(code, name));
                        } catch (error) {
                            console.error('CRITICAL ERROR in Object.entries:', error);
                            console.error('Countries value at error:', countries);
                            console.error('Countries type at error:', typeof countries);
                            console.error('Countries constructor:', countries.constructor);
                            throw error;
                        }
                    } else {
                        console.error('Unexpected type:', typeof countries);
                    }

                    // // Support both {US: "United States"} and [{code:"US", name:"United States"}] shapes
                    // const appendOption = (code, name) => {
                    //     if (!code || !name) return;
                    //     const option = new Option(name, code);
                    //     if (code === initialCountry) option.selected = true;
                    //     countrySelect.appendChild(option);
                    // };

                    if (Array.isArray(countries)) {
                        console.log('Processing countries as array, length:', countries.length);
                        countries.forEach(item => {
                            if (typeof item === 'string') {
                                console.log('Processing string country item:', item);
                                appendOption(item, item);
                            } else if (item && typeof item === 'object') {
                                console.log('Processing object country item:', item);
                                appendOption(item.code || item.iso2 || item.id || item.value, item.name || item.label || item.title);
                            }
                        });
                    } else if (typeof countries === 'object') {
                        console.log('Processing countries as object, keys:', Object.keys(countries));
                        try {
                            const entries = Object.entries(countries);
                            console.log('Entries from countries:', entries);
                            entries.forEach(([code, name]) => {
                                console.log('Processing country entry:', {code, name});
                                appendOption(code, name);
                            });
                        } catch (error) {
                            console.error('Failed to process countries object:', error);
                            console.error('Countries value:', countries);
                            countrySelect.innerHTML += '<option disabled>Error loading countries</option>';
                            return;
                        }
                    } else {
                        console.error('Unexpected countries data type:', typeof countries);
                        countrySelect.innerHTML += '<option disabled>Invalid data format</option>';
                        return;
                    }

                    console.log('Country select populated with options:', countrySelect.options.length);

                    if (countrySelect.value) {
                        countryCodeInput.value = countrySelect.value;
                        loadRegions(countrySelect.value, initialRegion, initialCity);
                    }
                })
                .catch(err => {
                    console.error('❌ Error in countries fetch chain:', err);
                    console.error('Error name:', err.name);
                    console.error('Error message:', err.message);
                    console.error('Error stack:', err.stack);
                    countrySelect.innerHTML = '<option value="">Select country</option><option disabled>Failed to load</option>';
                });

            countrySelect.addEventListener('change', function () {
                countryCodeInput.value = this.value || '';
                if (this.value) {
                    loadRegions(this.value);
                } else {
                    regionSelect.innerHTML = '<option value="">Select region</option>';
                    regionSelect.disabled = true;
                    citySelect.innerHTML = '<option value="">Select city</option>';
                    citySelect.disabled = true;
                    regionCustom.classList.add('hidden');
                    cityCustom.classList.add('hidden');
                }
            });

            regionSelect.addEventListener('change', function () {
                if (this.value === 'other') {
                    regionCustom.classList.remove('hidden');
                    citySelect.disabled = true;
                    citySelect.value = '';
                } else {
                    regionCustom.classList.add('hidden');
                    if (this.value && countrySelect.value) {
                        loadCities(countrySelect.value, this.value);
                    }
                }
            });

            function loadRegions(countryCode, selectedRegion = null, selectedCity = null) {
                console.log('Loading regions for:', countryCode);
                fetch(`/api/regions?country=${countryCode}`)
                    .then(res => {
                        console.log('Regions fetch response status:', res.status);
                        console.log('Regions fetch response ok:', res.ok);
                        if (!res.ok) {
                            throw new Error(`HTTP error! status: ${res.status}`);
                        }
                        return res.json();
                    })
                    .then(regions => {
                        console.log('Regions response:', regions);
                        console.log('Regions type:', typeof regions);
                        console.log('Regions is null?:', regions === null);
                        console.log('Regions is undefined?:', regions === undefined);
                        console.log('Regions isArray?:', Array.isArray(regions));

                        if (regions == null) {
                            console.error('Regions data is null or undefined');
                            return;
                        }

                        if (!Array.isArray(regions)) {
                            console.error('Invalid regions data format - expected array, got:', typeof regions, regions);
                            console.log('Attempting to convert to array if possible...');
                            if (Array.isArray(Object.values(regions))) {
                                regions = Object.values(regions);
                                console.log('Converted regions to array:', regions);
                            } else {
                                console.error('Could not convert regions data to array');
                                return;
                            }
                        }

                        console.log('Regions array length:', regions.length);
                        regionSelect.innerHTML = '<option value="">Select region</option>';
                        regions.forEach((region, index) => {
                            console.log(`Processing region ${index}:`, region);
                            const option = new Option(region, region);
                            if (region === selectedRegion) {
                                option.selected = true;
                                console.log('Selected region matched:', region);
                            }
                            regionSelect.appendChild(option);
                        });
                        regionSelect.appendChild(new Option('Other', 'other'));
                        regionSelect.disabled = false;
                        console.log('Region select now has options:', regionSelect.options.length);

                        if (regionSelect.value && regionSelect.value !== 'other') {
                            loadCities(countryCode, regionSelect.value, selectedCity);
                        }
                    })
                    .catch(err => {
                        console.error('Error loading regions:', err);
                        console.error('Error stack:', err.stack);
                        console.error('Error name:', err.name);
                        console.error('Error message:', err.message);
                    });
            }

            function loadCities(countryCode, regionCode, selectedCity = null) {
                console.log('Loading cities for:', {countryCode, regionCode});
                fetch(`/api/cities?country=${countryCode}&region=${regionCode}`)
                    .then(res => {
                        console.log('Cities fetch response status:', res.status);
                        console.log('Cities fetch response ok:', res.ok);
                        if (!res.ok) {
                            throw new Error(`HTTP error! status: ${res.status}`);
                        }
                        return res.json();
                    })
                    .then(cities => {
                        console.log('Cities response:', cities);
                        console.log('Cities type:', typeof cities);
                        console.log('Cities is null?:', cities === null);
                        console.log('Cities is undefined?:', cities === undefined);
                        console.log('Cities isArray?:', Array.isArray(cities));

                        if (cities == null) {
                            console.error('Cities data is null or undefined');
                            return;
                        }

                        if (!Array.isArray(cities)) {
                            console.error('Invalid cities data format - expected array, got:', typeof cities, cities);
                            console.log('Attempting to convert to array if possible...');
                            if (Array.isArray(Object.values(cities))) {
                                cities = Object.values(cities);
                                console.log('Converted cities to array:', cities);
                            } else {
                                console.error('Could not convert cities data to array');
                                return;
                            }
                        }

                        console.log('Cities array length:', cities.length);
                        citySelect.innerHTML = '<option value="">Select city</option>';
                        cities.forEach((city, index) => {
                            console.log(`Processing city ${index}:`, city);
                            const option = new Option(city, city);
                            if (city === selectedCity) {
                                option.selected = true;
                                console.log('Selected city matched:', city);
                            }
                            citySelect.appendChild(option);
                        });
                        citySelect.appendChild(new Option('Other', 'other'));
                        citySelect.disabled = false;
                        console.log('City select now has options:', citySelect.options.length);
                    })
                    .catch(err => {
                        console.error('Error loading cities:', err);
                        console.error('Error stack:', err.stack);
                        console.error('Error name:', err.name);
                        console.error('Error message:', err.message);
                    });
            }

            citySelect.addEventListener('change', function () {
                if (this.value === 'other') {
                    cityCustom.classList.remove('hidden');
                } else {
                    cityCustom.classList.add('hidden');
                }
            });
        }
    </script>
@endPushOnce

@push('scripts')
    <script>
        (function registerLocationSelector() {
            const runAll = () => {
                console.log('Initializing location selectors...');
                const selectors = document.querySelectorAll('[data-location-selector="true"]');
                console.log('Found selectors:', selectors.length);
                selectors.forEach(el => {
                    try {
                        const prefix = el.dataset.prefix;
                        if (!prefix) {
                            console.warn('Skipping selector without prefix');
                            return;
                        }

                        const countryEl = el.querySelector(`#${prefix}-country`);
                        const country = el.dataset.countryValue || countryEl?.dataset?.value || '';
                        const region = el.dataset.regionValue || '';
                        const city = el.dataset.cityValue || '';
                        const code = el.dataset.countryCodeValue || '';

                        console.log('Initializing selector with prefix:', prefix);
                        console.log('Initial values - country:', country, 'region:', region, 'city:', city, 'code:', code);

                        initLocationSelector(prefix, country, region, city, code);
                    } catch (error) {
                        console.error('Error initializing location selector:', error);
                        console.error('Element:', el);
                    }
                });
            };

            // Initial load
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', runAll);
            } else {
                // DOM already loaded, run immediately
                runAll();
            }

            // Livewire re-renders
            if (window.Livewire && typeof window.Livewire.hook === 'function') {
                window.Livewire.hook('message.processed', runAll);
            }
        })();
    </script>
@endpush
