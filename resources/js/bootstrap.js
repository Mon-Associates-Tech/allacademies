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
import './fullcalendar';
import 'aos/dist/aos.css'
import * as AOS from 'aos'
// Import chart data helper
import './activity'; // Import activity tracking
import renderMathInElement from 'katex/dist/contrib/auto-render';

import './pdf_reader_wrapper'
import './book_pdf_annotation_viewer'
import videojs from 'video.js';
import 'video.js/dist/video-js.css';
import 'videojs-markers';
import 'videojs-contrib-quality-levels';
import 'videojs-http-source-selector';
import './modal'
import './upload_progress'
import DOMPurify from 'dompurify';
import './phone-input';
window.DOMPurify = DOMPurify;

// resources/js/app.js (or a dedicated entry point)
import './proctoring'; // If proctoring.js is a module that attaches to window
import './exam-heartbeat'; // If exam-heartbeat.js is a module that attaches to window
import './echo';
// Register Chart.js components (added RadialLinearScale for radar charts)
Chart.register(CategoryScale, LinearScale, BarElement, LineElement, PointElement, ArcElement, Title, Tooltip, Legend, RadialLinearScale);

// Expose to window for global access
window.Alpine = Alpine;
window.marked = marked;
window.Chart = Chart;
window.axios = axios;
window.AOS = AOS;
window.videojs = videojs;
import '../css/videoplayer.css'


import './ChartDataHelper';
// Charts helpers (reusable components support)
// Important: use dynamic imports so these execute AFTER window.Chart is attached
Promise.resolve()
  .then(() => import('./charts/helpers'))
  .then(() => import('./charts/gauge'))
  .catch((e) => console.warn('Chart helpers load error:', e));
import './calendar';
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
window.katex = katex;
window.renderMathInElement = renderMathInElement;

// Safe markdown and math rendering function
window.renderMarkdownWithMath = function(content) {
    if (!content) return '';

    try {
        // Sanitize input to prevent XSS
        const sanitizedContent = DOMPurify ? DOMPurify.sanitize(content) : content;

        // Parse markdown with marked
        let htmlContent = marked.parse(sanitizedContent);

        // Create temporary element for math rendering
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = htmlContent;

        // Apply math rendering if available
        if (typeof renderMathInElement !== 'undefined') {
            renderMathInElement(tempDiv, {
                delimiters: [
                    {left: '$$', right: '$$', display: true},
                    {left: '$', right: '$', display: false},
                    {left: '\\[', right: '\\]', display: true},
                    {left: '\\(', right: '\\)', display: false}
                ],
                throwOnError: false,
                errorColor: '#cc0000',
                strict: false,
                trust: false
            });
        }

        return tempDiv.innerHTML;
    } catch (e) {
        console.warn('Markdown/Math rendering error:', e);
        return content; // Fallback to plain text
    }
};



Livewire.start();

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';
