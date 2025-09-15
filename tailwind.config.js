import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            colors: {
                "primary-blue": "#1a759f", // Contoh warna biru elegan
                "secondary-green": "#34a0a4", // Contoh warna hijau elegan
                "light-blue": "#6aa6bf",
                "dark-blue": "#16628b",
                "accent-green": "#52b69a",
            },
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
