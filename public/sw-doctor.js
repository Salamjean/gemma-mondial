const CACHE_NAME = 'gemma-doctor-cache-v18';

self.addEventListener('install', (event) => {
  self.skipWaiting();
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            return caches.delete(cache);
          }
        })
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;
  if (event.request.url.includes('/api/ping') || event.request.url.includes('clients3.google.com')) return;

  event.respondWith(
    fetch(event.request)
      .then((networkResponse) => {
        if (networkResponse && networkResponse.status === 200) {
          const responseToCache = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseToCache);
          });
        }
        return networkResponse;
      })
      .catch(async () => {
        const cachedResponse = await caches.match(event.request);
        if (cachedResponse) {
          return cachedResponse;
        }

        // Si hors-ligne et requête de navigation HTML, chercher un fallback dans le cache
        if (event.request.mode === 'navigate' || (event.request.headers.get('accept') && event.request.headers.get('accept').includes('text/html'))) {
          const cache = await caches.open(CACHE_NAME);
          const keys = await cache.keys();

          if (event.request.url.includes('/formulaire_issue/')) {
            const issueKey = keys.find(k => k.url.includes('/formulaire_issue/'));
            if (issueKey) {
              return await cache.match(issueKey);
            }
          }

          const consultationKey = keys.find(k => k.url.includes('/doctor/consultation/'));
          if (consultationKey) {
            return await cache.match(consultationKey);
          }
        }
      })
  );
});
