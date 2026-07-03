import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Echo / Pusher example (uncomment and configure if using realtime)
try {
	// prefer ESM-style import when using Vite; otherwise ensure packages are installed
	// npm install --save laravel-echo pusher-js
	import Echo from 'laravel-echo';
	window.Pusher = (await import('pusher-js')).default;

	if (typeof process !== 'undefined' && (process.env.MIX_PUSHER_APP_KEY || import.meta.env.VITE_PUSHER_APP_KEY)) {
		window.Echo = new Echo({
			broadcaster: 'pusher',
			key: import.meta.env.VITE_PUSHER_APP_KEY || process.env.MIX_PUSHER_APP_KEY,
			cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || process.env.MIX_PUSHER_APP_CLUSTER,
			forceTLS: true,
			encrypted: true,
		});
	}
} catch (e) {
	// No-op: dev environment may not have pusher/echo installed
}
