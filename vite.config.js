import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        // Allow requests from Herd/Valet served domains
        cors: true,
        host: true,
        // Ensure HMR works when the app is served via a domain (e.g. Herd *.test)
        // You can override via env: VITE_DEV_HOST and VITE_DEV_CLIENT_PORT
        hmr: (() => {
            const appUrl = process.env.APP_URL || '';
            let derivedHost = undefined;
            let protocol = 'ws';
            let clientPort = undefined;

            try {
                if (appUrl) {
                    const url = new URL(appUrl);
                    derivedHost = url.hostname;
                    if (url.protocol === 'https:') {
                        protocol = 'wss';
                        clientPort = 443;
                    }
                }
            } catch (_) {
                // ignore malformed APP_URL
            }

            return {
                host: process.env.VITE_DEV_HOST || derivedHost || 'localhost',
                protocol,
                clientPort: process.env.VITE_DEV_CLIENT_PORT
                    ? Number(process.env.VITE_DEV_CLIENT_PORT)
                    : clientPort,
            };
        })(),
        // Helps generated URLs point to the correct origin when proxied
        origin: process.env.APP_URL || undefined,
    },
});