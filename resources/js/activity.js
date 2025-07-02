import axios from 'axios';

let timeout;

// Optional: Configure Axios to include credentials (important for session-based auth)
axios.defaults.withCredentials = true;

function ping() {
    axios.post('/ping')
        .then(response => {

        })
        .catch(error => {
            console.error('Ping failed', error);
        });
}

['mousemove', 'keydown', 'click'].forEach(evt => {
    window.addEventListener(evt, () => {
        clearTimeout(timeout);
        timeout = setTimeout(ping, 10000); // ping every 10s after activity
    });
});
