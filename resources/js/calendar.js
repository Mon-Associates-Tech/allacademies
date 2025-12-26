// Calendar JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle calendar events from Livewire
    window.addEventListener('calendarEventCreated', function() {
        console.log('Calendar event created');
        // Additional handling if needed
    });

    window.addEventListener('calendarEventUpdated', function() {
        console.log('Calendar event updated');
        // Additional handling if needed
    });

    window.addEventListener('calendarEventDeleted', function() {
        console.log('Calendar event deleted');
        // Additional handling if needed
    });

    window.addEventListener('noteCreated', function() {
        console.log('Note created');
        // Additional handling if needed
    });

    // Handle clicks on calendar events
    document.addEventListener('click', function(e) {
        // If clicking on a calendar event element
        if (e.target.closest('.calendar-event')) {
            const eventId = e.target.closest('.calendar-event').dataset.eventId;
            if (eventId) {
                // Dispatch event to Livewire component
                Livewire.dispatch('selectEvent', { eventId: eventId });
            }
        }
    });
    
    // Handle calendar integration checkbox in notes
    const addToCalendarCheckbox = document.getElementById('add_to_calendar');
    if (addToCalendarCheckbox) {
        const calendarFields = document.getElementById('calendar-fields');
        
        addToCalendarCheckbox.addEventListener('change', function() {
            if (this.checked) {
                calendarFields.style.display = 'block';
            } else {
                calendarFields.style.display = 'none';
            }
        });
    }
    
    // Handle calendar integration checkbox in note creation modal
    const addToCalendarNoteCheckbox = document.getElementById('add_to_calendar_note');
    if (addToCalendarNoteCheckbox) {
        const calendarIntegrationFields = document.getElementById('calendar-integration-fields');
        
        addToCalendarNoteCheckbox.addEventListener('change', function() {
            if (this.checked) {
                calendarIntegrationFields.style.display = 'block';
            } else {
                calendarIntegrationFields.style.display = 'none';
            }
        });
    }
});

// Utility functions for calendar operations
const CalendarUtils = {
    formatDate: function(date) {
        return new Date(date).toISOString().slice(0, 16);
    },
    
    parseDate: function(dateString) {
        return new Date(dateString);
    },
    
    isSameDay: function(date1, date2) {
        const d1 = new Date(date1);
        const d2 = new Date(date2);
        return d1.getFullYear() === d2.getFullYear() &&
               d1.getMonth() === d2.getMonth() &&
               d1.getDate() === d2.getDate();
    },
    
    addDays: function(date, days) {
        const result = new Date(date);
        result.setDate(result.getDate() + days);
        return result;
    }
};