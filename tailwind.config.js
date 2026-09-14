import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    darkMode: ['class', '[data-theme="dark"]'],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"IBM Plex Sans"', ...defaultTheme.fontFamily.sans],
                serif: ['"Fraunces"', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                main: 'var(--bg-main)',
                panel: 'var(--bg-panel)',
                ink: 'var(--text-ink)',
                muted: 'var(--text-muted)',
                faint: 'var(--text-faint)',
                border: 'var(--border-color)',
                accent: {
                    DEFAULT: 'var(--accent-main)',
                    soft: 'var(--accent-soft)',
                    warm: 'var(--accent-warm)',
                },
                status: {
                    pending: {
                        bg: 'var(--status-pending-bg)',
                        text: 'var(--status-pending-text)',
                    },
                    process: {
                        bg: 'var(--status-process-bg)',
                        text: 'var(--status-process-text)',
                    },
                    done: {
                        bg: 'var(--status-done-bg)',
                        text: 'var(--status-done-text)',
                    },
                    cancel: {
                        bg: 'var(--status-cancel-bg)',
                        text: 'var(--status-cancel-text)',
                    },
                }
            },
        },
    },

    plugins: [forms],
};
