import type { App } from 'vue';

/** Recreate Vue roots on Turbo restores instead of leaving a cached empty root. */
export function mountModule(id: string, create: () => App): void {
    let app: App | null = null;
    let root: HTMLElement | null = null;
    const unmount = () => { app?.unmount(); app = null; root = null; };
    const mount = () => {
        const next = document.getElementById(id);
        if (next && next === root && app) return;
        unmount();
        if (!next) return;
        root = next;
        app = create();
        app.mount(next);
    };
    document.addEventListener('turbo:before-cache', unmount);
    document.addEventListener('turbo:before-render', unmount);
    document.addEventListener('turbo:load', mount);
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', mount, { once: true });
    else mount();
}
