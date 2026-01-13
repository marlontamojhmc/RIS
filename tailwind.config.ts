// tailwind.config.ts

import type { Config } from 'tailwindcss'; // Import the Config type for better type-checking

/** @type {Config} */
const config: Config = {
    content: [
        // This array tells Tailwind where to look for class names
        // Ensure this covers your Vue component paths
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],

    // 💡 THE ESSENTIAL FIX: SAFELIST
    // This tells Tailwind to always include these specific classes 
    // because they are generated dynamically in your JS (statusClass function).
    // tailwind.config.ts

    safelist: [
        // Include both regular and !important versions
        'bg-blue-100', '!bg-blue-100', 'text-blue-700', '!text-blue-700',
        'bg-yellow-100', '!bg-yellow-100', 'text-yellow-700', '!text-yellow-700',
        'bg-green-100', '!bg-green-100', 'text-green-700', '!text-green-700',
        // ... repeat for all colors ...
    ],

    theme: {
        extend: {},
    },
    plugins: [],
};

export default config;