import '../../node_modules/foliate-js/view.js';

const reader = document.querySelector('#book-ebook-reader');

if (reader) {
    const content = document.querySelector('#reader-content');
    const status = document.querySelector('#reader-status');
    const error = document.querySelector('#reader-error');
    const progressLabel = document.querySelector('#reader-progress-label');
    const previousButton = document.querySelector('#reader-previous');
    const nextButton = document.querySelector('#reader-next');
    let pageCount = Number(reader.dataset.totalPages) || 0;
    const initialProgress = Math.max(0, Math.min(1, Number(reader.dataset.initialProgress) || 0));
    const view = document.createElement('foliate-view');
    let saveProgressTimeout;
    let lastSavedPage = 0;

    view.setAttribute('flow', 'paginated');
    view.style.display = 'block';
    view.style.width = '100%';
    view.style.height = '100%';
    content.append(view);

    previousButton.addEventListener('click', () => view.prev().catch(() => {}));
    nextButton.addEventListener('click', () => view.next().catch(() => {}));

    view.addEventListener('relocate', event => {
        const fraction = Number(event.detail?.fraction);
        if (!Number.isFinite(fraction)) {
            return;
        }

        if (pageCount < 1) {
            pageCount = Math.max(1, Number(event.detail?.location?.total) || 1);
        }

        const boundedFraction = Math.max(0, Math.min(1, fraction));
        const percentage = Math.round(boundedFraction * 100);
        const currentPage = Math.max(1, Math.min(pageCount, Math.ceil(boundedFraction * pageCount)));
        progressLabel.textContent = `${percentage}% read`;

        if (currentPage === lastSavedPage) {
            return;
        }

        clearTimeout(saveProgressTimeout);
        saveProgressTimeout = setTimeout(async () => {
            try {
                const response = await fetch(reader.dataset.progressUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    },
                    body: JSON.stringify({
                        book_id: Number(reader.dataset.bookId),
                        current_page: currentPage,
                        total_pages: pageCount,
                    }),
                });

                if (response.ok) {
                    lastSavedPage = currentPage;
                }
            } catch {
                // Reading remains available if progress cannot be saved.
            }
        }, 800);
    });

    view.addEventListener('external-link', event => {
        let url;
        try {
            url = new URL(event.detail.href, window.location.href);
        } catch {
            event.preventDefault();
            return;
        }

        event.preventDefault();
        if (url.protocol === 'https:' || url.protocol === 'http:') {
            window.open(url.href, '_blank', 'noopener,noreferrer');
        }
    });

    document.addEventListener('keydown', event => {
        if (event.altKey || event.ctrlKey || event.metaKey || event.target instanceof HTMLInputElement) {
            return;
        }

        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            view.prev().catch(() => {});
        } else if (event.key === 'ArrowRight') {
            event.preventDefault();
            view.next().catch(() => {});
        }
    });

    const openReader = async () => {
        try {
            await view.open(reader.dataset.streamUrl);
            if (initialProgress > 0) {
                await view.goToFraction(initialProgress);
            }
            status.hidden = true;
        } catch (openError) {
            console.error('Unable to open ebook:', openError);
            status.hidden = true;
            error.hidden = false;
        }
    };

    openReader();
}