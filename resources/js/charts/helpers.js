// Chart helpers for Alpine + Livewire
// Exposes window.ChartHelpers with create/update/destroy helpers and dark-mode aware defaults

(() => {
  if (!window.Chart) return;

  const baseGridLight = 'rgba(0,0,0,0.08)';
  const baseGridDark = 'rgba(255,255,255,0.12)';
  const baseTickLight = '#374151'; // gray-700
  const baseTickDark = '#9CA3AF';  // gray-400

  function isDark() {
    return document.documentElement.classList.contains('dark');
  }

  function applyBaseDefaults(type, options = {}) {
    const dark = isDark();
    const grid = dark ? baseGridDark : baseGridLight;
    const tick = dark ? baseTickDark : baseTickLight;

    const merged = { ...options };

    if (type === 'bar' || type === 'line') {
      merged.scales = merged.scales || {};
      merged.scales.x = merged.scales.x || {};
      merged.scales.y = merged.scales.y || {};
      merged.scales.x.ticks = { ...(merged.scales.x.ticks || {}), color: tick };
      merged.scales.y.ticks = { ...(merged.scales.y.ticks || {}), color: tick };
      merged.scales.x.grid = { ...(merged.scales.x.grid || {}), color: grid };
      merged.scales.y.grid = { ...(merged.scales.y.grid || {}), color: grid };
    }

    merged.plugins = merged.plugins || {};
    merged.plugins.legend = merged.plugins.legend || {};
    merged.plugins.tooltip = merged.plugins.tooltip || { enabled: true };

    merged.responsive = merged.responsive !== false;
    merged.maintainAspectRatio = merged.maintainAspectRatio === true ? true : false;

    return merged;
  }

  function createChart(canvasEl, type, data, options = {}) {
    const ctx = canvasEl.getContext('2d');
    const cfg = {
      type,
      data,
      options: applyBaseDefaults(type, options),
    };
    return new window.Chart(ctx, cfg);
  }

  function updateChart(chart, data, options = {}) {
    chart.options = applyBaseDefaults(chart.config.type, { ...chart.options, ...options });
    chart.data = data;
    chart.update();
  }

  function destroyChart(chart) {
    if (chart && typeof chart.destroy === 'function') {
      chart.destroy();
    }
  }

  window.ChartHelpers = { createChart, updateChart, destroyChart, applyBaseDefaults };
})();
