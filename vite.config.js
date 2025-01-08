import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/navigation/navigation.js",
                "resources/js/animate3d/main.js",
                "resources/js/animate3d/validar.js",
                "resources/js/animate3d/firmar.js",
            ],
            refresh: true,
        }),
    ],
});
