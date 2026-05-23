/* ════════════════════════════════════════
   খেদমত সেন্টার — Service Worker v1.0
   PWA Offline + Push Notification Support
   ════════════════════════════════════════ */

var CACHE_NAME = 'khedmot-v1';
var OFFLINE_URL = './';

/* ══ Install: Cache শেল ফাইলগুলো ══ */
self.addEventListener('install', function(e) {
  e.waitUntil(
    caches.open(CACHE_NAME).then(function(cache) {
      return cache.addAll([
        './',
        './index.html',
        './hajera.png',
        './manifest.json'
      ]);
    })
  );
  self.skipWaiting();
});

/* ══ Activate: পুরনো Cache মুছো ══ */
self.addEventListener('activate', function(e) {
  e.waitUntil(
    caches.keys().then(function(keys) {
      return Promise.all(
        keys.filter(function(k) { return k !== CACHE_NAME; })
            .map(function(k) { return caches.delete(k); })
      );
    })
  );
  self.clients.claim();
});

/* ══ Fetch: Cache first, network fallback ══ */
self.addEventListener('fetch', function(e) {
  /* Firebase ও external API — সরাসরি network */
  if (e.request.url.includes('firestore') ||
      e.request.url.includes('firebase') ||
      e.request.url.includes('googleapis') ||
      e.request.url.includes('gstatic')) {
    return;
  }
  e.respondWith(
    caches.match(e.request).then(function(cached) {
      if (cached) return cached;
      return fetch(e.request).then(function(response) {
        if (!response || response.status !== 200) return response;
        var clone = response.clone();
        caches.open(CACHE_NAME).then(function(cache) {
          cache.put(e.request, clone);
        });
        return response;
      }).catch(function() {
        return caches.match(OFFLINE_URL);
      });
    })
  );
});

/* ══ Push Notification Receive ══ */
self.addEventListener('push', function(e) {
  var data = {};
  try { data = e.data ? e.data.json() : {}; } catch(err) { data = { title: 'নতুন পোস্ট', body: e.data ? e.data.text() : 'খেদমত সেন্টারে নতুন পোস্ট এসেছে' }; }

  var title = data.title || 'খেদমত সেন্টার';
  var options = {
    body: data.body || 'নতুন নিয়োগ বিজ্ঞপ্তি এসেছে! এখনই দেখুন।',
    icon: './hajera.png',
    badge: './hajera.png',
    tag: data.tag || 'new-post',
    renotify: true,
    vibrate: [200, 100, 200],
    data: { url: data.url || './' }
  };
  e.waitUntil(self.registration.showNotification(title, options));
});

/* ══ Notification Click ══ */
self.addEventListener('notificationclick', function(e) {
  e.notification.close();
  var url = (e.notification.data && e.notification.data.url) ? e.notification.data.url : './';
  e.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(list) {
      for (var i = 0; i < list.length; i++) {
        if (list[i].url.includes('khedmot') || list[i].url.includes('localhost') || list[i].url.includes('github.io')) {
          list[i].focus();
          return;
        }
      }
      return clients.openWindow(url);
    })
  );
});

/* ══ Background Sync (নতুন পোস্ট চেক) ══ */
self.addEventListener('sync', function(e) {
  if (e.tag === 'check-new-posts') {
    /* background sync — client handle করবে */
  }
});
