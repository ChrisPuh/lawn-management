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
                primary: {
                    50: "#E6F5EC",
                    100: "#C3E6D2",
                    200: "#9AE6B4", // lawn-accent
                    300: "#6BC288",
                    400: "#48BB78", // lawn-secondary
                    500: "#2F855A", // lawn-primary
                    600: "#276749",
                    700: "#1F4D37",
                    800: "#183426",
                    900: "#111B15",
                },

                // Naturfarben
                nature: {
                    soil: {
                        light: "#A67B5B",
                        DEFAULT: "#8B4513", // soil
                        dark: "#613010",
                    },
                    sand: {
                        light: "#FFF3B4",
                        DEFAULT: "#F6E05E", // sand
                        dark: "#B7A642",
                    },
                    grass: {
                        dead: "#BFA979",
                        dry: "#D4CB98",
                        healthy: "#48BB78",
                    },
                },

                // Feedback & Status Farben
                success: {
                    light: "#C6F6D5",
                    DEFAULT: "#48BB78",
                    dark: "#2F855A",
                },
                warning: {
                    light: "#FEFCBF",
                    DEFAULT: "#ECC94B",
                    dark: "#B7791F",
                },
                error: {
                    light: "#FED7D7",
                    DEFAULT: "#F56565",
                    dark: "#C53030",
                },
                info: {
                    light: "#BEE3F8",
                    DEFAULT: "#4299E1",
                    dark: "#2B6CB0",
                },

                // UI Graustufen
                gray: {
                    50: "#F9FAFB",
                    100: "#F3F4F6",
                    200: "#E5E7EB",
                    300: "#D1D5DB",
                    400: "#9CA3AF",
                    500: "#6B7280",
                    600: "#4B5563",
                    700: "#374151",
                    800: "#1F2937",
                    900: "#111827",
                },

                // Background & Surface Farben
                background: {
                    light: "#FFFFFF",
                    DEFAULT: "#F9FAFB",
                    dark: "#F3F4F6",
                },
                surface: {
                    light: "#FFFFFF",
                    DEFAULT: "#FFFFFF",
                    dark: "#F9FAFB",
                },
            },
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/typography"),
    ],
};
