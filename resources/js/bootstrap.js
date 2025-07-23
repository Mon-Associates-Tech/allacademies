import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import {
  Chart,
  CategoryScale,
  LinearScale,
  BarElement,
  LineElement,
  PointElement,
  ArcElement,
  Title,
  Tooltip,
  Legend,
  RadialLinearScale
} from 'chart.js/auto';
import { marked } from "marked";
import katex from "katex";
import axios from 'axios';
import './fullcalendar'
// Import chart data helper
import './activity'; // Import activity tracking



// Register Chart.js components (added RadialLinearScale for radar charts)
Chart.register(CategoryScale, LinearScale, BarElement, LineElement, PointElement, ArcElement, Title, Tooltip, Legend, RadialLinearScale);

// Expose to window for global access
window.Alpine = Alpine;
window.marked = marked;
window.Chart = Chart;
window.axios = axios;


import './ChartDataHelper';
// Configure axios
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Configure marked with proper LaTeX support
const renderer = new marked.Renderer();
const originalCodespan = renderer.codespan;
const originalCode = renderer.code;

renderer.codespan = function(code) {
    if (code.charAt(0) === '$' && code.charAt(code.length - 1) === '$') {
        try {
            return katex.renderToString(code.slice(1, -1), {
                throwOnError: false
            });
        } catch (e) {
            return originalCodespan.call(this, code);
        }
    }
    return originalCodespan.call(this, code);
};

renderer.code = function(code, lang, escaped) {
    if (code.charAt(0) === '$' && code.charAt(code.length - 1) === '$') {
        try {
            return katex.renderToString(code.slice(1, -1), {
                throwOnError: false,
                displayMode: true
            });
        } catch (e) {
            return originalCode.call(this, code, lang, escaped);
        }
    }
    return originalCode.call(this, code, lang, escaped);
};

marked.setOptions({ renderer });

Livewire.start();
