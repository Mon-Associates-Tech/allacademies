(function () {
    'use strict';

    var MARKER = 'data-aa-restricted';
    var applying = false;

    var SELECTOR = [
        '#printButton',
        '#downloadButton',
        '#secondaryPrint',
        '#secondaryDownload',
        '#print',
        '#download',
        '.toolbarButton[data-l10n-id="print"]',
        '.toolbarButton[data-l10n-id="download"]',
        '.toolbarButton[data-l10n-id="pdfjs-print-button"]',
        '.toolbarButton[data-l10n-id="pdfjs-download-button"]',
        '.secondaryToolbarButton[data-l10n-id="secondaryPrint"]',
        '.secondaryToolbarButton[data-l10n-id="secondaryDownload"]',
        '.secondaryToolbarButton[data-l10n-id="pdfjs-secondary-print-button"]',
        '.secondaryToolbarButton[data-l10n-id="pdfjs-secondary-download-button"]'
    ].join(',');

    function restrictButton(button) {
        if (button.hasAttribute(MARKER)) {
            return;
        }
        button.setAttribute(MARKER, '1');

        // Force hide with inline styles
        button.style.cssText += 'display:none !important; visibility:hidden !important; pointer-events:none !important; opacity:0 !important;';
        
        button.disabled = true;
        button.hidden = true;
        button.setAttribute('tabindex', '-1');
        button.setAttribute('aria-hidden', 'true');

        button.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
        }, true);
    }

    function disablePdfJsActions() {
        if (applying) {
            return;
        }
        applying = true;
        try {
            var buttons = document.querySelectorAll(SELECTOR);
            for (var i = 0; i < buttons.length; i++) {
                restrictButton(buttons[i]);
            }
        } finally {
            applying = false;
        }
    }

    function blockShortcuts() {
        window.addEventListener('keydown', function (event) {
            var key = String(event.key || '').toLowerCase();
            if ((event.ctrlKey || event.metaKey) && (key === 'p' || key === 's')) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        }, true);

        document.addEventListener('contextmenu', function (event) {
            event.preventDefault();
        }, true);
    }

    function boot() {
        disablePdfJsActions();
        blockShortcuts();

        var observer = new MutationObserver(function () {
            disablePdfJsActions();
        });

        observer.observe(document.documentElement, {
            childList: true,
            subtree: true
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }

    window.addEventListener('webviewerloaded', function () {
        disablePdfJsActions();

        if (window.PDFViewerApplication && window.PDFViewerApplication.initializedPromise) {
            window.PDFViewerApplication.initializedPromise
                .then(disablePdfJsActions)
                .catch(function () {});
        }
    });
})();