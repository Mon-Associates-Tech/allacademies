import {Calendar} from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

window.FullCalendar = {
    Calendar: Calendar,
    plugins: {
        dayGrid: dayGridPlugin,
        timeGrid: timeGridPlugin,
        interaction: interactionPlugin
    }
};
