/**
 * GEMMA - Device Hardware Fingerprinting & Persistent UUID
 * Permet d'identifier de façon unique et permanente un appareil physique (même hors hôpital, même avec IP changeante).
 */
(function () {
    function generateDeviceUUID() {
        var cached = null;
        try {
            cached = localStorage.getItem('gemma_device_uuid');
        } catch (e) {}

        if (cached && typeof cached === 'string' && cached.startsWith('DEV-')) {
            ensureCookie(cached);
            return cached;
        }

        try {
            var screenInfo = (window.screen.width || 0) + 'x' + (window.screen.height || 0) + '@' + (window.screen.colorDepth || 24);
            var cores = navigator.hardwareConcurrency || 4;
            var timezone = 'UTC';
            try {
                if (window.Intl && Intl.DateTimeFormat) {
                    timezone = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
                }
            } catch (e) {}

            var ua = navigator.userAgent || '';
            var osPrefix = 'WIN';
            if (/windows/i.test(ua)) osPrefix = 'WIN';
            else if (/macintosh|mac os/i.test(ua)) osPrefix = 'MAC';
            else if (/android/i.test(ua)) osPrefix = 'AND';
            else if (/iphone|ipad/i.test(ua)) osPrefix = 'IOS';
            else if (/linux/i.test(ua)) osPrefix = 'LNX';

            // Canvas 2D fingerprint
            var canvasHash = 0;
            try {
                var canvas = document.createElement('canvas');
                canvas.width = 200;
                canvas.height = 40;
                var ctx = canvas.getContext('2d');
                if (ctx) {
                    ctx.textBaseline = 'top';
                    ctx.font = '14px Arial';
                    ctx.fillStyle = '#f60';
                    ctx.fillRect(125, 1, 62, 20);
                    ctx.fillStyle = '#069';
                    ctx.fillText('GEMMA_DEVICE_ID', 2, 15);
                    ctx.fillStyle = 'rgba(102, 204, 0, 0.7)';
                    ctx.fillText('GEMMA_DEVICE_ID', 4, 17);
                    var dataUrl = canvas.toDataURL();
                    for (var i = 0; i < dataUrl.length; i++) {
                        canvasHash = ((canvasHash << 5) - canvasHash + dataUrl.charCodeAt(i)) & 0xffffffff;
                    }
                }
            } catch (e) {
                canvasHash = 987654;
            }

            // WebGL GPU info
            var gpu = '';
            try {
                var glCanvas = document.createElement('canvas');
                var gl = glCanvas.getContext('webgl') || glCanvas.getContext('experimental-webgl');
                if (gl) {
                    var debugInfo = gl.getExtension('WEBGL_debug_renderer_info');
                    if (debugInfo) {
                        gpu = gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL) || '';
                    }
                }
            } catch (e) {}

            var rawStr = [screenInfo, cores, timezone, osPrefix, canvasHash, gpu, navigator.language || ''].join('###');

            var hash1 = 5381;
            var hash2 = 0;
            for (var j = 0; j < rawStr.length; j++) {
                var charCode = rawStr.charCodeAt(j);
                hash1 = ((hash1 << 5) + hash1) ^ charCode;
                hash2 = ((hash2 << 7) + hash2) ^ charCode;
            }
            var hex1 = (hash1 >>> 0).toString(16).toUpperCase().padStart(8, '0').slice(-4);
            var hex2 = (hash2 >>> 0).toString(16).toUpperCase().padStart(8, '0').slice(-4);

            var uuid = 'DEV-' + osPrefix + '-' + hex1 + '-' + hex2;
            try {
                localStorage.setItem('gemma_device_uuid', uuid);
            } catch (e) {}
            ensureCookie(uuid);
            return uuid;
        } catch (err) {
            var fallback = 'DEV-GEN-' + Math.random().toString(36).substring(2, 10).toUpperCase();
            try {
                localStorage.setItem('gemma_device_uuid', fallback);
            } catch (e) {}
            ensureCookie(fallback);
            return fallback;
        }
    }

    function ensureCookie(uuid) {
        try {
            document.cookie = 'gemma_device_uuid=' + encodeURIComponent(uuid) + '; path=/; max-age=31536000; SameSite=Lax';
        } catch (e) {}
    }

    var devUuid = generateDeviceUUID();

    function setupAjax() {
        if (window.jQuery && window.jQuery.ajaxSetup) {
            window.jQuery.ajaxSetup({
                headers: {
                    'X-Client-Device-UUID': devUuid
                }
            });
        }
    }

    if (window.jQuery) {
        setupAjax();
    } else {
        document.addEventListener('DOMContentLoaded', setupAjax);
    }
})();
