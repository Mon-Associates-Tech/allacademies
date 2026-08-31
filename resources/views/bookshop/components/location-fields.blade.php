@props([
    'idPrefix' => 'location',
    'countryValue' => '',
    'countryCodeValue' => '',
    'regionValue' => '',
    'cityValue' => '',
    'required' => false,
])

{{--
    Thin wrapper around the project's shared <x-location-selector>.
    Scopes BookShop's visual language (border-radius: 2px, purple accent,
    slate palette) onto just this instance via CSS overrides, rather than
    editing location-selector.blade.php directly — that component is used
    elsewhere in the project (student forms, etc.) with its own rounded-xl
    / blue-accent styling, and changing it there would ripple everywhere.
--}}
<div class="bookshop-location-fields">
    <x-location-selector
        :id-prefix="$idPrefix"
        :country-value="$countryValue"
        :country-code-value="$countryCodeValue"
        :region-value="$regionValue"
        :city-value="$cityValue"
        :required="$required"
    />
</div>

@once
    <style>
        .bookshop-location-fields label {
            display: block;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b; /* slate-500 */
            margin-bottom: 0.5rem;
        }

        .bookshop-location-fields select,
        .bookshop-location-fields input[type="text"] {
            width: 100% !important;
            padding: 0.625rem 1rem 0.625rem 2.5rem !important; /* keep left space for the icon */
            font-size: 0.875rem !important;
            border: 1px solid #e2e8f0 !important; /* slate-200 */
            border-radius: 2px !important;
            background-color: #fff !important;
            color: #0f172a !important; /* slate-900 */
            box-shadow: none !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .bookshop-location-fields select:focus,
        .bookshop-location-fields input[type="text"]:focus {
            outline: none !important;
            border-color: #7c3aed !important; /* purple-500 */
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.2) !important;
        }

        .bookshop-location-fields select:disabled {
            background-color: #f1f5f9 !important; /* slate-100 */
            opacity: 1 !important;
        }

        .bookshop-location-fields svg {
            color: #94a3b8 !important; /* slate-400, matches the rest of the module's icon tone */
        }

        /*
         * This module's layouts load Tailwind via the CDN script
         * (<script src="https://cdn.tailwindcss.com">), which defaults to
         * the "media" dark-mode strategy unless explicitly configured
         * otherwise — meaning every dark: utility elsewhere in BookShop
         * responds to prefers-color-scheme, NOT a `.dark` class on the
         * page. A `.dark ...` selector here would never match, since
         * nothing in this standalone shell ever adds that class. Using
         * the same media query keeps this override consistent with how
         * the rest of the module's dark mode actually behaves.
         */
        @media (prefers-color-scheme: dark) {
            .bookshop-location-fields label {
                color: #94a3b8; /* slate-400 */
            }
            .bookshop-location-fields select,
            .bookshop-location-fields input[type="text"] {
                background-color: #1e293b !important; /* slate-800 */
                border-color: #334155 !important; /* slate-700 */
                color: #fff !important;
            }
            .bookshop-location-fields select:disabled {
                background-color: #334155 !important; /* slate-700 */
            }
        }
    </style>
@endonce
