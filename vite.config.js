import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';

export default ({ mode}) => {
    process.env = { ...process.env, ...loadEnv(mode, process.cwd())};

return defineConfig({
    base: process.env.VITE_BASE_URL,
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url)),
        },
    }
});
};
