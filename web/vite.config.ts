import { fileURLToPath, URL } from 'url';
import { defineConfig, loadEnv } from 'vite'
import vue from '@vitejs/plugin-vue';
import vuetify from 'vite-plugin-vuetify';

// const env = loadEnv(mode, process.cwd(), '');
// https://vitejs.dev/config/
export default defineConfig({
    // define: {
    //     'process.env.SOME_KEY': JSON.stringify(env.SOME_KEY)
    // },
    plugins: [
        vue(),
        vuetify({
            autoImport: true,
            styles: { configFile: 'src/scss/variables.scss' }
        })
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url))
        }
    },
    css: {
        preprocessorOptions: {
            scss: {}
        }
    },
    optimizeDeps: {
        exclude: ['vuetify'],
        entries: ['./src/**/*.vue']
    }
});
