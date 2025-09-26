import '../css/app.css';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import { initializeTheme } from './hooks/use-appearance';
import { QueryProvider } from './components/providers/QueryProvider';
import { Toaster } from 'react-hot-toast';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => title ? `${title} - ${appName}` : appName,
    resolve: (name) => resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(
            <QueryProvider>
                <App {...props} />
                <Toaster
                    position="top-center"
                    reverseOrder={false}
                    gutter={8}
                    containerClassName=""
                    containerStyle={{}}
                    toastOptions={{
                        // Define default options
                        className: '',
                        duration: 4000,
                        style: {
                            background: '#363636',
                            color: '#fff',
                        },
                        // Default options for specific types
                        success: {
                            duration: 3000,
                            style: {
                                background: '#10b981',
                                color: '#fff',
                            },
                        },
                        error: {
                            duration: 4000,
                            style: {
                                background: '#ef4444',
                                color: '#fff',
                            },
                        },
                    }}
                />
            </QueryProvider>
        );
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on load...
initializeTheme();
