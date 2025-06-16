import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

// Make FullCalendar available globally
window.FullCalendar = {
    Calendar: Calendar,
    plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin]
};
