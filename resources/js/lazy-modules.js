const modules = {
    dashboard: () => import('./pages/dashboard'),
    analytics: () => import('./pages/analytics'),
};

export function loadLazyModules() {
    const page = document.body.dataset.page;
    if (! page) {
        return;
    }

    const loader = modules[page];
    if (typeof loader === 'function') {
        loader().catch((error) => console.error('Lazy module load failed', error));
    }
}
