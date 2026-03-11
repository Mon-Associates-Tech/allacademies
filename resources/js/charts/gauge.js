// Simple Gauge built on Chart.js Doughnut with center text rendering
// Exposes window.GaugeChart with create/update helpers

(() => {
  if (!window.Chart) return;

  const CenterTextPlugin = {
    id: 'gaugeCenterText',
    afterDraw(chart, args, pluginOptions) {
      const { ctx, chartArea: { width, height } } = chart;
      const meta = chart.$gaugeMeta || {};
      const value = meta.value;
      const min = meta.min ?? 0;
      const max = meta.max ?? 100;
      const showValue = meta.showValue !== false;
      const centerLabel = meta.centerLabel || '';

      const cx = chart.getDatasetMeta(0).data[0]?.x || chart.width / 2;
      const cy = chart.getDatasetMeta(0).data[0]?.y || chart.height / 2;

      ctx.save();
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';

      // Draw value
      if (showValue && typeof value !== 'undefined') {
        ctx.fillStyle = getComputedStyle(document.documentElement).getPropertyValue('--tw-prose-body') || (document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#111827');
        ctx.font = `600 ${Math.max(12, Math.min(width, height) * 0.12)}px ui-sans-serif, system-ui, -apple-system`;
        const pct = max > min ? Math.round(((value - min) / (max - min)) * 100) : 0;
        ctx.fillText(`${pct}%`, cx, cy - 4);
      }

      // Draw label
      if (centerLabel) {
        ctx.fillStyle = document.documentElement.classList.contains('dark') ? '#9CA3AF' : '#6B7280';
        ctx.font = `400 ${Math.max(10, Math.min(width, height) * 0.05)}px ui-sans-serif, system-ui, -apple-system`;
        ctx.fillText(centerLabel, cx, cy + 16);
      }

      ctx.restore();
    }
  };

  if (!window.Chart.registry.plugins.get('gaugeCenterText')) {
    window.Chart.register(CenterTextPlugin);
  }

  function create(canvasEl, payload) {
    const data = payload.data;
    const options = Object.assign({
      rotation: payload.options?.rotation ?? -Math.PI,
      circumference: payload.options?.circumference ?? Math.PI,
      cutout: payload.options?.cutout ?? '70%',
      plugins: Object.assign({ legend: { display: false }, tooltip: { enabled: false } }, payload.options?.plugins || {})
    }, payload.options || {});

    const chart = window.ChartHelpers.createChart(canvasEl, 'doughnut', data, options);
    chart.$gaugeMeta = payload.meta || {};
    return chart;
  }

  function update(chart, payload) {
    window.ChartHelpers.updateChart(chart, payload.data, payload.options || {});
    chart.$gaugeMeta = payload.meta || chart.$gaugeMeta || {};
  }

  window.GaugeChart = { create, update };
})();
