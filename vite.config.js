import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/sass/app.scss", "resources/js/app.js"],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                api: "modern-compiler",
                quietDeps: true,
                silenceDeprecations: ["legacy-js-api", "import", "mixed-decls"],
            },
        },
    },
});
