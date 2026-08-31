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
import axios from 'axios';
import './fullcalendar';
import 'aos/dist/aos.css'
import * as AOS from 'aos'
// Import chart data helper
import './activity'; // Import activity tracking


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
import './marked'; // Ensure this is imported after DOMPurify and marked are available


// resources/js/app.js (or a dedicated entry point)
import './proctoring'; // If proctoring.js is a module that attaches to window
import './exam-heartbeat'; // If exam-heartbeat.js is a module that attaches to window
import './echo';
// Register Chart.js components (added RadialLinearScale for radar charts)
Chart.register(CategoryScale, LinearScale, BarElement, LineElement, PointElement, ArcElement, Title, Tooltip, Legend, RadialLinearScale);

// Expose to window for global access
window.Alpine = Alpine;
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



/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */


Livewire.start();



