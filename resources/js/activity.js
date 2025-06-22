// public/js/activity.js
let timeout;
function ping() {
    fetch('/ping', {method: 'POST', headers: {'X-CSRF-TOKEN': window.csrf_token}});
}
['mousemove', 'keydown', 'click'].forEach(evt =>
    window.addEventListener(evt, () => {
        clearTimeout(timeout);
        timeout = setTimeout(ping, 10000); // ping every 10s after activity
    })
);
