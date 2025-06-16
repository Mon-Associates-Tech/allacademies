
const fs = require('fs');
const path = require('path');

const workerSrc = path.resolve(__dirname, '../node_modules/pdfjs-dist/build/pdf.worker.js');
const workerDest = path.resolve(__dirname, '../public/js/pdf.worker.js');

// Create directory if it doesn't exist
if (!fs.existsSync(path.dirname(workerDest))) {
    fs.mkdirSync(path.dirname(workerDest), { recursive: true });
}

// Copy the file
fs.copyFileSync(workerSrc, workerDest);
