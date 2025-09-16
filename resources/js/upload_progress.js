// Add this to your layout or component
document.addEventListener('livewire:init', () => {
    // Track upload progress for file inputs
    Livewire.on('upload:start', (event) => {
        console.log('Upload started:', event);
        // You can add custom logic here
    });

    Livewire.on('upload:progress', (event) => {
        console.log('Upload progress:', event);
        // Update progress bars with actual percentages
        const progressBars = document.querySelectorAll(`[data-upload="${event.name}"] .progress-bar`);
        progressBars.forEach(bar => {
            bar.style.width = event.progress + '%';
        });
    });

    Livewire.on('upload:finish', (event) => {
        console.log('Upload finished:', event);
        // You can add completion logic here
    });

    Livewire.on('upload:error', (event) => {
        console.log('Upload error:', event);
        // Handle upload errors
    });
});

// Alternative: Custom file upload handler with real progress
function setupCustomUploadProgress() {
    const fileInputs = document.querySelectorAll('input[type="file"][wire\\:model]');

    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Show progress immediately
            const progressContainer = this.closest('.upload-container')?.querySelector('.upload-progress');
            if (progressContainer) {
                progressContainer.classList.remove('hidden');
            }

            // Simulate real upload progress (you'd replace this with actual upload)
            let progress = 0;
            const progressBar = progressContainer?.querySelector('.progress-bar');
            const progressText = progressContainer?.querySelector('.progress-text');

            const interval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress > 100) {
                    progress = 100;
                    clearInterval(interval);

                    // Show completion state
                    setTimeout(() => {
                        progressContainer.classList.add('hidden');
                        const successContainer = this.closest('.upload-container')?.querySelector('.upload-success');
                        if (successContainer) {
                            successContainer.classList.remove('hidden');
                        }
                    }, 500);
                }

                if (progressBar) {
                    progressBar.style.width = progress + '%';
                }
                if (progressText) {
                    progressText.textContent = Math.round(progress) + '%';
                }
            }, 200);
        });
    });
}
