(function () {
    'use strict';

    var ORIGIN = window.location.origin;
    var app = null;
    var known = new Map();      // dbId -> annotation (overlay boxes only)
    var drawMode = false;
    var lastSnapshot = '';

    function post(type, payload) {
        window.parent.postMessage(Object.assign({ source: 'aa-bridge', type: type }, payload || {}), ORIGIN);
    }

    function rgbToHex(c) {
        if (!Array.isArray(c)) return null;
        return '#' + c.slice(0, 3).map(function (v) {
            return Math.max(0, Math.min(255, Math.round(v))).toString(16).padStart(2, '0');
        }).join('');
    }

    function editorSource(el) {
        if (el.classList.contains('highlightEditor')) return 'highlight';
        if (el.classList.contains('inkEditor')) return 'ink';
        if (el.classList.contains('freeTextEditor')) return 'free_text';
        if (el.classList.contains('stampEditor')) return 'stamp';
        return 'highlight';
    }

    function pageDiv(num) {
        return document.querySelector('.page[data-page-number="' + num + '"]');
    }

    function layerFor(num) {
        var page = pageDiv(num);
        if (!page) return null;
        var layer = page.querySelector(':scope > .aaLayer');
        if (!layer) {
            layer = document.createElement('div');
            layer.className = 'aaLayer';
            layer.style.cssText = 'position:absolute;inset:0;z-index:6;pointer-events:none;';
            page.appendChild(layer);
        }
        return layer;
    }

    function renderOverlays(num) {
        var layer = layerFor(num);
        if (!layer) return;
        layer.querySelectorAll('.aaBox').forEach(function (el) { el.remove(); });

        known.forEach(function (a) {
            if (a.external_id) return;               // native editors render themselves
            if (Number(a.page_number) !== Number(num)) return;

            var box = document.createElement('div');
            box.className = 'aaBox';
            box.style.cssText =
                'position:absolute;pointer-events:auto;cursor:pointer;box-sizing:border-box;' +
                'border:2px solid ' + a.color + ';background:' + a.color + '33;' +
                'left:' + a.x_pct + '%;top:' + a.y_pct + '%;' +
                'width:' + a.width_pct + '%;height:' + a.height_pct + '%;';
            if (a.resolved_at) box.style.opacity = '0.35';

            box.addEventListener('click', function (ev) {
                ev.stopPropagation();
                post('annotation-selected', { id: a.id });
            });

            layer.appendChild(box);
        });
    }

    function renderAllOverlays() {
        if (!app) return;
        for (var p = 1; p <= app.pagesCount; p++) renderOverlays(p);
    }

    function collectEditors() {
        var storage = app && app.pdfDocument ? app.pdfDocument.annotationStorage : null;
        var serializable = (storage && storage.serializable) || {};
        var editors = [];

        document.querySelectorAll('.annotationEditorLayer .annotationEditor').forEach(function (el) {
            var pageEl = el.closest('.page');
            var layerEl = el.parentElement;
            if (!pageEl || !layerEl) return;

            var lr = layerEl.getBoundingClientRect();
            var er = el.getBoundingClientRect();
            if (!lr.width || !lr.height || !er.width || !er.height) return;

            var id = el.id || '';
            var data = serializable[id] || {};

            editors.push({
                external_id: id,
                page: Number(pageEl.getAttribute('data-page-number')),
                x_pct: Math.max(0, Math.min(100, ((er.left - lr.left) / lr.width) * 100)),
                y_pct: Math.max(0, Math.min(100, ((er.top - lr.top) / lr.height) * 100)),
                width_pct: Math.max(0, Math.min(100, (er.width / lr.width) * 100)),
                height_pct: Math.max(0, Math.min(100, (er.height / lr.height) * 100)),
                color: rgbToHex(data.color) || '#f59e0b',
                source: editorSource(el),
            });
        });

        return editors;
    }

    function pushEditorsIfChanged() {
        var editors = collectEditors();
        var snapshot = JSON.stringify(editors);
        if (snapshot === lastSnapshot) return;
        lastSnapshot = snapshot;
        post('editors-changed', { editors: editors });
    }

    function enableDrawCapture(on) {
        document.querySelectorAll('.aaLayer').forEach(function (layer) {
            layer.style.pointerEvents = on ? 'auto' : 'none';
            layer.style.cursor = on ? 'crosshair' : '';
            layer.style.zIndex = on ? '50' : '6';
        });
    }

    function bindDrawHandlers() {
        var start = null;
        var temp = null;

        document.addEventListener('mousedown', function (ev) {
            if (!drawMode || !ev.target.classList.contains('aaLayer')) return;
            var layer = ev.target;
            var lr = layer.getBoundingClientRect();
            start = { layer: layer, lr: lr, x: ev.clientX - lr.left, y: ev.clientY - lr.top };

            temp = document.createElement('div');
            temp.style.cssText = 'position:absolute;border:2px dashed #f59e0b;background:#f59e0b33;pointer-events:none;';
            layer.appendChild(temp);
            ev.preventDefault();
        }, true);

        document.addEventListener('mousemove', function (ev) {
            if (!start || !temp) return;
            var x = Math.max(0, Math.min(ev.clientX - start.lr.left, start.lr.width));
            var y = Math.max(0, Math.min(ev.clientY - start.lr.top, start.lr.height));
            var left = Math.min(start.x, x), top = Math.min(start.y, y);
            var w = Math.abs(x - start.x), h = Math.abs(y - start.y);
            temp.style.left = (left / start.lr.width) * 100 + '%';
            temp.style.top = (top / start.lr.height) * 100 + '%';
            temp.style.width = (w / start.lr.width) * 100 + '%';
            temp.style.height = (h / start.lr.height) * 100 + '%';
            temp.dataset.w = w; temp.dataset.h = h;
        }, true);

        document.addEventListener('mouseup', function () {
            if (!start || !temp) return;
            var w = Number(temp.dataset.w || 0), h = Number(temp.dataset.h || 0);
            var lr = start.lr;
            var pageEl = start.layer.closest('.page');
            temp.remove();

            if (w > 8 && h > 8 && pageEl) {
                var left = parseFloat(temp.style.left) || 0;
                // recompute from stored px via percentages set above
                post('rect-created', {
                    page: Number(pageEl.getAttribute('data-page-number')),
                    x_pct: (Math.min(start.x, start.x + w) / lr.width) * 100,
                    y_pct: (Math.min(start.y, start.y + h) / lr.height) * 100,
                    width_pct: (w / lr.width) * 100,
                    height_pct: (h / lr.height) * 100,
                    color: '#f59e0b',
                });
            }

            start = null; temp = null;
        }, true);
    }

    function boot() {
        app = window.PDFViewerApplication;

        app.eventBus.on('pagerendered', function (e) {
            renderOverlays(e.pageNumber);
        });

        ['annotationeditorstateschanged', 'annotationeditorselectionchanged', 'annotationeditormodechanged']
            .forEach(function (evt) {
                app.eventBus.on(evt, pushEditorsIfChanged);
            });

        // Click on a native editor -> open its comment thread in parent
        document.addEventListener('click', function (ev) {
            var editorEl = ev.target.closest('.annotationEditor');
            if (!editorEl || !editorEl.id) return;
            post('editor-selected', { external_id: editorEl.id });
        }, true);

        bindDrawHandlers();
        setInterval(pushEditorsIfChanged, 2000);

        window.addEventListener('message', function (ev) {
            if (ev.origin !== ORIGIN || !ev.data || ev.data.source !== 'aa-parent') return;

            if (ev.data.type === 'load-annotations') {
                known = new Map((ev.data.annotations || []).map(function (a) { return [a.id, a]; }));
                renderAllOverlays();
            }

            if (ev.data.type === 'draw-mode') {
                drawMode = !!ev.data.on;
                enableDrawCapture(drawMode);
            }

            if (ev.data.type === 'refresh-overlays') {
                renderAllOverlays();
            }
        });

        post('ready', {});
        pushEditorsIfChanged();
    }

    (function wait() {
        if (window.PDFViewerApplication && window.PDFViewerApplication.initialized) {
            boot();
        } else {
            setTimeout(wait, 150);
        }
    })();
})();