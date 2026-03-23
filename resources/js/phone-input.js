import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/build/css/intlTelInput.css';

document.addEventListener('DOMContentLoaded', function() {
    const phoneInput = document.querySelector("#phone");
    const countryCodeInput = document.querySelector("#country_code");

    if (phoneInput) {
        const iti = intlTelInput(phoneInput, {
            initialCountry: "auto",
            geoIpLookup: function(callback) {
                // Use Laravel API endpoint for IP-based country detection
                fetch("/api/detect-country")
                    .then(function(res) { return res.json(); })
                    .then(function(data) { callback(data.country_code); })
                    .catch(function() { callback("ng"); });
            },
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.11/build/js/utils.js",
            separateDialCode: true,
            preferredCountries: ["ng", "ke", "za", "gh", "ug", "tz", "eg", "us", "gb"],
            autoPlaceholder: "aggressive",
            formatOnDisplay: true,
            nationalMode: true, // Allow users to enter national format with leading 0
        });

        // Update hidden country code field when country changes
        phoneInput.addEventListener('countrychange', function() {
            const selectedCountryData = iti.getSelectedCountryData();
            countryCodeInput.value = '+' + selectedCountryData.dialCode;
        });

        // Set initial country code
        const selectedCountryData = iti.getSelectedCountryData();
        if (selectedCountryData && selectedCountryData.dialCode) {
            countryCodeInput.value = '+' + selectedCountryData.dialCode;
        }

        // Auto-format and clean the number as user types
        phoneInput.addEventListener('input', function() {
            let value = phoneInput.value.trim();

            // Remove any non-digit characters except leading +
            value = value.replace(/[^\d+]/g, '');

            // If user pastes a number with country code, extract just the national part
            if (value.startsWith('+')) {
                const selectedCountryData = iti.getSelectedCountryData();
                const dialCode = selectedCountryData.dialCode;
                if (value.startsWith('+' + dialCode)) {
                    value = value.substring(('+' + dialCode).length);
                    // For Nigeria and similar countries, add the leading 0 back
                    if (!value.startsWith('0') && ['ng', 'za', 'gh', 'ke'].includes(selectedCountryData.iso2)) {
                        value = '0' + value;
                    }
                    phoneInput.value = value;
                }
            }
        });

        // Update country code on form submit
        const form = phoneInput.closest('form');
        if (form) {
            form.addEventListener('submit', function() {
                if (phoneInput.value.trim()) {
                    const selectedCountryData = iti.getSelectedCountryData();
                    countryCodeInput.value = '+' + selectedCountryData.dialCode;

                    // Store the properly formatted international number
                    // The library will handle removing the leading 0 for international format
                    const fullNumber = iti.getNumber();
                    console.log('Submitting number:', fullNumber);
                }
            });
        }

        // Optional: Show a helpful hint based on selected country
        phoneInput.addEventListener('countrychange', function() {
            const selectedCountryData = iti.getSelectedCountryData();
            const label = document.querySelector('label[for="phone"]');

            if (label) {
                const hintSpan = label.querySelector('.phone-hint') || document.createElement('span');
                hintSpan.className = 'phone-hint text-xs text-gray-500 ml-2';

                // Add country-specific hints
                if (selectedCountryData.iso2 === 'ng') {
                    hintSpan.textContent = '(e.g., 0812 345 6789)';
                } else if (selectedCountryData.iso2 === 'gh') {
                    hintSpan.textContent = '(e.g., 0501 234 567)';
                } else if (selectedCountryData.iso2 === 'ke') {
                    hintSpan.textContent = '(e.g., 0712 345 678)';
                } else if (selectedCountryData.iso2 === 'za') {
                    hintSpan.textContent = '(e.g., 071 234 5678)';
                } else {
                    hintSpan.textContent = '';
                }

                if (!label.contains(hintSpan) && hintSpan.textContent) {
                    label.appendChild(hintSpan);
                }
            }
        });

        // Trigger hint display on load
        phoneInput.dispatchEvent(new Event('countrychange'));
    }
});
