import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/admin-user-create.js",
                "resources/js/auth/otp-common.js",
                "resources/js/admin/group-member-selector.js",
                "resources/js/layout/logout-handler.js",
            ],
            refresh: true,
        }),
    ],
});
