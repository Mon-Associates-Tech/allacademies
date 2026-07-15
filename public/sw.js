// Examination Hub Service Worker
// Provides offline support and caching for exam taking

const CACHE_NAME = 'exam-hub-v1';
const OFFLINE_PAGE = '/offline.html';

// Resources to cache immediately on install
const STATIC_ASSETS = [
    '/',
    '/examinations/exams/join',
    '/css/exam-dark-mode.css',
    '/js/app.js',
    '/android-chrome-192x192.png',
    '/android-chrome-512x512.png',
    '/apple-touch-icon.png',
];

// Exam-specific routes that should be cached with network-first strategy
const EXAM_ROUTES = [
    '/examinations/exams/',
    '/examinations/take/',
];

// Install event - cache static assets
self.addEventListener('install', (event) => {
    console.log('[Service Worker] Installing...');
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[Service Worker] Caching static assets');
            return cache.addAll(STATIC_ASSETS).catch((error) => {
                console.error('[Service Worker] Failed to cache:', error);
            });
        })
    );
    self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('[Service Worker] Activating...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME) {
                        console.log('[Service Worker] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch event - implement caching strategies
self.addEventListener('fetch', (event) => {
    const url = new URL(event.request.url);
    
    // Skip non-GET requests
    if (event.request.method !== 'GET') {
        return;
    }
    
    // Skip chrome-extension and other non-http(s) requests
    if (!url.protocol.startsWith('http')) {
        return;
    }
    
    // Strategy for exam routes - Network First, fallback to cache
    if (isExamRoute(url.pathname)) {
        event.respondWith(networkFirstStrategy(event.request));
        return;
    }
    
    // Strategy for static assets - Cache First, fallback to network
    if (isStaticAsset(url.pathname)) {
        event.respondWith(cacheFirstStrategy(event.request));
        return;
    }
    
    // Default strategy - Stale While Revalidate
    event.respondWith(staleWhileRevalidateStrategy(event.request));
});

// Network First Strategy (for dynamic content like exams)
async function networkFirstStrategy(request) {
    try {
        const networkResponse = await fetch(request);
        
        // If successful, cache the response
        if (networkResponse.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.log('[Service Worker] Network failed, trying cache:', error);
        
        // Try to get from cache
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // If not in cache, return offline page
        return caches.match(OFFLINE_PAGE);
    }
}

// Cache First Strategy (for static assets)
async function cacheFirstStrategy(request) {
    const cachedResponse = await caches.match(request);
    
    if (cachedResponse) {
        // Update cache in background
        fetch(request).then((networkResponse) => {
            if (networkResponse.ok) {
                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(request, networkResponse);
                });
            }
        }).catch(() => {
            // Network request failed, ignore
        });
        
        return cachedResponse;
    }
    
    // Not in cache, fetch from network
    try {
        const networkResponse = await fetch(request);
        if (networkResponse.ok) {
            const cache = await caches.open(CACHE_NAME);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch (error) {
        console.error('[Service Worker] Fetch failed:', error);
        return new Response('Offline', { status: 503, statusText: 'Service Unavailable' });
    }
}

// Stale While Revalidate Strategy (default)
async function staleWhileRevalidateStrategy(request) {
    const cache = await caches.open(CACHE_NAME);
    const cachedResponse = await cache.match(request);
    
    const fetchPromise = fetch(request).then((networkResponse) => {
        if (networkResponse.ok) {
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    }).catch(() => {
        // Network request failed
        return null;
    });
    
    return cachedResponse || fetchPromise || caches.match(OFFLINE_PAGE);
}

// Helper functions
function isExamRoute(pathname) {
    return EXAM_ROUTES.some(route => pathname.includes(route));
}

function isStaticAsset(pathname) {
    return STATIC_ASSETS.some(asset => pathname === asset || pathname.endsWith('.css') || pathname.endsWith('.js'));
}

// Background sync for offline form submissions
self.addEventListener('sync', (event) => {
    if (event.tag === 'save-exam-response') {
        event.waitUntil(savePendingResponses());
    }
});

async function savePendingResponses() {
    // This would sync pending responses saved while offline
    console.log('[Service Worker] Syncing pending responses...');
    // Implementation would depend on how responses are queued
}

// Push notifications (for exam reminders, results ready, etc.)
self.addEventListener('push', (event) => {
    if (!event.data) {
        return;
    }
    
    const data = event.data.json();
    const options = {
        body: data.body,
        icon: '/android-chrome-192x192.png',
        badge: '/android-chrome-192x192.png',
        vibrate: [200, 100, 200],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: data.primaryKey,
        },
        actions: [
            {
                action: 'view',
                title: 'View Details',
            },
            {
                action: 'dismiss',
                title: 'Dismiss',
            },
        ],
    };
    
    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Notification click handler
self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    
    if (event.action === 'view') {
        event.waitUntil(
            clients.openWindow('/examinations/results')
        );
    }
});

// Message handler for communication with main thread
self.addEventListener('message', (event) => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
    
    if (event.data && event.data.type === 'CACHE_EXAM_DATA') {
        // Cache specific exam data for offline access
        event.waitUntil(
            caches.open(CACHE_NAME).then((cache) => {
                return cache.addAll(event.data.urls);
            })
        );
    }
});

console.log('[Service Worker] Loaded successfully');
